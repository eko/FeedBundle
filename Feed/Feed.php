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

use Eko\FeedBundle\Field\Channel\ChannelFieldInterface;
use Eko\FeedBundle\Field\Item\ItemFieldInterface;
use Eko\FeedBundle\Item\Writer\ItemInterface;
use Eko\FeedBundle\Item\Writer\ProxyItem;
use Eko\FeedBundle\Item\Writer\RoutedItemInterface;
use Eko\FeedBundle\Formatter\AtomFormatter;
use Eko\FeedBundle\Formatter\RssFormatter;

use Symfony\Component\Routing\RouterInterface;

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
     * @var RouterInterface Router service
     */
    protected $router;

    /**
     * @var array $config Configuration settings
     */
    protected $config;

    /**
     * @var array $items Items of the feed
     */
    protected $items = array();

    /**
     * @var array $fields Contains channel Field instances for this feed
     */
    protected $channelFields = array();

    /**
     * @var array $fields Contains items Field instances for this feed
     */
    protected $itemFields = array();

    /**
     * @param array $config Configuration settings
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Set the router service
     *
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Set or redefine a configuration value
     *
     * @param mixed  $parameter A configuration parameter name
     * @param mixed  $value     A value
     *
     * @return \Eko\FeedBundle\Feed\Feed
     */
    public function set($parameter, $value)
    {
        $this->config[$parameter] = $value;

        return $this;
    }

    /**
     * Returns config parameter value
     *
     * @param mixed      $parameter A configuration parameter name
     * @param mixed|null $default   A default value if not found
     *
     * @return mixed
     */
    public function get($parameter, $default = null)
    {
        return isset($this->config[$parameter]) ? $this->config[$parameter] : $default;
    }

    /**
     * Add an item (an entity which implements ItemInterface instance)
     *
     * @param mixed $item An entity item (implements ItemInterface or RoutedItemInterface)
     *
     * @return \Eko\FeedBundle\Feed\Feed
     *
     * @throws \InvalidArgumentException if item does not implement ItemInterface or RoutedItemInterface
     */
    public function add($item)
    {
        if (!$item instanceof ItemInterface && !$item instanceof RoutedItemInterface) {
            throw new \InvalidArgumentException('Item must implement ItemInterface or RoutedItemInterface');
        }

        if ($item instanceof RoutedItemInterface) {
            $item = new ProxyItem($item, $this->router);
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * Add items from array
     *
     * @param array $items Array of items (implementing ItemInterface or RoutedItemInterface) to add
     *
     * @return \Eko\FeedBundle\Feed\Feed
     */
    public function addFromArray(array $items)
    {
        foreach ($items as $item) {
            $this->add($item);
        }

        return $this;
    }

    /**
     * Set items from array. Note that this method will override any existing items
     *
     * @param array $items Array of items (implementing ItemInterface or RoutedItemInterface) to set
     *
     * @return \Eko\FeedBundle\Feed\Feed
     */
    public function setItems(array $items)
    {
        $this->items = array();

        return $this->addFromArray($items);
    }

    /**
     * Returns feed items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add a new channel field to render
     *
     * @param ChannelFieldInterface $field A custom Field instance
     *
     * @return \Eko\FeedBundle\Feed\Feed
     */
    public function addChannelField(ChannelFieldInterface $field)
    {
        $this->channelFields[] = $field;

        return $this;
    }

    /**
     * Returns custom channel fields
     *
     * @return array
     */
    public function getChannelFields()
    {
        return $this->channelFields;
    }

    /**
     * Add a new item field to render
     *
     * @param ItemFieldInterface $field A custom Field instance
     *
     * @return \Eko\FeedBundle\Feed\Feed
     */
    public function addItemField(ItemFieldInterface $field)
    {
        $this->itemFields[] = $field;

        return $this;
    }

    /**
     * Returns custom item fields
     *
     * @return array
     */
    public function getItemFields()
    {
        return $this->itemFields;
    }

    /**
     * Render the feed in specified format
     *
     * @param string $format The format to render (RSS, Atom, ...)
     *
     * @return string
     *
     * @throws \InvalidArgumentException if given format formatter does not exists
     */
    public function render($format)
    {
        switch ($format) {
            case 'rss':
                $formatter = new RssFormatter($this);
                break;

            case 'atom':
                $formatter = new AtomFormatter($this);
                break;

            default:
                throw new \InvalidArgumentException(
                    sprintf("Format '%s' is not available. Please see documentation.", $format)
                );
                break;
        }

        return $formatter->render();
    }

    /**
     * Return if feed has items
     *
     * @return boolean
     */
    public function hasItems()
    {
        return (bool) !empty($this->itemFields);
    }
}
