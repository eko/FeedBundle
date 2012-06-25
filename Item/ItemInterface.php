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
 * Item interface
 *
 * This interface contains the methods that you need to implement in your entity
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
interface ItemInterface
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
     * This method returns feed item URL link
     *
     * @abstract
     * @return string
     */
    public function getFeedItemLink();

    /**
     * This method returns item publication date
     *
     * @abstract
     * @return string
     */
    public function getFeedItemPubdate();
}