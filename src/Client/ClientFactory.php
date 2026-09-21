<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Client;

use DevCraftClub\Shikimori\Config\SdkConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ClientFactory
{
    public static function fromEnv(): ShikimoriGraphQLClient
    {
        return self::fromConfig(SdkConfig::fromEnv());
    }

    public static function fromConfig(
        SdkConfig $config,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?RateLimiterInterface $rateLimiter = null,
        ?CacheItemPoolInterface $cachePool = null
    ): ShikimoriGraphQLClient {
        $httpClient ??= new Client();
        $requestFactory ??= new HttpFactory();
        $streamFactory ??= $requestFactory instanceof StreamFactoryInterface
            ? $requestFactory
            : new HttpFactory();
        $rateLimiter ??= $config->isRateLimitEnabled()
            ? new SlidingWindowRateLimiter($config)
            : new NullRateLimiter();

        return new ShikimoriGraphQLClient(
            $httpClient,
            $requestFactory,
            $streamFactory,
            $config,
            $rateLimiter
        );
    }
}
