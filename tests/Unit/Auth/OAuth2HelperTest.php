<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Auth;

use DevCraftClub\Shikimori\Auth\OAuth2Helper;
use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Exception\AuthException;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @covers \DevCraftClub\Shikimori\Auth\OAuth2Helper
 */
final class OAuth2HelperTest extends TestCase
{
    /** @var ClientInterface&MockObject */
    private ClientInterface $httpClient;
    private OAuth2Helper $helper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(ClientInterface::class);
        $factory = new HttpFactory();
        $config = SdkConfig::fromEnv([
            'user_agent' => 'Test/1.0',
            'oauth_client_id' => 'client-id',
            'oauth_client_secret' => 'client-secret',
            'oauth_redirect_uri' => 'https://app/callback',
        ]);

        $this->helper = new OAuth2Helper(
            $this->httpClient,
            $factory,
            $factory,
            $config
        );
    }

    public function testBuildAuthorizeUrl(): void
    {
        $url = $this->helper->buildAuthorizeUrl();

        self::assertStringContainsString('https://shikimori.io/oauth/authorize', $url);
        self::assertStringContainsString('client_id=client-id', $url);
        self::assertStringContainsString('redirect_uri=https%3A%2F%2Fapp%2Fcallback', $url);
        self::assertStringContainsString('response_type=code', $url);
    }

    public function testBuildAuthorizeUrlRequiresClientId(): void
    {
        $factory = new HttpFactory();
        $config = SdkConfig::fromEnv(['user_agent' => 'Test/1.0']);
        $helper = new OAuth2Helper($this->httpClient, $factory, $factory, $config);

        $this->expectException(AuthException::class);

        $helper->buildAuthorizeUrl();
    }

    public function testExchangeCode(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(static function (RequestInterface $request): bool {
                return $request->getMethod() === 'POST'
                    && (string) $request->getUri() === 'https://shikimori.io/oauth/token'
                    && $request->getHeaderLine('User-Agent') === 'Test/1.0';
            }))
            ->willReturn(new Response(200, [], json_encode([
                'access_token' => 'new-token',
                'token_type' => 'Bearer',
                'refresh_token' => 'new-refresh',
                'expires_in' => 86400,
                'created_at' => 1234567890,
            ], JSON_THROW_ON_ERROR)));

        $response = $this->helper->exchangeCode('auth-code');

        self::assertSame('new-token', $response->getAccessToken());
        self::assertSame('new-refresh', $response->getRefreshToken());
    }

    public function testRefreshTokenRequiresNonEmptyToken(): void
    {
        $this->expectException(AuthException::class);

        $this->helper->refreshToken('');
    }
}
