FeedBundle - A bundle to build RSS/Atom feeds from entities
=========================================================

[![Build Status](https://secure.travis-ci.org/eko/FeedBundle.png?branch=master)](http://travis-ci.org/eko/FeedBundle)

Features
--------

 * Generate XML feeds (RSS & Atom formats)
 * Items based on your entities
 * Easy to configurate & use
 * Available on packagist (to install via composer)
 * Dump your feeds into a file via a Symfony console command

Installation
-----------------------------------

Add this to deps
```
[EkoFeedBundle]
    git=https://github.com/eko/FeedBundle.git
    target=/bundles/Eko/FeedBundle
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

Go further with your feeds
--------------------------

### Add some custom fields

You can add custom fields for your entities nodes by adding them this way:

```php
<?php
$feed = $this->get('eko_feed.feed.manager')->get('article');
$feed->add(new FakeEntity());
$feed->addField(new Field('fake_custom', 'getFeedItemCustom'));
```

Of course, `getFeedItemCustom()` method needs to be declared in your entity.

Moreover, entities objects can be added separatly with add method:

```php
<?php
$feed = $this->get('eko_feed.feed.manager')->get('article');
$feed->add($article);
```

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
</table>

An example with all the options:

`php app/console eko:feed:dump --name=article --entity=AcmeDemoBundle:Fake --filename=test.xml --format=atom --orderBy=id --direction=DESC`

This will result:
```
Start dumping "article" feed from "AcmeDemoBundle:Fake" entity...
done!
Feed has been dumped and located in "/Users/vincent/dev/perso/symfony/web/test.xml"
```

For any question, do not hesitate to contact me and/or participate.

Contributors
------------

 * Vincent Composieux <vincent.composieux@gmail.com> (Twitter: @vcomposieux)
 * Rob Masters <mastahuk@gmail.com>

 * Anyone want to contribute ? Do not hesitate, you will be listed here!

