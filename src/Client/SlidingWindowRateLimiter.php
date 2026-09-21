<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Client;

use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Exception\RateLimitException;

final class SlidingWindowRateLimiter implements RateLimiterInterface
{
    /** @var list<float> */
    private array $requests = [];

    public function __construct(
        private readonly SdkConfig $config,
        private readonly float $nowProvider = 0.0
    ) {
    }

    public function acquire(): void
    {
        if (!$this->config->isRateLimitEnabled()) {
            return;
        }

        $now = $this->now();
        $this->cleanup($now);

        $minInterval = 1.0 / $this->config->getRateLimitRps();
        $windowSize = 60.0;
        $windowLimit = $this->config->getRateLimitRpm();

        $lastSecondRequests = 0;
        foreach ($this->requests as $timestamp) {
            if ($now - $timestamp < 1.0) {
                ++$lastSecondRequests;
            }
        }

        if ($lastSecondRequests >= $this->config->getRateLimitRps()) {
            $sleep = 1.0 - ($now - $this->requests[\count($this->requests) - $lastSecondRequests]);
            if ($sleep > 0.0) {
                usleep((int) ($sleep * 1_000_000));
            }
        }

        $this->cleanup($this->now());

        if (\count($this->requests) >= $windowLimit) {
            $oldest = $this->requests[0];
            $retryAfter = (int) ceil($oldest + $windowSize - $now);

            throw RateLimitException::exceeded(max(1, $retryAfter));
        }

        $this->requests[] = $this->now();

        $lastTimestamp = $this->requests[array_key_last($this->requests)];
        $elapsed = $now - $lastTimestamp;
        if ($elapsed < $minInterval && $elapsed > 0.0) {
            $sleep = $minInterval - $elapsed;
            usleep((int) ($sleep * 1_000_000));
        }
    }

    private function cleanup(float $now): void
    {
        $windowSize = 60.0;
        $this->requests = array_values(
            array_filter(
                $this->requests,
                static fn (float $timestamp): bool => $now - $timestamp < $windowSize
            )
        );
    }

    private function now(): float
    {
        return $this->nowProvider > 0.0 ? $this->nowProvider : microtime(true);
    }
}
