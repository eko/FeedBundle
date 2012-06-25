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

/**
 * Feed
 *
 * This is the main feed class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class Feed
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config  Configuration settings
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Set or redefine a configuration value
     *
     * @param string $parameter  A configuration parameter name
     * @param mixed  $value      A value
     */
    public function set($parameter, $value)
    {
        $this->config[$parameter] = $value;
    }

    /**
     * Returns config parameter value
     *
     * @param string     $parameter  A configuration parameter name
     * @param mixed|null $default    A default value if not found
     * @return mixed
     */
    public function get($parameter, $default = null)
    {
        return isset($this->config[$parameter]) ? $this->config[$parameter] : $default;
    }
}