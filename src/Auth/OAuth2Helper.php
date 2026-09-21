<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Auth;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Exception\AuthException;
use DevCraftClub\Shikimori\Exception\NetworkException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Throwable;

final class OAuth2Helper
{
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly SdkConfig $config
    ) {
    }

    public static function fromConfig(SdkConfig $config): self
    {
        $client = ShikimoriGraphQLClient::createHttpClient();

        return new self(
            $client,
            new \GuzzleHttp\Psr7\HttpFactory(),
            new \GuzzleHttp\Psr7\HttpFactory(),
            $config
        );
    }

    public function buildAuthorizeUrl(): string
    {
        $this->requireClientId();

        $query = http_build_query([
            'client_id' => $this->config->getOauthClientId(),
            'redirect_uri' => $this->config->getOauthRedirectUri(),
            'response_type' => 'code',
            'scope' => '',
        ], '', '&', PHP_QUERY_RFC3986);

        return $this->config->getOauthAuthorizeUrl() . '?' . $query;
    }

    /**
     * @throws AuthException
     * @throws NetworkException
     */
    public function exchangeCode(string $code): TokenResponse
    {
        $this->requireClientCredentials();

        if ($code === '') {
            throw AuthException::invalidTokenResponse('authorization code empty');
        }

        return $this->requestToken([
            'grant_type' => 'authorization_code',
            'client_id' => $this->config->getOauthClientId(),
            'client_secret' => $this->config->getOauthClientSecret(),
            'code' => $code,
            'redirect_uri' => $this->config->getOauthRedirectUri(),
        ]);
    }

    /**
     * @throws AuthException
     * @throws NetworkException
     */
    public function refreshToken(string $refreshToken): TokenResponse
    {
        $this->requireClientCredentials();

        if ($refreshToken === '') {
            throw AuthException::missingRefreshToken();
        }

        return $this->requestToken([
            'grant_type' => 'refresh_token',
            'client_id' => $this->config->getOauthClientId(),
            'client_secret' => $this->config->getOauthClientSecret(),
            'refresh_token' => $refreshToken,
        ]);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @throws NetworkException
     * @throws AuthException
     */
    private function requestToken(array $params): TokenResponse
    {
        $payload = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        $request = $this->requestFactory
            ->createRequest('POST', $this->config->getOauthTokenUrl())
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('User-Agent', $this->config->getUserAgent())
            ->withBody($this->streamFactory->createStream($payload));

        try {
            $response = $this->httpClient->sendRequest($request);
            /** @var array<string, mixed> $data */
            $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

            return TokenResponse::fromArray($data);
        } catch (Throwable $e) {
            throw NetworkException::wrap($e);
        }
    }

    private function requireClientId(): void
    {
        if ($this->config->getOauthClientId() === null || $this->config->getOauthClientId() === '') {
            throw AuthException::missingClientCredentials();
        }
    }

    private function requireClientCredentials(): void
    {
        if (
            $this->config->getOauthClientId() === null || $this->config->getOauthClientId() === ''
            || $this->config->getOauthClientSecret() === null || $this->config->getOauthClientSecret() === ''
        ) {
            throw AuthException::missingClientCredentials();
        }
    }
}
