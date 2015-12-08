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

use Eko\FeedBundle\Item\Reader\ItemInterface;
use Zend\Feed\Reader\Feed\FeedInterface;

/**
 * DefaultHydrator.
 *
 * This is the default feed hydrator
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class DefaultHydrator implements HydratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(FeedInterface $feed, $entityName)
    {
        $items = [];

        foreach ($feed as $entry) {
            $entity = new $entityName();

            if (!$entity instanceof ItemInterface) {
                throw new \RuntimeException(
                    sprintf('Entity "%s" does not implement required %s.', $entityName, 'Eko\FeedBundle\Item\Reader\ItemInterface')
                );
            }

            $entity->setFeedItemTitle($entry->getTitle());
            $entity->setFeedItemDescription($entry->getContent());
            $entity->setFeedItemLink($entry->getLink());
            $entity->setFeedItemPubDate($entry->getDateModified());

            $items[] = $entity;
        }

        return $items;
    }
}
