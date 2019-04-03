# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

(nothing yet)

## [0.7.0] - 2019-04-03

### Changed
- Update from httplug to PSR-17/PSR-18. With PSR-17 follows the requirement of a HTTP factory implementation.
  Run `composer require http-interop/http-factory-guzzle` to continue using sru-client as before.

- Remove support for PHP 5.6 and 7.0 because we updated the tests to work with PHPUnit 7+
  and can no longer run tests on PHP < 7.1. Also, PHP 5.6 and PHP 7.0 have now reached end-of-life.

## [0.6.4] - 2019-03-06
### Fixed
- Fix a few type hints.
- Minor code style cleanups.

## [0.6.3] - 2017-08-01
### Added
- Add support for setting custom headers.

### Changed
- Don't throw `ClientErrorException` on 4XX responses, since some servers return 4XX responses whenever there is a diagnostic message, including zero result queries. Diagnostic messages are better handled by our `Response` class.

## [0.6.2] - 2017-08-01
### Added
- Add `__toString()` method to `Record` to allow simple string serialization.

## [0.6.1] - 2017-08-01
### Changed
- Update from quitesimplexmlelement 0.x to 1.x.

## [0.6.0] - 2017-07-01
### Changed
- Replace hard dependency on Guzzle with HTTPlug client discovery.
  To continue use Guzzle, run `composer require php-http/guzzle6-adapter`

[Unreleased]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.7.0...HEAD
[0.7.0]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.6.4...v0.7.0
[0.6.4]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.6.3...v0.6.4
[0.6.3]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.6.2...v0.6.3
[0.6.2]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.6.1...v0.6.2
[0.6.1]: https://github.com/olivierlacan/keep-a-changelog/compare/v0.6.0...v0.6.1
[0.6.0]: https://github.com/olivierlacan/keep-a-changelog/releases/tag/v0.6.0