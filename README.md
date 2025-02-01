## Commission Calculator

### Requirements

- PHP (version 8.3 or higher)
- Extensions: `ext-bcmath`
- Docker (optional, for containerized setup)

### Steps to Run the Project

**Test project within docker:**

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

### About the project

#### Libraries Used

- **phpdotenv**: Loads environment variables from a .env file.
- **Mockery**: A mock object framework for testing.
- **Guzzle**: A PHP HTTP client for making HTTP requests. Implemented an exponential backoff strategy for failed requests in class: `Services/AbstractHttpService.php`.

#### Commission Calculation Architecture

This architecture is designed to handle commission calculations dynamically and is extensible for adding more rules in the future. It consists of the following components:

- **Commission Calculators**: These implement a common interface for calculating commissions based on different rules.
- **Base Amount Resolvers**: These implement a resolver interface to determine the base amount used by the calculators.
- **Commission Handler**: This combines a calculator and a resolver, managing the commission calculation process.
- **Registry**: It loads the handlers based on configuration.
- **Config File**: This file configures each handler with the necessary calculator and an optional resolver.

At runtime, the appropriate handler is selected based on operation parameters, allowing flexible commission calculations. The design also allows for easy extension to add new commission calculation rules in the future.
