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
     * Constructor
     *
     * @param RoutedItemInterface $item
     */
    public function __construct(RoutedItemInterface $item, Router $router)
    {
        $this->item = $item;
        $this->router = $router;
    }

    /**
     * Returns item custom fields methods if exists in entity
     *
     * @param string $method Method name
     * @param array  $args   Arguments array
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException If method is not defined
     */
    public function __call($method, $args)
    {
        if (method_exists($this->item, $method)) {
            return call_user_func_array(array($this->item, $method), $args);
        }

        throw new \InvalidArgumentException(sprintf('Method "%s" should be defined in your entity.', $method));
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

        $url = $this->router->generate($this->item->getFeedItemRouteName(), $parameters, true);

        $anchor = (string) $this->item->getFeedItemUrlAnchor();
        if ($anchor !== '') {
            $url .= '#' . $anchor;
        }

        return $url;
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
