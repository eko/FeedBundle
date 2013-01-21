<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Feed;

use Eko\FeedBundle\Item\Reader\ItemInterface;

use Zend\Feed\Reader\Reader as ZendReader;
use Zend\Feed\Reader\Feed\FeedInterface;

/**
 * Reader
 *
 * This is the main feed reader class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class Reader
{
    /**
     * @var FeedInterface
     */
    protected $feed;

    /**
     * Loads feed from an url or file path
     *
     * @param string $file
     *
     * @return Reader
     */
    public function load($file)
    {
        if (file_exists($file)) {
            $this->feed = ZendReader::importFile($file);
        } else {
            $this->feed = ZendReader::import($file);
        }

        return $this;
    }

    /**
     * Get feed object previously loaded
     *
     * @return FeedInterface
     */
    public function get()
    {
        $this->checkIfFeedIsLoaded();

        return $this->feed;
    }

    /**
     * Populate entities with feed entries
     *
     * @param string $entityName
     *
     * @return array
     *
     * @throws \Exception if entity does not exists or does not implement Reader\ItemInterface
     */
    public function populate($entityName)
    {
        $this->checkIfFeedIsLoaded();

        if (!class_exists($entityName)) {
            throw new \Exception(sprintf('Entity %s does not exists.'));
        }

        $entity = new $entityName();

        if (!$entity instanceof ItemInterface) {
            throw new \Exception(sprintf('Entity "%s" does not implement required %s.', $entityName, 'Eko\FeedBundle\Item\Reader\ItemInterface'));
        }

        $items = array();

        foreach ($this->feed as $entry) {
            $entity = new $entityName();

            $entity->setFeedItemTitle($entry->getTitle());
            $entity->setFeedItemDescription($entry->getContent());
            $entity->setFeedItemLink($entry->getLink());
            $entity->setFeedItemPubDate($entry->getDateModified());

            $items[] = $entity;
        }

        return $items;
    }

    /**
     * Check if a feed is currently loaded
     *
     * @throws \Exception if there is no feed loaded
     */
    protected function checkIfFeedIsLoaded()
    {
        if (null === $this->feed) {
            throw new \Exception('There is not feed loaded. Please make sure to load a feed before using the get() method.');
        }
    }
}
