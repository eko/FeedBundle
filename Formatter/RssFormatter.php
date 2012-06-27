<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Formatter;

use Eko\FeedBundle\Feed\Feed;
use Eko\FeedBundle\Item\ItemInterface;

/**
 * RSS formatter
 *
 * This class provides an RSS formatter
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class RssFormatter implements FormatterInterface
{
    /**
     * @var Feed $feed  A feed instance
     */
    protected $feed;

    /**
     * @var DOMDocument $dom  XML DOMDocument
     */
    protected $dom;

    /**
     * Construct a formatter with given feed
     *
     * @param Feed $feed  A feed instance
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
        $this->initialize();
    }

    /**
     * Initialize XML DOMDocument
     */
    public function initialize()
    {
        $encoding = $this->feed->get('encoding');

        $this->dom = new \DOMDocument('1.0', $encoding);

        $root = $this->dom->createElement('rss');
        $root->setAttribute('version', '2.0');
        $root = $this->dom->appendChild($root);

        $channel = $this->dom->createElement('channel');
        $channel = $root->appendChild($channel);

        $title = $this->dom->createElement('title', $this->feed->get('title'));
        $description = $this->dom->createElement('description', $this->feed->get('description'));
        $link = $this->dom->createElement('link', $this->feed->get('link'));

        $date = new \DateTime();
        $lastBuildDate = $this->dom->createElement('lastBuildDate', $date->format(\DateTime::RSS));

        $channel->appendChild($title);
        $channel->appendChild($description);
        $channel->appendChild($link);
        $channel->appendChild($lastBuildDate);

        $items = $this->feed->getItems();

        foreach ($items as $item) {
            $this->addItem($channel, $item);
        }
    }

    /**
     * Add an entity item to the feed
     *
     * @param \DOMElement   $channel  The channel DOM element
     * @param ItemInterface $item     An entity object
     */
    public function addItem(\DOMElement $channel, ItemInterface $item)
    {
        $node = $this->dom->createElement('item');
        $node = $channel->appendChild($node);

        $title = $this->dom->createCDATASection($item->getFeedItemTitle());

        $element = $this->dom->createElement('title');
        $element->appendChild($title);

        $node->appendChild($element);

        $description = $this->dom->createCDATASection($item->getFeedItemDescription());

        $element = $this->dom->createElement('description');
        $element->appendChild($description);

        $node->appendChild($element);

        $link = $this->dom->createElement('link', $item->getFeedItemLink());
        $node->appendChild($link);

        $date = $item->getFeedItemPubDate()->format(\DateTime::RSS);

        $pubDate = $this->dom->createElement('pubDate', $date);
        $node->appendChild($pubDate);
    }

    /**
     * This method render the given feed transforming the DOMDocument to XML
     *
     * @return string
     */
    public function render()
    {
        $this->dom->formatOutput = true;

        return $this->dom->saveXml();
    }
}