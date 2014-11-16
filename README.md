FeedBundle - A bundle to build RSS/Atom feeds from entities
=========================================================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5620e128-834b-462c-b6fc-395609c57999/big.png)](https://insight.sensiolabs.com/projects/5620e128-834b-462c-b6fc-395609c57999)

[![Build Status](https://secure.travis-ci.org/eko/FeedBundle.png?branch=master)](http://travis-ci.org/eko/FeedBundle)
[![Latest Stable Version](https://poser.pugx.org/eko/feedbundle/version.png)](https://packagist.org/packages/eko/feedbundle)
[![Total Downloads](https://poser.pugx.org/eko/feedbundle/d/total.png)](https://packagist.org/packages/eko/feedbundle)

Features
--------

 * Generate XML feeds (RSS & Atom formats)
 * Easy to configure & use
 * Items based on your entities
 * Add groups of items
 * Add enclosure media tags
 * Translate your feed data
 * Read XML feeds and populate your Symfony entities
 * Dump your feeds into a file via a Symfony console command

Installation
-----------------------------------

Add the package to your composer.json file
```
"eko/feedbundle": "dev-master",
```

Add this to app/AppKernel.php
```php
<?php
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Eko\FeedBundle\EkoFeedBundle(),
        );

        ...

        return $bundles;
    }
```

Add this to app/autoload.php
```php
<?php
$loader->registerNamespaces(array(
   ...
    'Eko'             => __DIR__.'/../vendor/bundles'
));
```
Configuration (only 3 quick steps!)
-----------------------------------

### 1) Edit app/config.yml

The following configuration lines are required:

```yaml
eko_feed:
    hydrator: your_hydrator.custom.service # Optional, if you use entity hydrating with a custom hydrator
    translation_domain: test # Optional, if you want to use a custom translation domain
    feeds:
        article:
            title:       'My articles/posts'
            description: 'Latests articles'
            link:        'http://vincent.composieux.fr'
            encoding:    'utf-8'
            author:      'Vincent Composieux' # Only required for Atom feeds
```

### 2) Implement the ItemInterface

Each entities you will use to generate an RSS feed needs to implement `Eko\FeedBundle\Item\Writer\ItemInterface` or `Eko\FeedBundle\Item\Writer\RoutedItemInterface` as demonstrated in this example for an `Article` entity of a blog:

#### Option A: Eko\FeedBundle\Item\Writer\ItemInterface

```php
<?php

namespace Bundle\BlogBundle\Entity;

use Eko\FeedBundle\Item\Writer\ItemInterface;

/**
 * Bundle\BlogBundle\Entity\Article
 */
class Article implements ItemInterface
{
```

In this same entity, just implement those required methods:

 * `public function getFeedItemTitle() { … }` : this method returns entity item title
 * `public function getFeedItemDescription() { … }` : this method returns entity item description (or content)
 * `public function getFeedItemPubDate() { … }` : this method returns entity item publication date
 * `public function getFeedItemLink() { … }` : this method returns entity item link (URL)

#### Option B: Eko\FeedBundle\Item\Writer\RoutedItemInterface

Alternatively, if you need to make use of the router service to generate the link for your entity you can use the following interface. You don't need to worry about injecting the router to your entity.

```php
<?php

namespace Bundle\BlogBundle\Entity;

use Eko\FeedBundle\Item\Writer\RoutedItemInterface;

/**
 * Bundle\BlogBundle\Entity\Article
 */
class Article implements RoutedItemInterface
{
```

In this entity, you'll need to implement the following methods:

 * `public function getFeedItemTitle() { … }` : this method returns entity item title
 * `public function getFeedItemDescription() { … }` : this method returns entity item description (or content)
 * `public function getFeedItemPubDate() { … }` : this method returns entity item publication date
 * `public function getFeedItemRouteName() { … }` : this method returns the name of the route
 * `public function getFeedItemRouteParameters() { … }` : this method must return an array with the parameters that are required for the route
 * `public function getFeedItemUrlAnchor() { … }` : this method returns the anchor that will be appended to the router-generated url. *Note: can be an empty string.*


### 3) Generate the feed!

The action now takes place in your controller. Just declare a new action with those examples lines:

```php
<?php

namespace Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends Controller
{
    /**
     * Generate the article feed
     *
     * @return Response XML Feed
     */
    public function feedAction()
    {
        $articles = $this->getDoctrine()->getRepository('BundleBlogBundle:Article')->findAll();

        $feed = $this->get('eko_feed.feed.manager')->get('article');
        $feed->addFromArray($articles);

        return new Response($feed->render('rss')); // or 'atom'
    }
}
```

Please note that for better performances you can add a cache control.

Moreover, entities objects can be added separately with add method:

```php
<?php
$feed = $this->get('eko_feed.feed.manager')->get('article');
$feed->add($article);
```

Go further with your feeds
--------------------------

### Add some custom channel fields

You can add custom fields to main channel by adding them this way:

```php
<?php
$feed = $this->get('eko_feed.feed.manager')->get('article');
$feed->add(new FakeEntity());
$feed->addChannelField(new ChannelField('custom_name', 'custom_value'));
```

### Add some custom items fields

##### Add custom item fields

You can add custom items fields for your entities nodes by adding them this way:

```php
<?php
$feed = $this->get('eko_feed.feed.manager')->get('article');
$feed->add(new FakeEntity());
$feed->addItemField(new ItemField('fake_custom', 'getFeedItemCustom'));
```

Of course, `getFeedItemCustom()` method needs to be declared in your entity.

##### Add a group of custom item fields

You can also add group item fields using this way, if your method returns an array:

```php
<?php
$feed = $this->get('eko_feed.feed.manager')->get('article');
$feed->add(new FakeEntity());
$feed->addItemField(
    new GroupItemField('categories', new ItemField('category', 'getFeedCategoriesCustom'))
);
```

or even, multiple item fields in a group, like this:

```php
$feed->addItemField(
    new GroupItemField('author', array(
        new ItemField('name', 'getFeedItemAuthorName', array('cdata' => true)),
        new ItemField('email', 'getFeedItemAuthorEmail')
    )
);
```

##### Add a group of custom channel fields

As you can do for item fields, you can also add a custom group of channel fields like this:

```php
$feed->addChannelField(
    new GroupChannelField('author', array(
        new ChannelField('name', 'My author name'),
        new ChannelField('email', 'myauthor@email.org')
    )
);
```

##### Add custom media item fields

Media enclosure can be added using the `MediaItemField` field type as below:

```php
<?php
$feed = $this->get('eko_feed.feed.manager')->get('article');
$feed->add(new FakeEntity());
$feed->addItemField(new MediaItemField('getFeedMediaItem'));
```

The `getFeedMediaItem()` method must return an array with the following keys: type, length & value:

```php
/**
 * Returns a custom media field
 *
 * @return string
 */
public function getFeedMediaItem()
{
    return array(
        'type'   => 'image/jpeg',
        'length' => 500,
        'value'  => 'http://website.com/image.jpg'
    );
}
```

This media items can also be grouped using `GroupItemField`.

### Dump your feeds by using the Symfony console command

You can dump your feeds into a .xml file if you don't want to generate it on the fly by using the `php app/console eko:feed:dump` Symfony command.

Here are the options :
<table>
  <tr>
    <th>Option</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>--name</td>
    <td>Feed name defined in eko_feed configuration</td>
  </tr>
  <tr>
    <td>--entity</td>
    <td>Entity to use to generate the feed</td>
  </tr>
  <tr>
    <td>--filename</td>
    <td>Defines feed filename</td>
  </tr>
  <tr>
    <td>--orderBy</td>
    <td>Order field to sort by using findBy() method</td>
  </tr>
  <tr>
    <td>--direction</td>
    <td>Direction to give to sort field with findBy() method</td>
  </tr>
  <tr>
    <td>--format</td>
    <td>Formatter to use to generate, "rss" is default</td>
  </tr>
  <tr>
    <td>--limit</td>
    <td>Defines a limit of entity items to retrieve</td>
  </tr>
  <tr>
    <td>Host</td>
    <td>Defines the host base to generate absolute Url</td>
  </tr>
</table>

An example with all the options:

`php app/console eko:feed:dump --name=article --entity=AcmeDemoBundle:Fake --filename=test.xml --format=atom --orderBy=id --direction=DESC www.myhost.com`

This will result:
```
Start dumping "article" feed from "AcmeDemoBundle:Fake" entity...
done!
Feed has been dumped and located in "/Users/vincent/dev/perso/symfony/web/test.xml"
```

### Dump your feeds by using the eko_feed.feed.dump
You can dump your feeds by simply using the "eko_feed.feed.dump" service. Used by the dump command, you have the same value to set.
If you already have you items feed ready, you can dump it using the setItems().

```php
<?php

$feedDumpService = $this->get('eko_feed.feed.dump');
$feedDumpService
        ->setName($name)
        //You can set an entity
        //->setEntity($entity)
        // Or set you Items
        ->setItems($MyOwnItemList)
        ->setFilename($filename)
        ->setFormat($format)
        ->setLimit($limit)
        ->setRootDir($rootDir)
        ->setDirection($direction)
        ->setOrderBy($orderBy)
    ;

$feedDumpService->dump();
```


For any question, do not hesitate to contact me and/or participate.

### Read an XML feed and populate an entity

If you only want to read an XML feed, here is the way:

```php
<?php
$reader = $this->get('eko_feed.feed.reader');
$feed = $reader->load('http://php.net/feed.atom')->get();
```

`$feed` will be a `\Zend\Feed\Reader\Feed\FeedInterface` that you can manipulate.

--------------------------------------------------------------------------------

You can also populate an entity from an XML feed. This is very easy.

Just load the feed and call the populate method with your entity name which needs to implement `Eko\FeedBundle\Item\Reader\ItemInterface`, take a look on this example:

```php
<?php
$reader = $this->get('eko_feed.feed.reader');
$items = $reader->load('http://php.net/feed.atom')->populate('MyNamespace\Entity\Name');
```

In this example, `$items` will be an array that will contains an array with your entities populated with the given feed content.

### Use a custom hydrator to populate your entity

You can also write your own hydrator and use it this way:

```php
$reader = $this->get('eko_feed.feed.reader');
$reader->setHydrator(new MyCustomHydrator());

$items = $reader->load('http://php.net/feed.atom')->populate('MyNamespace\Entity\Name');
```

This way, your custom hydrator will be used instead of the `Eko\FeedBundle\Hydrator\DefaultHydrator`

### Define a custom feed formatter

You can define your own feed formatter by using the following tag:

```xml
<service id="acme.my_bundle.formatter.custom" class="Acme\MyBundle\Feed\Formatter\CustomFormatter">
    <tag name="eko_feed.formatter" format="custom"></tag>
</service>
```

Then, use it by simply calling `$feed->render('custom')`.

Contributors
------------

 * Vincent Composieux <vincent.composieux@gmail.com> (Twitter: @vcomposieux)
 * Rob Masters <mastahuk@gmail.com>
 * Thomas P <thomas@scullwm.com> (Twitter: @scullwm)

 * Anyone want to contribute ? Do not hesitate, you will be listed here!

