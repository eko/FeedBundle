CHANGELOG
=========

### 2013-07-13

* [BC BREAK] We have introduced the `ChannelField` to add custom fields to your feed's main channel
   so we have moved the `\Eko\FeedBundle\Item\Field` class to `\Eko\FeedBundle\Field\ItemField`

  Database Migration: (replace table name)

      ALTER TABLE page__page ADD type VARCHAR(255) DEFAULT NULL;
      ALTER TABLE page__snapshot ADD type VARCHAR(255) DEFAULT NULL;