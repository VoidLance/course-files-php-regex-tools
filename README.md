# PHP Regex Tools

Two small, browser-based PHP tools for learning and experimenting with regular expressions:

- **[Regex Matcher](regex_matcher.php)** tests a pattern against sample text, shows every full match and its position, and includes a quick-reference cheat sheet.
- **[Email Validator](email_validator.php)** checks multiple email addresses and explains invalid results. Optional strict mode applies additional checks to usernames and domain lengths.

Both tools are self-contained and use PHP's built-in regular-expression support. They are intended as learning aids; the email validator applies a simplified set of rules and is not a definitive check that an address exists or can receive mail.

## Get started

### Requirements

- PHP with the standard PCRE functions enabled
- A web browser

There are no Composer packages, database, or other external dependencies.

### Run locally

From the project directory, start PHP's built-in development server:

```sh
php -S 127.0.0.1:8000
```

Open either page in your browser:

- [http://127.0.0.1:8000/regex_matcher.php](http://127.0.0.1:8000/regex_matcher.php)
- [http://127.0.0.1:8000/email_validator.php](http://127.0.0.1:8000/email_validator.php)

Press **Ctrl+C** in the terminal to stop the server. For deployment, serve the PHP files using a PHP-enabled web server; the built-in server is intended for local development.

## Using the tools

### Regex Matcher

Enter sample text and a regular-expression pattern body (without delimiters), then choose an optional modifier. For example, enter `cat|dog` with the `i` modifier to find either word without regard to case. Results include the number of matches and each match's position in the input. The page also describes the supported modifiers and common pattern syntax.

### Email Validator

Enter one address per line and select **Strict mode** to enable additional checks. For example:

```text
alice@example.com
bob.smith@my-domain.org
```

The results show each address, a validity status, a reason when it fails, and summary counts.

## Help and contributing

For questions or bug reports, [open an issue](https://github.com/VoidLance/course-files-php-regex-tools/issues). Contributions can be proposed as pull requests to this repository. There is no separate `CONTRIBUTING.md` or project documentation site at this time.

The repository does not identify an individual maintainer. It is maintained through contributions to [VoidLance/course-files-php-regex-tools](https://github.com/VoidLance/course-files-php-regex-tools).

## Development

Check PHP syntax with:

```sh
php -l regex_matcher.php
php -l email_validator.php
```

No automated test suite, build system, or linter is currently configured.

## License

No `LICENSE` file is currently present. Check with the repository owner before reusing or redistributing this project.
