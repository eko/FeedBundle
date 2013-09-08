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
use Eko\FeedBundle\Field\ItemFieldInterface;
use Eko\FeedBundle\Item\Writer\ItemInterface;

/**
 * Formatter
 *
 * This class provides formatter methods
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class Formatter
{
    /**
     * @var Feed $feed A feed instance
     */
    protected $feed;

    /**
     * @var DOMDocument $dom XML DOMDocument
     */
    protected $dom;

    /**
     * @var array $fields Contain item Field instances for this formatter
     */
    protected $itemFields = array();

    /**
     * Construct a formatter with given feed
     *
     * @param Feed $feed A feed instance
     */
    public function __construct(Feed $feed)
    {
        $this->itemFields = array_merge($this->itemFields, $feed->getItemFields());

        $this->feed = $feed;
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

    /**
     * Format items field
     *
     * @param ItemFieldInterface $field A item field instance
     * @param ItemInterface      $item  An entity instance
     * 
     * @return string
     */
    protected function format(ItemFieldInterface $field, ItemInterface $item)
    {
        $class = get_class($field);

        switch ($class) {
            case 'Eko\FeedBundle\Field\GroupItemField':
                return $this->formatGroupItemField($field, $item);
                break;

            case 'Eko\FeedBundle\Field\ItemField':
                return $this->formatItemField($field, $item);
                break;
        }
    }

    /**
     * Format a group item field
     *
     * @param ItemFieldInterface $field An item field instance
     * @param ItemInterface      $item  An entity instance
     *
     * @return \DOMElement
     */
    protected function formatGroupItemField(ItemFieldInterface $field, ItemInterface $item)
    {
        $name = $field->getName();

        $element = $this->dom->createElement($name);
        $itemElements = $this->formatItemField($field->getItemField(), $item);

        foreach ($itemElements as $itemElement) {
            $element->appendChild($itemElement);
        }

        return $element;
    }

    /**
     * Format an item field
     *
     * @param ItemFieldInterface $field An item field instance
     * @param ItemInterface      $item  An entity instance
     *
     * @return array|\DOMElement
     */
    protected function formatItemField(ItemFieldInterface $field, ItemInterface $item)
    {
        $elements = array();

        $method = $field->getMethod();
        $values = $item->{$method}();

        if (!is_array($values)) {
            $values = array($values);
        }

        foreach ($values as $value) {
            $elements[] = $this->formatWithOptions($field, $item, $value);
        }

        return 1 == count($elements) ? current($elements) : $elements;
    }

    /**
     * Format an item field
     *
     * @param ItemFieldInterface $field An item field instance
     * @param ItemInterface      $item  An entity instance
     * @param string             $value A field value
     *
     * @return \DOMElement
     *
     * @throws \InvalidArgumentException
     */
    protected function formatWithOptions(ItemFieldInterface $field, ItemInterface $item, $value)
    {
        $element = null;

        $name = $field->getName();

        if ($field->get('cdata')) {
            $value = $this->dom->createCDATASection($value);

            $element = $this->dom->createElement($name);
            $element->appendChild($value);
        } else if ($field->get('attribute')) {
            if (!$field->get('attribute_name')) {
                throw new \InvalidArgumentException("'attribute' parameter required an 'attribute_name' parameter.");
            }

            $element = $this->dom->createElement($name);
            $element->setAttribute($field->get('attribute_name'), $item->getFeedItemLink());
        } else {
            if ($format = $field->get('date_format')) {
                if (!$value instanceof \DateTime) {
                    throw new \InvalidArgumentException(sprintf('Field "%s" should be a DateTime instance.', $name));
                }

                $value = $value->format($format);
            }

            $element = $this->dom->createElement($name, $value);
        }

        return $element;
    }
}
