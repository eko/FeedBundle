<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Item;

/**
 * Routed Item interface
 *
 * This interface contains the methods that you need to implement in your entity
 *
 * @author Rob Masters <mastahuk@gmail.com>
 */
interface RoutedItemInterface
{
    /**
     * This method returns feed item title
     *
     * @abstract
     * @return string
     */
    public function getFeedItemTitle();

    /**
     * This method returns feed item description (or content)
     *
     * @abstract
     * @return string
     */
    public function getFeedItemDescription();

    /**
     * This method returns the name of the route
     *
     * @abstract
     * @return string
     */
    public function getFeedItemRouteName();

    /**
     * This method returns the parameters for the route.
     *
     * @abstract
     * @return array
     */
    public function getFeedItemRouteParameters();

    /**
     * This method returns item publication date
     *
     * @abstract
     * @return \DateTime
     */
    public function getFeedItemPubDate();
}