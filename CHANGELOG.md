# Change Log
## [0.8.0] - 2026-03-28
### Added
- Laravel 11, 12, and 13 support.
- PHP 8.2, 8.3, 8.4, and 8.5 support.
- GitHub Actions CI workflow.
- Unit tests for service provider registration.

### Changed
- Modernized PHPUnit configuration for PHPUnit 11.
- Modernized service provider (removed deprecated `$defer` and `provides()`).
- Migrated from Travis CI to GitHub Actions.

### Removed
- Laravel 10 and earlier support.
- PHP 8.1 and earlier support.
- Unused BrowserKit/Dusk test infrastructure.

## [0.7.0] - 2020-02-29
### Added
- Laravel 7 compatibility

## [0.5.7] - 5 Oct 2018
### Added
- Laravel 5.7 compatibility.

## [0.5.1] - 10 Feb 2018
### Added
- Laravel 5.6 compatibility.

## [0.5.0] - 14 Oct 2017
### Added
- test stubs and required dependencies.
- badges to README.

### Changed
- package namespace to genealabs/laravel-maps.
- and cleaned up code, did some refactoring.

## [0.3.8 - 0.3.9] - 2016-08-07
### Added
- automatic setting of apiKey from `config('services.google.maps.api-key')`.

### Removed
- deprectated 'sensor' querystring parameter, which would trigger a "SensorNotRequired" warning.

## [0.3.5 - 0.3.7] - 2016-01-11
### Added
- clusterstyles (thanks to @MichaelGollinski).

### Changed
- script tag 'src' attributes to always referr to 'https' targets.

### Removed
- options to manually choose http or https.

## [0.3.4] - 2015-12-11
### Fixed
- class name conflicts.

### Removed
- auto-loading of Facade to avoid conflicts.

## 0.3.0 - 0.3.3: 25 Jun 2015
### Added
- License
- Change Log

### Changed
- Namespace from Appitventures to GeneaLabs.
- Installation instructions, affected areas of README.
