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
use Eko\FeedBundle\Field\Item\ItemField;
use Eko\FeedBundle\Item\Writer\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * RSS formatter.
 *
 * This class provides an RSS formatter
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class RssFormatter extends Formatter implements FormatterInterface
{
    /**
     * Construct a formatter with given feed.
     *
     * @param TranslatorInterface $translator A Symfony translator service instance
     * @param string|null         $domain     A Symfony translation domain
     */
    public function __construct(TranslatorInterface $translator, $domain = null)
    {
        $this->itemFields = [
            new ItemField('title', 'getFeedItemTitle', ['cdata' => true]),
            new ItemField('description', 'getFeedItemDescription', ['cdata' => true]),
            new ItemField('link', 'getFeedItemLink'),
            new ItemField('pubDate', 'getFeedItemPubDate', ['date_format' => \DateTime::RSS]),
        ];

        parent::__construct($translator, $domain);
    }

    /**
     * Sets feed instance.
     *
     * @param Feed $feed
     */
    public function setFeed(Feed $feed)
    {
        $this->feed = $feed;

        $this->initialize();
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        parent::initialize();

        $encoding = $this->feed->get('encoding');

        $this->dom = new \DOMDocument('1.0', $encoding);

        $root = $this->dom->createElement('rss');
        $root->setAttribute('version', '2.0');
        $root = $this->dom->appendChild($root);

        $channel = $this->dom->createElement('channel');
        $channel = $root->appendChild($channel);

        $title = $this->translate($this->feed->get('title'));
        $title = $this->dom->createElement('title', $title);
        $channel->appendChild($title);

        $description = $this->translate($this->feed->get('description'));
        $description = $this->dom->createElement('description', $description);
        $channel->appendChild($description);

        $link = $this->dom->createElement('link', $this->feed->get('link'));
        $channel->appendChild($link);

        $date = new \DateTime();
        $lastBuildDate = $this->dom->createElement('lastBuildDate', $date->format(\DateTime::RSS));

        $channel->appendChild($lastBuildDate);

        // Add custom channel fields
        $this->addChannelFields($channel);

        // Add feed items
        $items = $this->feed->getItems();

        foreach ($items as $item) {
            $this->addItem($channel, $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(\DOMElement $channel, ItemInterface $item)
    {
        $node = $this->dom->createElement('item');
        $node = $channel->appendChild($node);

        foreach ($this->itemFields as $field) {
            $elements = $this->format($field, $item);

            foreach ($elements as $element) {
                $node->appendChild($element);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rss';
    }
}
