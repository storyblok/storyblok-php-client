# Changelog

## 2.6.3 - 2024-03-15
Release: [v2.6.3](https://github.com/storyblok/storyblok-php-client/releases/tag/v2.6.3)
- Update Symfony Cache 7 library for Symfony 7 compatibility

## 2.6.2 - 2023-12-08
Release: [v2.6.2](https://github.com/storyblok/storyblok-php-client/releases/tag/v2.6.2)
- fix: set from_release parameter in getStories if release is set for client
- Update readme with new regions

## 2.6.1 - 2023-07-15
Release: [v2.6.1](https://github.com/storyblok/storyblok-php-client/releases/tag/v2.6.1)
Added agent headers to every request

## 2.6.0 - 2023-04-13
Release: [v2.6.0](https://github.com/storyblok/storyblok-php-client/releases/tag/v2.6.0)
Fixed level resolving stories

## 2.5.0 - 2023-02-14
Release: [v2.5.0](https://github.com/storyblok/php-client/releases/tag/v2.5.0)

### Added
- Add **getAll()** method function for retrieving all the entries (automatic pagination resolution) thanks to @web-dev-passion
- Add **default_lifetime** option as TTL value for caching
- Code quality: added **static code analysis** tool (PHPStan)
- Refactoring for achieving **PHPStan level 5**
- **Add test for Resolve Relations**, check sorting and check resolver (added specific dataset)

### Changed
- Internal Refactoring (checking hit/miss cache)

## 2.4.0 - 2022-12-15
Release: [v2.4.0](https://github.com/storyblok/php-client/releases/tag/v2.4.0)

### Added
- Added **PHP 8.2** compatibility, so now we are supporting from PHP 7.3 to PHP 8.2
- Start adding **integration tests**

### Changed
- For a better compatibility with the libraries, Apix Cache is replaced by **Symfony Cache**
- **Update documentation** in the README file

## 2.3.0 - 2022-08-24
Release: [v2.3.0](https://github.com/storyblok/php-client/releases/tag/v2.3.0)

### Added
- **Added Test suite**
- Added examples for using spaces in **US region**
- Added **tests** for tags, managing errors 
- Added **configuration** in composer.json for launching tasks for the code quality
- Added documentation for **Resolve Relations** functionality

### Changed
- Improved **documentation** in Readme file for datasource
- **Clean source** codes, eliminating hidden fields
- Removed support for PHP 5.6 to 7.2
- Fixed: Language is not respected if a single story should be fetched  #44
