# Admiral Digital Banking API

A mini-banking API built with Laravel, providing basic account and transaction management features (deposit, transfer, balance inquiry, and transaction history).

---

## Introduction

This project serves as a lightweight banking backend, allowing users to:

* Authenticate via Sanctum (login/register/logout).
* Retrieve their account balance.
* List known recipient accounts.
* Deposit funds into any account (except system or own account).
* Transfer funds to approved recipients.
* View all transactions and the most recent transaction.

The code is organized into Controllers, Services, Form Requests, Resources, and Traits for clear separation of concerns.

---

## Getting Started

### Prerequisites

* PHP >= 8.1
* Composer
* MySQL (or other supported database)
* Laravel 10.x

### Installation

1. **Clone the repo**

   ```bash
   git clone git@github.com:koyanyaroo/ad-bank-api.git
   cd ad-bank-api
   ```

2. **Install dependencies**

   ```bash
   composer install
   ```

3. **Environment setup**

    * Copy `.env.example` to `.env`
    * Set your database credentials
    * Generate an application key:

      ```bash
      php artisan key:generate
      ```

4. **Database migrations & seeding**

   ```bash
   php artisan migrate --seed
   ```

5. **Serve the application**

   ```bash
   php artisan serve
   ```

Your API will be available at `http://localhost:8000/api/v1`

### API Documentation

A complete OpenAPI 3.0 spec is provided in `admiral_openapi.json`. You can view it with any Swagger/OpenAPI tool or import it into Postman.

---

## Build & Test

* **Run tests**

  ```bash
  php artisan test
  ```

  or

  ```bash
  vendor/bin/phpunit
  ```

* **Code style**

    * PSR-12 via PHP-CS-Fixer or PHP\_CodeSniffer

---

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/YourFeature`)
3. Commit your changes (`git commit -m "Add feature"`)
4. Push to the branch (`git push origin feature/YourFeature`)
5. Open a Pull Request

Please follow PSR-12 coding standards and include tests for new functionality.

---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
