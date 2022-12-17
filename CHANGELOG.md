# Changelog

## 0.2.5 - WIP

### Added

- Add **getAll()** method function for retrieving all the entries (automatic pagination resolution) thanks to @web-dev-passion
- Add **default_lifetime** option as TTL value for caching
- Code quality: added **static code analysis** tool (PHPStan)
- Refactoring for achieving **PHPStan level 2**
- **Add test for Resolve Relations**, check sorting and check resolver (added specific dataset)


## 0.2.4 - 2022-12-15

### Added
- Added **PHP 8.2** compatibility, so now we are supporting from PHP 7.3 to PHP 8.2
- Start adding **integration tests**

### Changed
- For a better compatibility with the libraries, Apix Cache is replaced by **Symfony Cache**
- **Update documentation** in the README file

## 0.2.3 - 2022-08-24

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
