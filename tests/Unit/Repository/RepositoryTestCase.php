<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\Config\SdkConfig;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

abstract class RepositoryTestCase extends TestCase
{
    /** @var ClientInterface&MockObject */
    protected ClientInterface $httpClient;

    protected function createClient(): ShikimoriGraphQLClient
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $factory = new HttpFactory();
        $config = SdkConfig::fromEnv(['user_agent' => 'Test/1.0']);

        return new ShikimoriGraphQLClient(
            $this->httpClient,
            $factory,
            $factory,
            $config
        );
    }
}
