<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Client;

use DevCraftClub\Shikimori\Client\NullRateLimiter;
use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @covers \DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient
 */
final class ShikimoriGraphQLClientTest extends TestCase
{
    private ShikimoriGraphQLClient $client;
    /** @var ClientInterface&MockObject */
    private ClientInterface $httpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(ClientInterface::class);
        $factory = new HttpFactory();
        $config = SdkConfig::fromEnv(['user_agent' => 'Test/1.0']);

        $this->client = new ShikimoriGraphQLClient(
            $this->httpClient,
            $factory,
            $factory,
            $config,
            new NullRateLimiter()
        );
    }

    public function testSendsQueryAndReturnsData(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(static function (RequestInterface $request): bool {
                return $request->getHeaderLine('User-Agent') === 'Test/1.0'
                    && $request->getHeaderLine('Content-Type') === 'application/json'
                    && $request->getHeaderLine('Authorization') === '';
            }))
            ->willReturn(new Response(200, [], json_encode(['data' => ['animes' => []]], JSON_THROW_ON_ERROR)));

        $result = $this->client->query('{ animes { id } }');

        self::assertSame(['animes' => []], $result);
    }

    public function testAddsBearerTokenWhenConfigured(): void
    {
        $factory = new HttpFactory();
        $config = SdkConfig::fromEnv(['user_agent' => 'Test/1.0'])->withAccessToken('secret-token');

        $client = new ShikimoriGraphQLClient(
            $this->httpClient,
            $factory,
            $factory,
            $config,
            new NullRateLimiter()
        );

        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(static function (RequestInterface $request): bool {
                return $request->getHeaderLine('Authorization') === 'Bearer secret-token';
            }))
            ->willReturn(new Response(200, [], json_encode(['data' => []], JSON_THROW_ON_ERROR)));

        $client->query('{ currentUser { id } }');
    }

    public function testThrowsOnGraphQLError(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'errors' => [['message' => 'Field not found']],
            ], JSON_THROW_ON_ERROR)));

        $this->expectException(GraphQLQueryException::class);
        $this->expectExceptionMessage('Field not found');

        $this->client->query('{ unknown }');
    }
}
