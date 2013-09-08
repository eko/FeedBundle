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
 * Atom formatter
 *
 * This class provides an Atom formatter
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class AtomFormatter extends Formatter implements FormatterInterface
{
    /**
     * Construct a formatter with given feed
     *
     * @param Feed $feed A feed instance
     */
    public function __construct(Feed $feed)
    {
        $this->itemFields = array(
            new ItemField('id', 'getFeedItemLink', array('cdata' => false)),
            new ItemField('title', 'getFeedItemTitle', array('cdata' => true)),
            new ItemField('summary', 'getFeedItemDescription', array('cdata' => true)),
            new ItemField('link', 'getFeedItemLink', array('attribute' => true, 'attribute_name' => 'href')),
            new ItemField('updated', 'getFeedItemPubDate',array('date_format' => \DateTime::ATOM)),
        );

        $author = $feed->get('author');

        if (empty($author)) {
            throw new \InvalidArgumentException('Atom formatter requires an "author" parameter in configuration.');
        }

        parent::__construct($feed);

        $this->initialize();
    }

    /**
     * Initialize XML DOMDocument nodes and call addItem on all items
     */
    public function initialize()
    {
        $encoding = $this->feed->get('encoding');

        $this->dom = new \DOMDocument('1.0', $encoding);

        $root = $this->dom->createElement('feed');
        $root->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $root = $this->dom->appendChild($root);

        $identifier = $this->dom->createElement('id', $this->feed->get('link'));
        $title = $this->dom->createElement('title', $this->feed->get('title'));
        $subtitle = $this->dom->createElement('subtitle', $this->feed->get('description'));
        $name = $this->dom->createElement('name', $this->feed->get('author'));

        $link = $this->dom->createElement('link');
        $link->setAttribute('href', $this->feed->get('link'));

        $date = new \DateTime();
        $updated = $this->dom->createElement('updated', $date->format(\DateTime::ATOM));

        $author = $this->dom->createElement('author');
        $author->appendChild($name);

        $root->appendChild($title);
        $root->appendChild($subtitle);
        $root->appendChild($link);
        $root->appendChild($updated);
        $root->appendChild($identifier);
        $root->appendChild($author);

        // Add custom channel fields
        foreach ($this->feed->getChannelFields() as $field) {
            $child = $this->dom->createElement($field->getName(), $field->getValue());
            $root->appendChild($child);
        }

        // Add field items
        $items = $this->feed->getItems();

        foreach ($items as $item) {
            $this->addItem($root, $item);
        }
    }

    /**
     * Add an entity item to the feed
     *
     * @param \DOMElement   $root The root (feed) DOM element
     * @param ItemInterface $item An entity object
     */
    public function addItem(\DOMElement $root, ItemInterface $item)
    {
        $node = $this->dom->createElement('entry');
        $node = $root->appendChild($node);

        foreach ($this->itemFields as $field) {
            $element = $this->format($field, $item);
            $node->appendChild($element);
        }
    }
}