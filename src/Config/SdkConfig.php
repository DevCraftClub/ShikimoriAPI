<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Config;

use Devcraft\Abstracts\AbstractWith;
use Devcraft\Attributes\With;
use DevCraftClub\Shikimori\Exception\ConfigurationException;
use Lombok\Getter;
use Lombok\Setter;

/**
 * @method self withEndpoint(string $endpoint)
 * @method self withUserAgent(string $userAgent)
 * @method self withAccessToken(string|null $accessToken)
 * @method self withCacheTtl(int $cacheTtl)
 * @method self withCacheDir(string $cacheDir)
 * @method self withRateLimitEnabled(bool $rateLimitEnabled)
 * @method self withRateLimitRps(int $rateLimitRps)
 * @method self withRateLimitRpm(int $rateLimitRpm)
 * @method self withOauthAuthorizeUrl(string $oauthAuthorizeUrl)
 * @method self withOauthTokenUrl(string $oauthTokenUrl)
 * @method self withOauthClientId(string|null $oauthClientId)
 * @method self withOauthClientSecret(string|null $oauthClientSecret)
 * @method self withOauthRedirectUri(string|null $oauthRedirectUri)
 * @method string getEndpoint()
 * @method string getUserAgent()
 * @method string|null getAccessToken()
 * @method int getCacheTtl()
 * @method string getCacheDir()
 * @method bool isRateLimitEnabled()
 * @method int getRateLimitRps()
 * @method int getRateLimitRpm()
 * @method string getOauthAuthorizeUrl()
 * @method string getOauthTokenUrl()
 * @method string|null getOauthClientId()
 * @method string|null getOauthClientSecret()
 * @method string|null getOauthRedirectUri()
 * @method self withDbDriver(string $dbDriver)
 * @method self withDbHost(string $dbHost)
 * @method self withDbPort(int|null $dbPort)
 * @method self withDbName(string $dbName)
 * @method self withDbUser(string|null $dbUser)
 * @method self withDbPassword(string|null $dbPassword)
 * @method self withDbSqlitePath(string|null $dbSqlitePath)
 * @method string getDbDriver()
 * @method string getDbHost()
 * @method int|null getDbPort()
 * @method string getDbName()
 * @method string|null getDbUser()
 * @method string|null getDbPassword()
 * @method string|null getDbSqlitePath()
 */
#[Getter, Setter]
final class SdkConfig extends AbstractWith
{
    public function __construct()
    {
        parent::__construct();
    }

    #[With]
    private string $endpoint = 'https://shikimori.io/api/graphql';

    #[With]
    private string $userAgent = '';

    #[With]
    private ?string $accessToken = null;

    #[With]
    private int $cacheTtl = 86400;

    #[With]
    private string $cacheDir = '';

    #[With]
    private bool $rateLimitEnabled = true;

    #[With]
    private int $rateLimitRps = 5;

    #[With]
    private int $rateLimitRpm = 90;

    #[With]
    private string $oauthAuthorizeUrl = 'https://shikimori.io/oauth/authorize';

    #[With]
    private string $oauthTokenUrl = 'https://shikimori.io/oauth/token';

    #[With]
    private ?string $oauthClientId = null;

    #[With]
    private ?string $oauthClientSecret = null;

    #[With]
    private ?string $oauthRedirectUri = null;

    #[With]
    private string $dbDriver = 'sqlite';

    #[With]
    private string $dbHost = 'localhost';

    #[With]
    private ?int $dbPort = null;

    #[With]
    private string $dbName = 'shikimori';

    #[With]
    private ?string $dbUser = null;

    #[With]
    private ?string $dbPassword = null;

    #[With]
    private ?string $dbSqlitePath = '/tmp/shikimori.sqlite';

    /**
     * @param array<string, mixed>|null $overrides
     */
    public static function fromEnv(?array $overrides = null): self
    {
        $config = new self();

        $config = $config
            ->withEndpoint(self::envString('SHIKIMORI_ENDPOINT', $config->endpoint))
            ->withUserAgent(self::envString('SHIKIMORI_USER_AGENT', $config->userAgent))
            ->withAccessToken(self::envNullableString('SHIKIMORI_ACCESS_TOKEN', $config->accessToken))
            ->withCacheTtl(self::envInt('SHIKIMORI_CACHE_TTL', $config->cacheTtl))
            ->withCacheDir(self::envString('SHIKIMORI_CACHE_DIR', $config->cacheDir))
            ->withRateLimitEnabled(self::envBool('SHIKIMORI_RATE_LIMIT_ENABLED', $config->rateLimitEnabled))
            ->withRateLimitRps(self::envInt('SHIKIMORI_RATE_LIMIT_RPS', $config->rateLimitRps))
            ->withRateLimitRpm(self::envInt('SHIKIMORI_RATE_LIMIT_RPM', $config->rateLimitRpm))
            ->withOauthAuthorizeUrl(self::envString('SHIKIMORI_OAUTH_AUTHORIZE_URL', $config->oauthAuthorizeUrl))
            ->withOauthTokenUrl(self::envString('SHIKIMORI_OAUTH_TOKEN_URL', $config->oauthTokenUrl))
            ->withOauthClientId(self::envNullableString('SHIKIMORI_OAUTH_CLIENT_ID', $config->oauthClientId))
            ->withOauthClientSecret(self::envNullableString('SHIKIMORI_OAUTH_CLIENT_SECRET', $config->oauthClientSecret))
            ->withOauthRedirectUri(self::envNullableString('SHIKIMORI_OAUTH_REDIRECT_URI', $config->oauthRedirectUri))
            ->withDbDriver(self::envString('SHIKIMORI_DB_DRIVER', $config->dbDriver))
            ->withDbHost(self::envString('SHIKIMORI_DB_HOST', $config->dbHost))
            ->withDbPort(self::envNullableInt('SHIKIMORI_DB_PORT', $config->dbPort))
            ->withDbName(self::envString('SHIKIMORI_DB_NAME', $config->dbName))
            ->withDbUser(self::envNullableString('SHIKIMORI_DB_USER', $config->dbUser))
            ->withDbPassword(self::envNullableString('SHIKIMORI_DB_PASSWORD', $config->dbPassword))
            ->withDbSqlitePath(self::envNullableString('SHIKIMORI_DB_SQLITE_PATH', $config->dbSqlitePath));

        if ($overrides !== null) {
            foreach ($overrides as $key => $value) {
                $method = 'with' . str_replace('_', '', ucwords($key, '_'));
                if (\is_callable([$config, $method])) {
                    /** @var self $config */
                    $config = $config->$method($value);
                }
            }
        }

        if ($config->userAgent === '') {
            throw ConfigurationException::missingUserAgent();
        }

        if ($config->cacheDir === '') {
            $config = $config->withCacheDir(sys_get_temp_dir() . '/shikimori_cache');
        }

        if ($config->cacheTtl <= 0) {
            throw ConfigurationException::invalidPositive('SHIKIMORI_CACHE_TTL', $config->cacheTtl);
        }

        if ($config->rateLimitRps <= 0) {
            throw ConfigurationException::invalidPositive('SHIKIMORI_RATE_LIMIT_RPS', $config->rateLimitRps);
        }

        if ($config->rateLimitRpm <= 0) {
            throw ConfigurationException::invalidPositive('SHIKIMORI_RATE_LIMIT_RPM', $config->rateLimitRpm);
        }

        return $config;
    }

    private static function envString(string $key, string $default): string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        return \is_string($value) && $value !== '' ? $value : $default;
    }

    private static function envNullableString(string $key, ?string $default): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return (string) $value;
    }

    private static function envInt(string $key, int $default): int
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return (int) $value;
    }

    private static function envNullableInt(string $key, ?int $default): ?int
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return (int) $value;
    }

    private static function envBool(string $key, bool $default): bool
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return \in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }
}
