<?php
/*
 * This file is part of the EkoFeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * FeedManager
 *
 * This class manage feeds specified in configuration file
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class FeedManager extends ContainerAware
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
}