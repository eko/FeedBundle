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

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Routing\RouterInterface;

/**
 * FeedManager
 *
 * This class manage feeds specified in configuration file
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class FeedManager
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $formatters;

    /**
     * @var array
     */
    protected $feeds;

    /**
     * Constructor
     * 
     * @param RouterInterface $router     A Symfony router instance
     * @param array           $config     Configuration settings
     * @param array           $formatters Feed formatters list
     */
    public function __construct(RouterInterface $router, array $config, array $formatters)
    {
        $this->config     = $config;
        $this->router     = $router;
        $this->formatters = $formatters;
    }

    /**
     * Check if feed exists in configuration under 'feeds' node
     *
     * @param string $feed Feed name
     *
     * @return bool
     */
    public function has($feed) {
        return isset($this->config['feeds'][$feed]);
    }

    /**
     * Return specified Feed instance if exists
     *
     * @param string $feedName
     *
     * @return Feed
     *
     * @throws \InvalidArgumentException
     */
    public function get($feedName)
    {
        if (!$this->has($feedName)) {
            throw new \InvalidArgumentException(
                sprintf("Specified feed '%s' is not defined in your configuration.", $feedName)
            );
        }

        if (!isset($this->feeds[$feedName])) {
            $feed = new Feed($this->config['feeds'][$feedName], $this->formatters);
            $feed->setRouter($this->router);

            $this->feeds[$feedName] = $feed;
        }

        return $this->feeds[$feedName];
    }
}
