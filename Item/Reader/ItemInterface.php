<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Item\Reader;

/**
 * Item interface
 *
 * This interface contains the methods that you need to implement in your entity
 * to load data from an XML feed in your entity
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
interface ItemInterface
{
    /**
     * This method sets feed item title
     *
     * @abstract
     */
    public function setFeedItemTitle();

    /**
     * This method sets feed item description (or content)
     *
     * @abstract
     */
    public function setFeedItemDescription();

    /**
     * This method sets feed item URL link
     *
     * @abstract
     */
    public function setFeedItemLink();

    /**
     * This method sets item publication date
     *
     * @abstract
     */
    public function setFeedItemPubDate();
}