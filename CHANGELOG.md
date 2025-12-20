CHANGELOG
=========

### 2025-01-XX

* Added Symfony 8.0 and PHP 8.4 compatibility
* [BC BREAK] Dropped PHP 7.x support (PHP 7.4 reached end-of-life in November 2022)
* Migrated service definitions from XML to YAML format (required for Symfony 8 compatibility, but YAML works with all supported Symfony versions)
* Modernized `FeedDumpCommand` to use `AsCommand` attribute instead of deprecated `$defaultName` property
* Updated CI workflow to test against PHP 8.1, 8.2, 8.3, 8.4 and Symfony 8.0.x
* Minimum PHP version requirement changed from `^7.2 || ^8.0` to `^8.0`

### 2013-07-13

* [BC BREAK] We have introduced the `ChannelField` to add custom fields to your feed's main channel
   so we have moved the `\Eko\FeedBundle\Item\Field` class to `\Eko\FeedBundle\Field\ItemField`.