FeedBundle - A Symfony bundle to build RSS feeds from your entities
===================================================================

CURRENTLY IN DEVELOPMENT, CAN'T BE USED.

Version 1.0

Features
--------

 * Generate RSS feed
 * Based on your entities
 * Easy to use

Configuration
-------------

### Edit app/config.yml

```
    ekofeed:
        feeds:
            article:
                title:       'My articles/posts'
                description: 'Latests articles'
                link:        'http://vincent.composieux.fr'
                entity:      MyBundle\Entity\Article
```

Author :
-------------

 * Vincent Composieux <vincent.composieux@gmail.com> (Twitter: @vcomposieux)