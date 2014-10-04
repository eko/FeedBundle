<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Hydrator;

use Zend\Feed\Reader\Feed\FeedInterface;

/**
 * HydratorInterface
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
interface HydratorInterface
{
    /**
     * Hydrates given entity from its name with Feed data retrieved from reader
     *
     * @param FeedInterface $feed       A Feed instance
     * @param string        $entityName An entity name to populate with feed entries
     *
     * @return array
     *
     * @throws \RuntimeException if entity does not implements ItemInterface
     */
    public function hydrate(FeedInterface $feed, $entityName);
}
