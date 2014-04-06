<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Tests\Entity\Writer;

use Eko\FeedBundle\Item\Writer\RoutedItemInterface;

/**
 * Fake
 *
 * A fake entity implementing RoutedItemInterface for tests
 */
class FakeRoutedItemInterfaceEntity implements RoutedItemInterface
{
    /**
     * Returns a fake title
     *
     * @return string
     */
    public function getFeedItemTitle()
    {
        return 'Fake title';
    }


    /**
     * Returns a fake description or content
     *
     * @return string
     */
    public function getFeedItemDescription()
    {
        return 'Fake description or content';
    }

    /**
     * Returns a fake item link
     *
     * @return string
     */
    public function getFeedItemRouteName()
    {
        return 'fake_route';
    }

    /**
     * Returns a fake route parameters array
     *
     * @return array
     */
    public function getFeedItemRouteParameters()
    {
        return array();
    }

    /**
     * Returns a fake anchor
     *
     * @return string
     */
    public function getFeedItemUrlAnchor()
    {
        return 'fake-anchor';
    }

    /**
     * Returns a fake publication date
     *
     * @return \DateTime
     */
    public function getFeedItemPubDate()
    {
        return new \DateTime();
    }

    /**
     * Returns a fake custom field
     *
     * @return string
     */
    public function getFeedItemCustom()
    {
        return 'My custom field';
    }
}
