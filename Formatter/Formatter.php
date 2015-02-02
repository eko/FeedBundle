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
use Eko\FeedBundle\Field\Item\ItemFieldInterface;
use Eko\FeedBundle\Field\Item\MediaItemField;
use Eko\FeedBundle\Item\Writer\ItemInterface;

use Symfony\Component\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var string|null
     */
    protected $domain;

    /**
     * @var \DOMDocument $dom XML DOMDocument
     */
    protected $dom;

    /**
     * @var array $fields Contain item Field instances for this formatter
     */
    protected $itemFields = array();

    /**
     * Construct a formatter with given feed
     *
     * @param TranslatorInterface $translator A Symfony translator service instance
     * @param string|null         $domain     A Symfony translation domain
     */
    public function __construct(TranslatorInterface $translator, $domain = null)
    {
        $this->translator = $translator;
        $this->domain     = $domain;
    }

    /**
     * Initializes feed
     */
    public function initialize()
    {
        $this->itemFields = array_merge($this->itemFields, $this->feed->getItemFields());
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
     * Adds channel fields to given channel
     *
     * @param \DOMElement $channel
     */
    protected function addChannelFields(\DOMElement $channel)
    {
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
            case 'Eko\FeedBundle\Field\Item\GroupItemField':
                return $this->formatGroupItemField($field, $item);
                break;

            case 'Eko\FeedBundle\Field\Item\MediaItemField':
                return $this->formatMediaItemField($field, $item);
                break;

            case 'Eko\FeedBundle\Field\Item\ItemField':
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
        $parent = $field->getParentObject();
        if($parent) {
        	$elArray = $this->format($parent, $item);
        	$element = reset($elArray);
        }
        else {
        	$element = $this->dom->createElement($name);
        }
        $itemFields = $field->getItemFields();

        foreach ($itemFields as $itemField) {
            $class = get_class($itemField);

            switch ($class) {
                case 'Eko\FeedBundle\Field\Item\MediaItemField':
                    $itemElements = $this->formatMediaItemField($itemField, $item);
                    break;

                case 'Eko\FeedBundle\Field\Item\ItemField':
                    $itemElements = $this->formatItemField($itemField, $item);
                    break;
            }

            foreach ($itemElements as $itemElement) {
                $element->appendChild($itemElement);
            }
        }

        return array($element);
    }

    /**
     * Format a media item field
     *
     * @param MediaItemField $field A media item field instance
     * @param ItemInterface  $item  An entity instance
     *
     * @return array|null|\DOMElement
     *
     * @throws \InvalidArgumentException if media array format waiting to be returned is not well-formatted
     */
    protected function formatMediaItemField(MediaItemField $field, ItemInterface $item)
    {
        $elements = array();

        $method = $field->getMethod();
        $values = $item->{$method}();

        if (null === $values) {
            return $elements;
        }

        if (!is_array($values) || (is_array($values) && isset($values['value']))) {
            $values = array($values);
        }

        foreach ($values as $value) {
            if (!isset($value['type']) || !isset($value['length']) || !isset($value['value'])) {
                throw new \InvalidArgumentException('Item media method must returns an array with following keys: type, length & value.');
            }

            $elementName = $field->getName();
            $elementName = $elementName[$this->getName()];

            $element = $this->dom->createElement($elementName);

            switch ($this->getName()) {
                case 'rss':
                    $element->setAttribute('url', $value['value']);
                    break;

                case 'atom':
                    $element->setAttribute('rel', 'enclosure');
                    $element->setAttribute('href', $value['value']);
                    break;
            }

            $element->setAttribute('type', $value['type']);
            $element->setAttribute('length', $value['length']);

            $elements[] = $element;
        }

        return $elements;
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

        if (null === $values) {
            return $elements;
        }

        if (!is_array($values)) {
            $values = array($values);
        }

        foreach ($values as $value) {
            $elements[] = $this->formatWithOptions($field, $item, $value);
        }

        return $elements;
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
        if ($field->get('translatable')) {
            $value = $this->translate($value);
        }

        if ($field->get('cdata')) {
            $value = $this->dom->createCDATASection($value);

            $element = $this->dom->createElement($name);
            $element->appendChild($value);
        } else if ($field->get('attribute')) {
            if (!$field->get('attribute_name')) {
                throw new \InvalidArgumentException("'attribute' parameter required an 'attribute_name' parameter.");
            }

            $element = $this->dom->createElement($name);
            $element->setAttribute($field->get('attribute_name'), $value);
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

    /**
     * Translates a value
     *
     * @param string $value
     *
     * @return string
     */
    protected function translate($value)
    {
        return $this->translator->trans($value, array(), $this->domain);
    }
}
