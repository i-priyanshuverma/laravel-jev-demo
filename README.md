# Laravel Jev Demo Application

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE)

A hands-on reference application demonstrating real-world implementations of the **[laravel-jev](https://github.com/i-priyanshuverma/laravel-jev)** package in a Laravel 12 codebase.

It provides an interactive web operations console, a companion Artisan CLI tool, and a test suite showcasing sub-100ms semantic decisions, categorical routing, single round-trip batching, and form validation.

---

## Application Features

### 1. Operations Console (`/`)
A web workbench providing live evaluation against realistic incoming event streams:
- **Support Triage**: Evaluates inbound tickets, flags spam, routes to departments (`billing`, `technical_support`, `sales`, `security_incident`), and scores urgency and customer frustration.
- **Sales Lead Qualification**: Detects enterprise buyer intent, assigns tiers (`enterprise_account`, `mid_market`, `self_serve_starter`), and estimates deal scale.
- **Review Moderation**: Flags toxic or defamatory reviews, categorizes topics, and rates sentiment on a 5-point scale.
- **Batch Evaluation**: Evaluates multiple dimensions in a single HTTP request using `Jev::analyze()`.
- **Latency Tracker**: Displays real-time round-trip execution latency for each decision.
- **Code Inspector**: Displays the underlying PHP code executed for each scenario.

### 2. Custom Rule Sandbox
An interactive tool within the console to test arbitrary criteria:
- Boolean decisions with adjustable confidence thresholds (`Jev::is`).
- Categorical selection from custom comma-separated options (`Jev::choose`).
- Numerical scoring against custom ordered levels (`Jev::score`).

### 3. Native Form Request Validation
An interactive feedback form backed by `FeedbackSubmissionRequest`, demonstrating real-time validation via `JevRule::not()` and `JevRule::is()`.

### 4. Interactive Terminal CLI
A companion Artisan command (`php artisan jev:demo`) built with Laravel Prompts for evaluating scenarios and custom rules directly in the terminal.

---

## Project Structure

This application demonstrates clean architectural patterns for integrating Jev into a Laravel application:

```text
app/
├── Console/Commands/
│   └── JevDemoCommand.php             # Interactive terminal walkthrough (php artisan jev:demo)
├── Http/
│   ├── Controllers/
│   │   └── TriageController.php       # JSON API endpoints for the web console
│   └── Requests/
│       └── FeedbackSubmissionRequest.php # Form Request with JevRule validation
└── Services/
    └── TriageService.php              # Service layer encapsulating Jev decision logic & fakes
resources/views/
└── triage.blade.php                   # Single-file operations console UI (vanilla CSS & JS)
tests/Feature/
└── TriageTest.php                     # 11 automated feature tests covering all scenarios
```

---

## Getting Started

### Requirements
- PHP 8.2+
- Composer

### Installation & Run

```bash
git clone https://github.com/i-priyanshuverma/laravel-jev-demo.git
cd laravel-jev-demo

composer install
php artisan serve
```

Open `http://localhost:8000` to access the operations console.

### Running in the Terminal

```bash
php artisan jev:demo
```

### Running the Test Suite

```bash
php artisan test
```

Check code style formatting:

```bash
vendor/bin/pint --test
```

---

## Simulation vs. Live Mode

By default, the application runs with an offline simulation driver (`Jev::fake()`), configured in `TriageService`. This allows exploring the console, running the CLI, and executing the test suite without an external API key.

To connect to the live TypeSafe Jev API:
1. Obtain an API key from [TypeSafe](https://typesafe.ai).
2. Update your `.env`:
   ```env
   JEV_API_KEY=your_typesafe_api_key_here
   JEV_SIMULATE=false
   ```

---

## Core Package

For package documentation, configuration options, and installation instructions for your own applications, visit the **[Laravel Jev Repository](https://github.com/i-priyanshuverma/laravel-jev)** (`i-priyanshuverma/laravel-jev`).

---

## License

This demo application is open-sourced software licensed under the [MIT license](LICENSE).
