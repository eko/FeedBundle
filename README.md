FeedBundle - A bundle to build RSS/Atom feeds from entities
=========================================================

[![Build Status](https://secure.travis-ci.org/eko/FeedBundle.png?branch=master)](http://travis-ci.org/eko/FeedBundle)

Features
--------

 * Generate XML feeds (RSS & Atom formats)
 * Items based on your entities
 * Easy to configurate & use
 * Available on packagist (to install via composer)

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

Each entities you will use to generate an RSS feed needs to implement `Eko\FeedBundle\Item\ItemInterface` or `Eko\FeedBundle\Item\RoutedItemInterface` as demonstrated in this example for an `Article` entity of a blog:

#### Option A: Eko\FeedBundle\Item\ItemInterface

```php
<?php

namespace Bundle\BlogBundle\Entity;

use Eko\FeedBundle\Item\ItemInterface;

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

#### Option B: Eko\FeedBundle\Item\RoutedItemInterface

Alternatively, if you need to make use of the router service to generate the link for your entity you can use the following interface. You don't need to worry about injecting the router to your entity.

```php
<?php

namespace Bundle\BlogBundle\Entity;

use Eko\FeedBundle\Item\RoutedItemInterface;

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

For any question, do not hesitate to contact me and/or participate.

Contributors
------------

 * Vincent Composieux <vincent.composieux@gmail.com> (Twitter: @vcomposieux)
 * Rob Masters <mastahuk@gmail.com>

 * Anyone want to contribute ? Do not hesitate, you will be listed here!
