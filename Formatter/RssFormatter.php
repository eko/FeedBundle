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
use Eko\FeedBundle\Field\ItemField;
use Eko\FeedBundle\Item\Writer\ItemInterface;

/**
 * RSS formatter
 *
 * This class provides an RSS formatter
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class RssFormatter extends Formatter implements FormatterInterface
{
    /**
     * Construct a formatter with given feed
     *
     * @param Feed $feed A feed instance
     */
    public function __construct(Feed $feed)
    {
        $this->itemFields = array(
            new ItemField('title', 'getFeedItemTitle', array('cdata' => true)),
            new ItemField('description', 'getFeedItemDescription', array('cdata' => true)),
            new ItemField('link', 'getFeedItemLink'),
            new ItemField('pubDate', 'getFeedItemPubDate', array('date_format' => \DateTime::RSS)),
        );

        parent::__construct($feed);

        $this->initialize();
    }

    /**
     * {@inheritdoc}
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

        $fields = array('title', 'description', 'link');

        foreach ($fields as $field) {
            $element = $this->dom->createElement($field, $this->feed->get($field));
            $channel->appendChild($element);
        }

        $date = new \DateTime();
        $lastBuildDate = $this->dom->createElement('lastBuildDate', $date->format(\DateTime::RSS));

        $channel->appendChild($lastBuildDate);

        // Add custom channel fields
        foreach ($this->feed->getChannelFields() as $field) {
            $child = $this->dom->createElement($field->getName(), $field->getValue());
            $channel->appendChild($child);
        }

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
            $element = $this->format($field, $item);

            if (null === $element) {
                continue;
            }

            $node->appendChild($element);
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
