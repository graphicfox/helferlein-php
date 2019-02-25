# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]
## [1.0.5] - 2019-02-25
### Added
- Added FilesAndFolders helper to make some repeating tasks easier
- Added a relativePath() implementation to PathsAndLinks

### Changed
- BREAKING: Removed the $options parameter from the Inflector::toArray(). I adapted the simpler "intelligentSplitting" option instead and applied all the bugfixes I did to the Helferlein javascript counterpart.

## [1.0.4] - 2019-02-06
### Changed
- Cookies will no longer use serialize() but json_encode to store the data

## [1.0.3] - 2019-01-29
### Added
- Added "callable" type check to Options::make()
- Added documentation to EventBus implementation

### Changed
- Inflector::toSlug() now returns lowercase only strings

### Fixed
- Fixed some issues with Options::make() where the input types of objects where generated incorrectly
- Fixed some issues with the old implementation of Options::make()

## [1.0.2] - 2019-01-28
### Added
- Added ArrayGenerator to Arrays as replacement for x::castArray()
- Added Arrays::sortBy()
- Added Arrays::getSimilarKey()
- Added HelferleinNotImplementedException
- Added the remaining methods to Inflector class
- Added PathsAndLinks as a mixture of different x utilities. It handles everything to do with (file)path's and url's.

### Changed
- Rewrote Options::make() to a more advanced version as a much slimmer and easier to understand replacement of the ArraySchema stuff in the x package
- Moved Cookies class into it's own namespace
- Moved DateAndtime class into it's own namespace

### Removed
- Removed caster

## [1.0.1] - 2019-01-25
### Added
- Added Cookies for easier access to cookie data
- Added EventBus from labor\EventHandling package
- Added Arrays from x::arrayUtilities
- Added (currently empty) Caster class
- Added early implementation of the new inflector
## [1.0.0] - 2019-01-18
### Added
- Initial commit added all basic files



The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).
