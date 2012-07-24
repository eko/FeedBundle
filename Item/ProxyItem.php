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

use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Proxy Item
 *
 * This interface contains the methods that you need to implement in your entity
 *
 * @author Rob Masters <mastahuk@gmail.com>
 */
class ProxyItem implements ItemInterface
{
    /**
     * @var RoutedItemInterface
     */
    protected $item;

    /**
     * @var Router
     */
    protected $router;


    /**
     * @param RoutedItemInterface $item
     */
    public function __construct(RoutedItemInterface $item, Router $router)
    {
        $this->item = $item;
        $this->router = $router;
    }

    /**
     * This method returns feed item title
     *
     * @return string
     */
    public function getFeedItemTitle()
    {
        return $this->item->getFeedItemTitle();
    }

    /**
     * This method returns feed item description (or content)
     *
     * @return string
     */
    public function getFeedItemDescription()
    {
        return $this->item->getFeedItemDescription();
    }

    /**
     * This method returns feed item URL link
     *
     * @return string
     */
    public function getFeedItemLink()
    {
        $parameters = $this->item->getFeedItemRouteParameters() ?: array();

        return $this->router->generate($this->item->getFeedItemRouteName(), $parameters, true);
    }

    /**
     * This method returns item publication date
     *
     * @return \DateTime
     */
    public function getFeedItemPubDate()
    {
        return $this->item->getFeedItemPubDate();
    }
}