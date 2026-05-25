# Bang

A personal firearms tracking application built with Laravel + Vue.

## Setup

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
npm install && npm run dev
```

## Environment Variables

Standard Laravel variables (APP_KEY, DB_*, MAIL_*, REDIS_*, etc.) are documented by the framework. The non-standard variables specific to this app are:

| Variable | Required | Description |
|---|---|---|
| `APP_API_DOMAIN` | Yes | Domain used for API subdomain routing. Local: `bang.test`. Production: `api.yourdomain.com`. |
| `REGISTRATION_ENABLED` | No | Set to `true` to allow new user self-registration. Defaults to `false`. |
| `JWT_SECRET` | Yes | Secret key for signing JWT tokens. Generate with `php artisan jwt:secret`. |
| `VITE_API_BASE_URL` | Yes | Base URL for API requests from the Vue frontend. Typically `/api`. |
| `TEST_EMAIL` | No | Email for the seeded dev/test user. Defaults to `test@test.com`. |
| `TEST_NAME` | No | Name for the seeded dev/test user. Defaults to `Testy McTest`. |
| `TEST_PASSWORD` | No | Password for the seeded dev/test user. Defaults to `password`. |

## Testing

Tests run against a separate `bang_testing` database (configured in `phpunit.xml`) to avoid touching your development data. Create the database before running tests:

```bash
createdb bang_testing
php artisan test
```