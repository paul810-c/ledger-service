# Ledger Service

A work in progress Symfony 7.2-based API service for managing multi-currency ledgers and transactions.

---

## Architecture (Quick Overview)

The app is structured to stay clean and easy to extend:

- **Domain layer** – This is the heart of the logic (Ledgers, Transactions, Balances, etc.). No framework stuff here.
- **Application layer** – Commands and Queries go through Symfony Messenger. This makes it easy to handle async messages and keep controller logic slim.
- **Infrastructure layer** – Handles DB access (Doctrine), messaging (RabbitMQ), and other system-level concerns.
- **Presentation layer** – Just basic Symfony controllers that expose our REST API.
- **Authentication** – We use a custom API token header and Symfony’s security system to keep things simple and secure.
- **Dockerized** – All services (app, DB, RabbitMQ, Redis, worker) are containerized so the stack spins up reliably anywhere.

---

## Features

- Create ledgers
- Record credit/debit transactions
- Automatically manage and update ledger balances
- Multi-currency support
- RESTful API design
- Asynchronous message handling via Symfony Messenger & RabbitMQ
- PostgreSQL database
- Integration and functional testing with fixtures
- API documentation with NelmioApiDocBundle
- Stateless internal authentication with Symfony Security

## Requirements

- PHP 8.3+
- Composer
- Docker & Docker Compose

---

## Getting Started

### 1. Clone the repository
```bash
git clone https://github.com/your-org/ledger-service.git
cd ledger-service
```

### 2. Copy and update environment configuration
```bash
cp .env .env.local
```
Edit `.env.local` and provide credentials for:
- `POSTGRES_USER`
- `POSTGRES_PASSWORD`
- `POSTGRES_DB`
- `RABBITMQ_DEFAULT_USER`
- `RABBITMQ_DEFAULT_PASS`
- `INTERNAL_API_TOKEN`

### 3. Build and start containers
```bash
docker-compose up -d --build
```

### 4. Set up the database
```bash
docker-compose exec app php bin/console doctrine:database:create
docker-compose exec app php bin/console doctrine:migrations:migrate
```

### 5. Install dependencies
```bash
docker-compose exec app composer install
```

---

## API Endpoints

- `POST /api/ledgers` - Create a new ledger
- `POST /api/transactions` - Record a transaction
- `GET /api/balances/{ledgerId}` - Get balances for a ledger

Full documentation available at:
```
http://localhost:8000/api/doc
```

---

## Testing

Run tests inside the container:
```bash
docker-compose exec app php bin/phpunit
```

Tests include:
- Unit tests
- Integration tests
- Functional tests

---

## Fixtures

Fixtures are loaded during integration/functional tests via base test classes:
- `KernelTestCaseWithFixtures`
- `WebTestCaseWithFixtures`

---

## Authentication

Stateless internal service-to-service authentication via custom HTTP header:
```
X-API-TOKEN: {INTERNAL_API_TOKEN}
```
Configured using Symfony's native security system.

---

## Docker Services

- **app**: PHP-FPM + Symfony
- **db**: PostgreSQL 15
- **rabbitmq**: RabbitMQ with management UI (http://localhost:15672)
- **redis**: Redis 7
- **messenger-worker**: Symfony Messenger worker with restart policy

---

## Improvements and TODOs

- Add code quality tools (phpstan, psalm, php-cs-fixer, php-codesniffer)
- Add JWT-based auth option
- Add rate limiting (API rate throttling)
- Add NGINX as production reverse proxy
- Migrate Currency enum to DB table for flexibility
- Add createdAt/updatedAt traits for all entities
- Add E2E, contract testing and more testting scenarios
- Improve monitoring/logging (e.g. Graylog, Sentry)
- Improve Makefile

---

## License
MIT

