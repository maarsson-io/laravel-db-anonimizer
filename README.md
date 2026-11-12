# maarsson/laravel-db-anonimizer

Developers sometimes need, or simply find it easier, to work locally with a copy of a real database. To protect privacy and address GDPR requirements, create an anonymized version for local use, preferably before the dump leaves the server.

## Installation

```bash
composer require maarsson/laravel-db-anonimizer
```

Publish the configuration:

```bash
php artisan vendor:publish --tag=laravel-db-anonimizer-config
```

## Configuration

Edit `config/anonymization.php` to match your application's schema.

| Setting | Purpose |
| --- | --- |
| `default_password` | Development-only password used by the `password_hash` rule. Defaults to `secret`. |
| `database_suffix` | Suffix for the copied database name. Defaults to `_anonymized`. |
| `consistent_tables` | Tables whose matching original values share replacements within a run. Matching uses all configured columns together. |
| `truncate_tables` | Tables to empty completely in the copy. |
| `anonymize_tables` | Replacement rules structured as `table => [column => rule]`. |

The default rules cover `users.name`, `users.email`, `users.password` and
`users.remember_token`. The default tables to empty are `failed_jobs`,
`password_resets` and `personal_access_tokens`.

Add rules for application-specific sensitive data; the default rules alone do not
cover an entire application database.

The anonymization command has not yet been moved into the package.

## Testing

```bash
composer install
composer test
```

Tests use PHPUnit and Orchestra Testbench to run in an isolated Laravel application.
No consumer project or database is required.
