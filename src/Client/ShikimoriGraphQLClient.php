<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Client;

use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use DevCraftClub\Shikimori\Exception\NetworkException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Throwable;

final class ShikimoriGraphQLClient
{
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly SdkConfig $config,
        private readonly ?RateLimiterInterface $rateLimiter = null
    ) {
    }

    public static function createHttpClient(): ClientInterface
    {
        return new \GuzzleHttp\Client();
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @return array<string, mixed>
     *
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function query(string $query, array $variables = []): array
    {
        $this->rateLimiter?->acquire();

        $payload = json_encode([
            'query' => $query,
            'variables' => $variables,
        ], JSON_THROW_ON_ERROR);

        $request = $this->requestFactory
            ->createRequest('POST', $this->config->getEndpoint())
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('User-Agent', $this->config->getUserAgent())
            ->withHeader('Accept', 'application/json')
            ->withBody($this->streamFactory->createStream($payload));

        $token = $this->config->getAccessToken();
        if ($token !== null && $token !== '') {
            $request = $request->withHeader('Authorization', 'Bearer ' . $token);
        }

        try {
            $response = $this->httpClient->sendRequest($request);
            $body = (string) $response->getBody();
            /** @var array{data?: array<string, mixed>, errors?: list<array{message: string}>} $decoded */
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            if (isset($decoded['errors']) && \count($decoded['errors']) > 0) {
                throw GraphQLQueryException::fromMessages(array_column($decoded['errors'], 'message'));
            }

            return $decoded['data'] ?? [];
        } catch (ClientExceptionInterface $e) {
            throw NetworkException::wrap($e);
        } catch (Throwable $e) {
            if ($e instanceof GraphQLQueryException) {
                throw $e;
            }

            throw NetworkException::wrap($e);
        }
    }
}
