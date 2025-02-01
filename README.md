## Commission Calculator App

### Requirements

- PHP (version 8.3 or higher)
- Extensions: `ext-bcmath`
- Docker (optional, for containerized setup)

### Steps to Run the Project

**Run project with docker:**

```bash
docker compose run --rm php bash
```

**Commands for both local setup and docker:**

```bash
# install dependencies
composer install

# Copy env file and update the `EXCHANGE_RATES_API_KEY` var, you can use `9bedae7b9d0f13d8069fa36c6399`
cp .env.example .env

# run unit tests
composer test

# Use the provided `input.csv` file or create a new CSV file for testing.
php app.php input.csv
```

**Calculation result with exchange rates: EUR:USD - 1:1.1497, EUR:JPY - 1:129.53:**

```
➜ php app.php input.csv
0.60
3.00
0.00
0.06
1.50
0
0.70
0.30
0.30
3.00
0.00
0.00
8612
```

### Project structure

#### Commission Calculation Architecture

This architecture is designed to handle commission calculations dynamically and extensible for adding more rules in the future. It consists of the following components:

- **Commission Calculators**: These implement a common interface for calculating commission based on the operation amount.
- **Base Amount Resolvers**: These implement a resolver interface to determine the base operation amount used by the calculators.
- **Commission Handler**: This combines a calculator and a resolver, managing the commission calculation process.
- **Registry**: It loads the handlers based on configuration.
- **Config File**: This file configures each handler with the necessary calculator and an optional base amount resolver.

At runtime, the appropriate handler is selected based on operation params, allowing flexible commission calculation. This design also allows for easy extension to add new commission calculation rules in the future.

The app supports only 3 currencies: EUR, USD, and JPY, listed in the 'currencies' field in the config/app.php file. To support more currencies, simply add them to the config file. If a currency is not supported, the execution will fail.

The app uses bc-match extension for more accurate money calculations.

#### Libraries Used

- **phpdotenv**: Loads environment variables from a .env file.
- **Mockery**: A mock object framework for testing.
- **Guzzle**: A PHP HTTP client for making HTTP requests. Implemented an exponential backoff strategy for failed requests in class: `Services/AbstractHttpService.php`.
- **PHP CS Fixer**: Used for formatting and standards, and configured with PHPStorm.

### Unit Testing

Unit tests are written using PHPUnit and Mockery to ensure test isolation and maintainability.

- **PHPUnit**: For writing and executing unit tests.
- **Mockery**: For mocking dependencies to keep tests isolated.
- **Reflection API**: To override protected properties and invoke protected methods in unit tests.
- **Data Providers**: Organized separately in traits to maintain test clarity.
- **PHP Attributes**: Used for configuring data providers, before, and after methods.
- **PHP CS Fixer**: Used for code formatting and maintaining coding standards.

#### Running Tests
```bash
# Running unit tests
composer test

# Generate code coverage in text format
composer coverage-text

# Generate Code coverage in html format
composer coverage-html
```

#### latest test & coverage results

*Note: The `src/Support` directory is not covered by unit tests.*

```
Runtime:       PHP 8.3.16
Configuration: /var/www/html/phpunit.xml.dist

..........................................................        58 / 58 (100%)

Time: 00:00.142, Memory: 14.00 MB

OK (58 tests, 99 assertions)

Code Coverage Report:      
2025-02-01 22:02:30      
                   
Summary:                  
Classes: 46.67% (7/15)   
Methods: 79.49% (31/39)  
Lines:   82.38% (159/193)
```

### Things to Improve

- **Dependency Injection**: Use a DI library like Symfony DI or Laravel Service Container to manage class dependencies instead of creating them manually. This will make the code easier to maintain and test.

- **Caching API Data**: Use a cache system like Redis to store API data. This will speed up the app by reducing repeated API calls.
