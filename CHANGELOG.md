# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 0.19.1
### Added
- Declared Nextcloud 32–35 compatibility.
- Weekly API smoke checks against supported Nextcloud versions and the development branch.
- Regression tests for legacy XML and current JSON admin settings responses.

### Fixed
- Load admin settings when the Nextcloud app configuration API returns JSON.
- Use the PSR container `get()` method during application boot.

### Changed
- Use the SPDX license identifier in app metadata.

## 0.19.0
### Added
- Nextcloud 29 support
- Nextcloud 30 support
- Nextcloud 31 support, please report any issues

## 0.18.0
### Added
- Nextcloud 27 support
- Nextcloud 28 support

### Changed
- Translations update

## 0.17.0
### Fixed
- Not all rooms visible in long room list
- Room list button tooltips ([#22](https://github.com/nextcloud/jitsi/issues/22))
- Translation of „Copy to clipboard“ button tooltip ([#23](https://github.com/nextcloud/jitsi/pull/23))

### Changed
- Updated translations

## 0.16.1
### Fixed
- PHP 7.4 compatibility

## 0.16.0
### Changed
- Nextcloud 25 support
- Improved „join with app“ buttons
- Updated translations

## 0.15.0
### Added
- Greek translation

### Fixed
- Browser version 100 support

### Changed
- Nextcloud code-style applied
