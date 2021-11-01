# Changelog

All notable changes to `LaravelFinder` will be documented in this file.

## Version 1.0
- Initial release
## Version 1.0.1
- PHP 8 is required.
## Version 1.1.0
- Fixed an error in the name of the containing folder when a Finder is created without specifying a model and at the same time the singular folder name, derived from the finder name, does not correspond to any existing model.
- The finders are instantiated, the static methods are only to call internally a constructor.
- Global filters can be applied.
