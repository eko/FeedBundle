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
use Eko\FeedBundle\Field\Channel\GroupChannelField;
use Eko\FeedBundle\Field\Item\ItemField;
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
     *
     * @throws \InvalidArgumentException if author is not filled in bundle configuration
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
     * {@inheritdoc}
     */
    public function initialize()
    {
        $encoding = $this->feed->get('encoding');

        $this->dom = new \DOMDocument('1.0', $encoding);

        $channel = $this->dom->createElement('feed');
        $channel->setAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $channel = $this->dom->appendChild($channel);

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

        $channel->appendChild($title);
        $channel->appendChild($subtitle);
        $channel->appendChild($link);
        $channel->appendChild($updated);
        $channel->appendChild($identifier);
        $channel->appendChild($author);

        // Add custom channel fields
        foreach ($this->feed->getChannelFields() as $field) {
            if ($field instanceof GroupChannelField) {
                $parent = $this->dom->createElement($field->getName());

                foreach ($field->getItemFields() as $childField) {
                    $child = $this->dom->createElement($childField->getName(), $childField->getValue());
                    $parent->appendChild($child);
                }
            } else {
                $parent = $this->dom->createElement($field->getName(), $field->getValue());
            }

            $channel->appendChild($parent);
        }

        // Add field items
        $items = $this->feed->getItems();

        foreach ($items as $item) {
            if (null === $item) {
                continue;
            }

            $this->addItem($channel, $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(\DOMElement $channel, ItemInterface $item)
    {
        $node = $this->dom->createElement('entry');
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
        return 'atom';
    }
}