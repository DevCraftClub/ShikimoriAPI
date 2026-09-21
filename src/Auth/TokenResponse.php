<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Auth;

use Devcraft\Abstracts\AbstractWith;
use Devcraft\Attributes\With;
use DevCraftClub\Shikimori\Exception\AuthException;
use Lombok\Getter;
use Lombok\Setter;

/**
 * @method self withAccessToken(string $accessToken)
 * @method self withTokenType(string $tokenType)
 * @method self withRefreshToken(string|null $refreshToken)
 * @method self withScope(string|null $scope)
 * @method self withCreatedAt(int $createdAt)
 * @method self withExpiresIn(int $expiresIn)
 * @method string getAccessToken()
 * @method string getTokenType()
 * @method string|null getRefreshToken()
 * @method string|null getScope()
 * @method int getCreatedAt()
 * @method int getExpiresIn()
 */
#[Getter, Setter]
final class TokenResponse extends AbstractWith
{
    public function __construct()
    {
        parent::__construct();
    }

    #[With]
    private string $accessToken = ''; // @phpstan-ignore-line Lombok getter

    #[With]
    private string $tokenType = 'Bearer'; // @phpstan-ignore-line Lombok getter

    #[With]
    private ?string $refreshToken = null; // @phpstan-ignore-line Lombok getter

    #[With]
    private ?string $scope = null; // @phpstan-ignore-line Lombok getter

    #[With]
    private int $createdAt = 0; // @phpstan-ignore-line Lombok getter

    #[With]
    private int $expiresIn = 0; // @phpstan-ignore-line Lombok getter

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $token = new self();

        if (!\is_string($data['access_token'] ?? null) || $data['access_token'] === '') {
            throw AuthException::invalidTokenResponse('access_token missing');
        }

        $token = $token
            ->withAccessToken((string) $data['access_token'])
            ->withTokenType(\is_string($data['token_type'] ?? null) ? (string) $data['token_type'] : 'Bearer')
            ->withRefreshToken(\is_string($data['refresh_token'] ?? null) ? (string) $data['refresh_token'] : null)
            ->withScope(\is_string($data['scope'] ?? null) ? (string) $data['scope'] : null)
            ->withCreatedAt(\is_int($data['created_at'] ?? null) ? (int) $data['created_at'] : time())
            ->withExpiresIn(\is_int($data['expires_in'] ?? null) ? (int) $data['expires_in'] : 0);

        return $token;
    }
}
