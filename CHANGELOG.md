# Changelog

## Unreleased

### Changed
- Removed legacy page from sidebar nav.

## 3.1.1 - 2023-10-26

### Changed
- Denote when in “read only” mode. ([#22](https://github.com/doublesecretagency/craft-inventory/issues/22))

### Fixed
- Improved handling of field deletion.

## 3.1.0 - 2023-06-30

### Fixed
- Fixed a major issue which allowed false negatives to be reported. ([#17](https://github.com/doublesecretagency/craft-inventory/issues/17))

## 3.0.3 - 2023-05-01

### Changed
- Updated repo URLs.

## 3.0.2 - 2023-04-26

### Changed
- Updated README images and description.

### Fixed
- Fixed a bug in multi-site compatibility. ([#21](https://github.com/doublesecretagency/craft-inventory/issues/21))

## 3.0.1 - 2023-04-25

### Changed
- New plugin icon!

### Fixed
- Fixed a bug occurring when disabled plugins leave behind orphaned element types.
- Fixed a bug occurring when changing field groups on a multisite project.
- Fixed a bug which caused CLI commands to fail. (thanks @Jensderond)

## 3.0.0 - 2023-04-24

### Changed
- Moved everything over to **Utilities > Field Inventory**.
- Automatically redirect to Field Inventory utility when the plugin is installed.

### Fixed
- Fixed bug caused by recently deleted Global Sets. ([#18](https://github.com/doublesecretagency/craft-inventory/issues/18))

## 2.2.0 - 2022-03-26

### Added
- Craft 4 compatibility.

## 2.1.1 - 2020-08-21

### Fixed
- Be more defensive against missing field layouts.

## 2.1.0 - 2020-04-10

### Added
- Added ability to delete unused fields.
- Clearly denotes soft-deleted sections.
- Added support for Ad Wizard elements.

### Changed
- Re-styled for Craft 3.4.
- Changed minimum requirement to Craft 3.4.

## 2.0.3 - 2019-06-07

### Fixed
- Fixed bug preventing PostgreSQL compatibility.

## 2.0.2 - 2018-07-16

### Changed
- Properly mark orphaned layouts, instead of throwing an error.

## 2.0.1 - 2018-05-21

### Fixed
- Proper handling of invalid field layouts.

## 2.0.0 - 2017-12-15

### Added
- Craft 3 compatibility.
- Added “Translatable” column.
- Added tab identification for User fields and Global Set fields.

## 1.0.1 - 2016-05-04

### Changed
- Improved UI.

## 1.0.0 - 2016-05-03

Initial release.
