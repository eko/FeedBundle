FeedBundle - A bundle to build RSS/Atom feeds from entities
=========================================================

[![Build Status](https://secure.travis-ci.org/eko/FeedBundle.png?branch=master)](http://travis-ci.org/eko/FeedBundle)

Features
--------

 * Generate XML feeds (RSS & Atom formats)
 * Items based on your entities
 * Easy to configurate & use
 * Available on packagist (to install via composer)

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

Each entities you will use to generate an RSS feed needs to implement the `Eko\FeedBundle\Item\ItemInterface` interface as follow in this example for an `Article` entity of a blog:

```php
<?php

namespace Bundle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eko\FeedBundle\Item\ItemInterface;

/**
 * Bundle\BlogBundle\Entity\Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity
 */
class Article implements ItemInterface
{
```

In this same entity, just implement those required methods:

 * `public function getFeedItemTitle() { … }` : this method returns entity item title
 * `public function getFeedItemDescription() { … }` : this method returns entity item description (or content)
 * `public function getFeedItemLink() { … }` : this method returns entity item link (URL)
 * `public function getFeedItemPubDate() { … }` : this method returns entity item publication date

### 3) Generate the feed!

The action now takes place in your controller. Just declare a new action with those examples lines:

```php
<?php

namespace Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BlogController extends Controller
{
    /**
     * Generate the article feed
     *
     * @Route("/feed", name="feed")
     * @Template()
     * @Cache(expires="+2 days")
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
$feed = $this->get('eko_feed.feed.manager')->get('article');
$feed->add(new FakeEntity());
$feed->addField(new Field('fake_custom', 'getFeedItemCustom'));
```

Of course, `getFeedItemCustom()` method needs to be declared in your entity.

Moreover, entities objects can be added separatly with add method:

```php
$feed = $this->get('eko_feed.feed.manager')->get('article');
$feed->add($article);
```

For any question, do not hesitate to contact me and/or participate.

Authors
-------

 * Vincent Composieux <vincent.composieux@gmail.com> (Twitter: @vcomposieux)
 * Anyone want to contribute ? Do not hesitate, you will be listed here!
