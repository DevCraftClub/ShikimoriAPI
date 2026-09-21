# Shikimori GraphQL PHP SDK

PHP 8.3+ клиент для GraphQL API Shikimori (shikimori.io) с двойным движком хранения (Cycle ORM + файловый кэш) и fluent-моделями через [devcraftclub/dev-tools](https://packagist.org/packages/devcraftclub/dev-tools).

## Установка

```bash
composer require devcraftclub/shikimori-api-php
```

## Быстрый старт

Скопируйте `.env.example` в свой `.env` и заполните обязательные поля:

```dotenv
SHIKIMORI_USER_AGENT=MyApp/1.0
SHIKIMORI_ACCESS_TOKEN=           # опционально, для currentUser/userRates
SHIKIMORI_CACHE_TTL=86400
SHIKIMORI_CACHE_DIR=/tmp/shikimori_cache

# Cycle ORM — опционально, для персистентности
SHIKIMORI_DB_DRIVER=sqlite        # sqlite, postgres или mysql
SHIKIMORI_DB_NAME=shikimori
SHIKIMORI_DB_SQLITE_PATH=/tmp/shikimori.sqlite
```

```php
<?php
require 'vendor/autoload.php';

use DevCraftClub\Shikimori\ShikimoriClient;
use DevCraftClub\Shikimori\Filter\AnimeListFilter;
use DevCraftClub\Shikimori\Query\Profile;

$client = ShikimoriClient::fromEnv();

// Поиск аниме
$results = $client->animes()->search(
    (new AnimeListFilter())
        ->withSearch('One Piece')
        ->withLimit(5),
    Profile::Summary
);

// Получение одного аниме с деталями
$anime = $client->animes()->findById(21, Profile::Detail, forceRecheck: false);

// Жанры
$genres = $client->genres()->list(\DevCraftClub\Shikimori\Enum\GenreEntryType::Anime);

// Текущий пользователь (требуется токен)
$user = $client->users()->currentUser();
```

## Fluent-модели

DTO, фильтры и `TokenResponse` построены на `Devcraft\Abstracts\AbstractWith` + Lombok Getter/Setter + DevTools With/WithItem:

```php
$filter = (new AnimeListFilter())
    ->withSearch('Naruto')
    ->withOrder(AnimeOrder::Popularity)
    ->withLimit(10)
    ->withIdsItem(1)
    ->withIdsItem(2);
```

## Хранение данных

Аниме — эталон сущности с полной персистентностью.

Подключить Cycle ORM можно из env-настроек одним вызовом:

```php
$client = ShikimoriClient::fromEnv()->withDatabase();
$anime = $client->animes()->findById(21);
```

Или передать готовый ORM вручную:

```php
$client->withOrm($orm, $entityManager);
```

Если не подключать ORM, SDK использует файловый кэш (PSR-6 через `Devcraft\Cache\FileCachePool`) или работает только через API, если `SHIKIMORI_CACHE_DIR` пуст.

Поведение свежести:

- `forceRecheck = false` — сначала БД/кэш, при устаревании данных — повторный запрос к API.
- `forceRecheck = true` — всегда запрос к API и обновление хранилища.

## Ограничение частоты запросов

По умолчанию клиент соблюдает лимиты Shikimori: 5 запросов/с, 90/мин. Настройки через env:

```dotenv
SHIKIMORI_RATE_LIMIT_ENABLED=true
SHIKIMORI_RATE_LIMIT_RPS=5
SHIKIMORI_RATE_LIMIT_RPM=90
```

## OAuth2

```php
$oauth = $client->oauth();
$authorizeUrl = $oauth->buildAuthorizeUrl();
$token = $oauth->exchangeCode($_GET['code']);
$refreshed = $oauth->refreshToken($token->getRefreshToken());
```

## Разработка

```bash
composer install
vendor/bin/phpstan analyse --level=9
vendor/bin/phpunit --no-coverage
```

## Обновление Packagist

При пуше в `main`/`master` workflow сравнивает `version` в `composer.json` с предыдущим коммитом. Если версия изменилась или пуш пришёлся на тег, GitHub Actions вызывает API Packagist с токеном из переменной `COMPOSER_UPDATE_API_KEY`:

```bash
# Settings → Secrets and variables → Actions → Repository secrets
COMPOSER_UPDATE_API_KEY=your-packagist-api-token
```

Также задайте `PACKAGIST_USERNAME` в переменных репозитория (vars), иначе workflow использует `devcraftclub` по умолчанию.

## Лицензия

MIT
