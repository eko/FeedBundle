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

use Eko\FeedBundle\Hydrator\HydratorInterface;

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
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * Sets entity hydrator service
     *
     * @param HydratorInterface $hydrator
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

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
     * Populate entities with feed entries using hydrator
     *
     * @param string $entityName
     *
     * @return array
     *
     * @throws \RuntimeException if entity name does not exists
     */
    public function populate($entityName)
    {
        if (!class_exists($entityName)) {
            throw new \RuntimeException(sprintf('Entity %s does not exists.', $entityName));
        }

        $feed = $this->get();

        return $this->hydrator->hydrate($feed, $entityName);
    }

    /**
     * Check if a feed is currently loaded
     *
     * @throws \RuntimeException if there is no feed loaded
     */
    protected function checkIfFeedIsLoaded()
    {
        if (null === $this->feed) {
            throw new \RuntimeException('There is not feed loaded. Please make sure to load a feed before using the get() method.');
        }
    }
}
