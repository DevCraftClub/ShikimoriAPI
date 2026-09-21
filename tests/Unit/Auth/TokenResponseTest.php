<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Auth;

use DevCraftClub\Shikimori\Auth\TokenResponse;
use DevCraftClub\Shikimori\Exception\AuthException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DevCraftClub\Shikimori\Auth\TokenResponse
 */
final class TokenResponseTest extends TestCase
{
    public function testFromArrayAndGetters(): void
    {
        $token = TokenResponse::fromArray([
            'access_token' => 'abc',
            'token_type' => 'Bearer',
            'refresh_token' => 'refresh',
            'scope' => '',
            'created_at' => 1234567890,
            'expires_in' => 86400,
        ]);

        self::assertSame('abc', $token->getAccessToken());
        self::assertSame('Bearer', $token->getTokenType());
        self::assertSame('refresh', $token->getRefreshToken());
        self::assertSame('', $token->getScope());
        self::assertSame(1234567890, $token->getCreatedAt());
        self::assertSame(86400, $token->getExpiresIn());
    }

    public function testMissingAccessTokenThrows(): void
    {
        $this->expectException(AuthException::class);

        TokenResponse::fromArray([]);
    }
}
