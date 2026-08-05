# Bang

Bang is a self-hosted firearms inventory and training tracker. It helps users keep an organized record of firearms, ammunition, magazines, accessories, storage locations, ranges, orders, photographs, notes, and range-training sessions in one place.

The application is a Laravel API with a Vue single-page frontend. Authentication uses JWTs, and the API is designed to run on its own domain or subdomain when needed.

## Features

- Firearm inventory, lifecycle status, activity history, and mounted accessories
- Ammunition inventory, stock additions, caliber totals, and usage statistics
- Magazine groups, magazine batches, and magazine state tracking
- Optics, suppressors, lights, mounts, and other accessory records
- Locations, stores, ranges, orders, notes, and reference data
- Training-session logs with session lines, targets, inventory usage, and statistics
- Private picture storage with support for local storage, Amazon S3, and S3-compatible services
- JWT authentication, password reset, and optional user registration
- API filtering, sorting, and includes through `spatie/laravel-query-builder`

## Requirements

- PHP 8.3 or newer with the JSON extension
- Composer 2
- Node.js and npm
- PostgreSQL (the default application and test database driver)
- A web server capable of serving a Laravel application
- An S3-compatible object store if pictures should be stored outside the local filesystem

Redis, a mail server, and a queue worker are optional for a basic local installation. The default local configuration uses file-based cache and sessions and synchronous queues.

## Installation

1. Clone the repository and enter the project directory:

   ```bash
   git clone https://github.com/elconejito/bang.git
   cd bang
   ```

2. Install PHP and JavaScript dependencies:

   ```bash
   composer install
   npm install
   ```

3. Create the environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure PostgreSQL in `.env`. The default values expect a database named `bang` on `127.0.0.1:5432`:

   ```dotenv
   DB_CONNECTION=pgsql
   DB_DATABASE=bang
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. For the simplest local setup, leave `APP_API_DOMAIN` empty so the API is served under `/api`. Configure the remaining application URLs in `.env`:

   ```dotenv
   APP_URL=http://localhost
   APP_API_DOMAIN=
   VITE_API_BASE_URL=/api
   ```

   ```bash
   php artisan jwt:secret
   ```

6. Run the migrations and seed the built-in reference data:

   ```bash
   php artisan migrate --seed
   ```

7. Start the frontend asset server:

   ```bash
   npm run dev
   ```

   Serve the Laravel application through your local web server, or use `php artisan serve` for a simple local backend. In production, build the frontend assets with `npm run build` and serve the application through a production-ready web server.

### Optional development user

Set these variables before running the seeders to create a local test user:

```dotenv
TEST_EMAIL=test@test.com
TEST_NAME=Testy McTest
TEST_PASSWORD=password
```

The seeder skips user creation when any of these values is empty. Set `REGISTRATION_ENABLED=true` if users should be allowed to register through the application.

## Configuration

The complete list of environment variables is in [`.env.example`](.env.example). The most important application-specific settings are:

| Variable | Required | Description |
| --- | --- | --- |
| `APP_API_DOMAIN` | No | Optional host for API domain routing, such as `api.bang.test` or `api.example.com`. Leave empty to use the `/api` path. |
| `JWT_SECRET` | Yes | Secret used to sign JWT access tokens. Generate it with `php artisan jwt:secret`. |
| `VITE_API_BASE_URL` | Yes | API base URL used by the Vue frontend; `/api` is the usual local value. |
| `REGISTRATION_ENABLED` | No | Enables self-service registration when set to `true`; defaults to `false`. |
| `PICTURES_DISK_DRIVER` | No | Picture storage driver. Defaults to `local`; use `s3` for object storage. |
| `TEST_EMAIL`, `TEST_NAME`, `TEST_PASSWORD` | No | Credentials for the optional seeded development user. |

For S3 storage, also configure `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, and, when applicable, `AWS_ENDPOINT` and `AWS_USE_PATH_STYLE_ENDPOINT`.

## Development commands

```bash
# Start Vite in watch mode
npm run dev

# Build production frontend assets
npm run build

# Lint frontend source
npm run lint

# Run frontend tests
npm test

# Run the Laravel test suite
php artisan test --compact
```

The Laravel tests use a separate PostgreSQL database named `bang_testing`. Create it before running the suite:

```bash
createdb bang_testing
php artisan test --compact
```

## Project structure

- `app/` — Laravel domain logic, API controllers, models, actions, and rules
- `database/` — migrations, factories, and seeders
- `resources/front-end/` — Vue application source
- `routes/api.php` — authenticated and public API routes
- `routes/web.php` — frontend catch-all route
- `tests/` — PHPUnit feature and unit tests

## API

The API is defined in [`routes/api.php`](routes/api.php). With `APP_API_DOMAIN` empty, authentication endpoints are available under `/api/auth`; application resources are protected by JWT authentication. If `APP_API_DOMAIN` is set, the same routes are served from that host without the `/api` prefix. The frontend consumes the API using the URL configured by `VITE_API_BASE_URL`.

## Security and privacy

Bang is intended for self-hosting. Firearms, inventory, location, and photograph data can be sensitive. Use HTTPS, strong credentials, secure JWT and database secrets, private object-storage permissions, regular backups, and appropriate access controls before exposing an installation to the internet.

Please report security vulnerabilities privately to the project maintainers rather than opening a public issue.

## Contributing

Contributions are welcome. Before opening a pull request:

1. Make focused changes that follow the existing Laravel and Vue conventions.
2. Add or update tests for behavior changes.
3. Run the relevant PHP and frontend checks.
4. Include a clear description of the change and any required configuration updates.

## License

Bang is open-source software licensed under the [MIT License](LICENSE).
