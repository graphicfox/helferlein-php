# Change Log

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [1.14.1](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.14.1%0Dv1.14.0#diff) (2019-11-21)


### Bug Fixes

* **DateAndTime:** rename formatDateAndtime to formatDateAndTime ([7dc7d1b](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/7dc7d1b))
* **DateAndTime:** Use the 24h clock by default as time format ([a865043](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/a865043))



# [1.14.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.14.0%0Dv1.13.0#diff) (2019-11-16)


### Features

* **Options:** try to handle wrong boolean flags using a more speaking error message ([e4d96c9](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/e4d96c9))



# [1.13.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.13.0%0Dv1.12.1#diff) (2019-10-25)


### Features

* **Options:** rework the inner logic of the option applier to allow nested option application by making the applier class stateless ([4a68a55](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/4a68a55))



## [1.12.1](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.12.1%0Dv1.12.0#diff) (2019-09-25)


### Bug Fixes

* **FilesAndFolders:** make sure writeFile() does handle the writing of empty files correctly ([654ee1c](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/654ee1c))
* **PathsAndLinks:** make sure absolutePath strips slashes before exploding the path ([5b8df79](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/5b8df79))



# [1.12.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.12.0%0Dv1.11.0#diff) (2019-09-09)


### Bug Fixes

* **ArrayGenerator:** make sure that arrays from string list's that receive "0" don't return an empty array, but an array like [0]. ([6b731a5](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/6b731a5))


### Features

* **PathsAndLinks:** add new absolutePath() helper to resolve a relative path using a base path. ([cae30cb](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/cae30cb))



# [1.11.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.11.0%0Dv1.10.0#diff) (2019-09-03)


### Features

* **FilesAndFolders:** make sure that the directory exists before writing a file ([3de1f79](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/3de1f79))
* **PathsAndLinks:** make sure to always create a fresh url to incorporate server/get array changes. ([abd92a5](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/abd92a5))



# [1.10.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.10.0%0Dv1.9.0#diff) (2019-08-14)


### Bug Fixes

* **Inflector:** make sure the sanitization of getter and setter methods can be disabled ([4e7dcbb](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/4e7dcbb))


### Features

* **Link:** add option to merge two links into each other ([060ab9b](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/060ab9b))
* **PathsAndLinks:** more reliable url parsing in link factory ([a8791a3](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/a8791a3))



# [1.9.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.9.0%0Dv1.8.0#diff) (2019-08-06)


### Features

* **Events:** implement lazy event subscriptions ([b6aa09a](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/b6aa09a))
* **Events:** implement lazy event subscriptions and event priority ([15f6988](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/15f6988))



# [1.8.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.8.0%0Dv1.7.4#diff) (2019-07-29)


### Features

* **Options:** rewrite for the options logic to use a object based approach ([f412f5f](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/f412f5f))



## [1.7.4](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.7.4%0Dv1.7.3#diff) (2019-07-22)


### Bug Fixes

* **Arrays:** minor bugfixes and adjustments ([d9f5d83](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/d9f5d83))



## [1.7.3](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.7.3%0Dv1.7.2#diff) (2019-07-21)


### Bug Fixes

* **Cookies:** fix not working remove cookies ([501fde9](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/501fde9))



## [1.7.2](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.7.2%0Dv1.7.1#diff) (2019-07-19)


### Bug Fixes

* **Inflector:** fix some notices of unknown array keys ([6930019](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/6930019))



## [1.7.1](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.7.1%0Dv1.7.0#diff) (2019-07-17)


### Bug Fixes

* **Arrays:** Make sure arrayPath walker returns a reference ([4742dbd](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/4742dbd))



# [1.7.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.7.0%0Dv1.6.1#diff) (2019-07-16)


### Bug Fixes

* **EventBus:** make sure initialized property in the event class is used ([b08d720](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/b08d720))
* **Options:** make sure we print the correct label when throwing an exception for a unknown bool flag ([4d8fb0c](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/4d8fb0c))


### Features

* **Arrays:** add sortByKeyStrLen() helper ([7f9875a](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/7f9875a))
* **FilesAndFolders:** writing a file: try to write files by writing a temp file and overriding the target file to avoid half-written read requests ([e63dbb3](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/e63dbb3))



## [1.6.1](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.6.1%0Dv1.6.0#diff) (2019-07-04)


### Bug Fixes

* **FilesAndFolders:** Additional failsaves ([effefb1](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/effefb1))



# [1.6.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.6.0%0Dv1.5.2#diff) (2019-07-04)


### Bug Fixes

* **ArrayPaths:** fix issue where the cached path was not sensitive to the configured separator ([b696ddd](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/b696ddd))


### Features

* **ArrayGenerator:** Array from xml now can also decode xml strings that don't start with the opening <?xml... tag ([ebc0cbb](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/ebc0cbb))
* **Arrays:** add new sortByStrLen() helper to sort an array of strings according to their string length ([50756a7](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/50756a7))



## [1.5.2](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.5.2%0Dv1.5.1#diff) (2019-06-11)


### Bug Fixes

* **FilesAndFolders:** add clearstatcache() calls to deleting methods ([4e6618a](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/4e6618a))



## [1.5.1](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.5.1%0Dv1.5.0#diff) (2019-05-28)


### Bug Fixes

* **FilesAndFolders:** try to fix weired issues where files that don't exist, exist according to php and vice versa... ([52d0d3a](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/52d0d3a))



# [1.5.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.5.0%0Dv1.4.0#diff) (2019-05-28)


### Bug Fixes

* **ArrayDumper:** make sure the content of xml nodes is encoded using htmlspecialchars() ([3be45d2](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/3be45d2))


### Features

* add FormatAndConvert as group of misc formatter and converter functions ([3dacc4f](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/3dacc4f))



# [1.4.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.4.0%0Dv1.3.0#diff) (2019-05-27)


### Bug Fixes

* **ArrayDumper:** More speaking path when printing xml errors ([287e74e](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/287e74e))


### Features

* **FilesAndFolders:** add additional helpers to read and write files as well as setting permissions ([3ce2092](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/3ce2092))



# [1.3.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.3.0%0Dv1.2.0#diff) (2019-05-23)


### Bug Fixes

* **ArrayPaths:** add missing $walker variable in _set() method ([0ecc6f9](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/0ecc6f9))
* **Options:** add missing quote ([e31526a](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/e31526a))


### Features

* add additional methods for insertAt(), without() and unflatten() ([3f80fc6](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/3f80fc6))
* add methods to dump arrays into different (currently only xml) string formats ([8649b11](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/8649b11))
* **ArrayGenerator:** changed the way how xml arrays are created to implement a more speaking output ([9e803ea](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/9e803ea))
* **FilesAndFolders:** add directoryIterator method and implement it into flushDirectory() ([1a457e2](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/1a457e2))



# [1.2.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.2.0%0Dv1.1.0#diff) (2019-05-18)


### Features

* add Arrays::flatten and Arrays::unflatten helpers ([2002c89](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/2002c89))



# [1.1.0](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.1.0%0Dv1.0.9#diff) (2019-03-25)


### Bug Fixes

* **ArrayGenerator:** String lists may now also be created from numeric values ([d93ef64](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/d93ef64))
* **Inflector:** made toUuid static as it should be ([a5155da](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/a5155da))
* **Link:** query can now be set to NULL ([03044bb](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/03044bb))
* **Options:** closures are now valid against the "callable" pseudo-type ([614aea9](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/614aea9))
* **PathsAndLinks:** classNamespace() no longer returns "." when there is no namespace ([fc8164b](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/fc8164b))


### Features

* **Events:** Event objects can now be easily instantiated outside the event bus ([ba62409](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/ba62409))


### Performance Improvements

* Arrays::merge walker will now skip if one of the given arrays is empty ([c22f905](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/c22f905))



## [1.0.9](https://bitbucket.org/labor-digital/labor-library-helferlein-php/branches/compare/v1.0.9%0Dv1.0.8#diff) (2019-03-19)



# 1.0.8 (2019-03-19)


### Features

* add new definition of valid "values" to Options::make ([bfd510e](https://bitbucket.org/labor-digital/labor-library-helferlein-php/commits/bfd510e))



## [1.0.7] - 2019-02-28
### Added
- Added concept of "boolean flags" to Options::make()
- Added better alternative for link generation in PathsAndLinks::getLink();

### Changed
- BREAKING: Removed parseLink() from PathsAndLinks class

### Fixed
- Fixed some typos
- Removed some unused variables

## [1.0.6] - 2019-02-25
### Fixed
- Removed some invalid @return statements from EventBusInterface

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
