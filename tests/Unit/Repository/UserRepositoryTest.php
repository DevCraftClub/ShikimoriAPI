<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Repository;

use DevCraftClub\Shikimori\DTO\UserDTO;
use DevCraftClub\Shikimori\Filter\UserListFilter;
use DevCraftClub\Shikimori\Repository\UserRepository;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \DevCraftClub\Shikimori\Repository\UserRepository
 */
final class UserRepositoryTest extends RepositoryTestCase
{
    public function testFindByIdReturnsDto(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['users' => [[
                    'id' => 1,
                    'nickname' => 'tester',
                    'avatar' => null,
                    'lastOnlineAt' => null,
                    'url' => '/tester',
                    'name' => null,
                    'sex' => null,
                    'fullYears' => null,
                    'createdAt' => null,
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new UserRepository($client);
        $user = $repository->findById(1);

        self::assertInstanceOf(UserDTO::class, $user);
        self::assertSame('tester', $user->getNickname());
    }

    public function testSearchReturnsDtos(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['users' => [[
                    'id' => 2,
                    'nickname' => 'admin',
                    'avatar' => null,
                    'lastOnlineAt' => null,
                    'url' => '/admin',
                    'name' => null,
                    'sex' => null,
                    'fullYears' => null,
                    'createdAt' => null,
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new UserRepository($client);
        $filter = (new UserListFilter())->withSearch('admin')->withLimit(1);
        $results = $repository->search($filter);

        self::assertCount(1, $results);
        self::assertSame('admin', $results[0]->getNickname());
    }

    public function testCurrentUserReturnsDto(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['currentUser' => [
                    'id' => 1,
                    'nickname' => 'me',
                    'avatar' => null,
                    'lastOnlineAt' => null,
                    'url' => '/me',
                    'name' => null,
                    'sex' => null,
                    'fullYears' => null,
                    'createdAt' => null,
                ]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new UserRepository($client);
        $user = $repository->currentUser();

        self::assertInstanceOf(UserDTO::class, $user);
        self::assertSame('me', $user->getNickname());
    }
}
