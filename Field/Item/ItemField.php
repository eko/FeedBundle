<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Field\Item;

/**
 * ItemField
 *
 * This is the items field class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ItemField implements ItemFieldInterface
{
    /**
     * @var string $name Field name
     */
    protected $name;

    /**
     * @var string $method Item method name
     */
    protected $method;

    /**
     * @var array $options Options array (required, cdata, ...)
     */
    protected $options;

    /**
     * @var array $attributes Attributes to add to this item field
     */
    protected $attributes;

    /**
     * Constructor
     *
     * @param string|array $name       A field name
     * @param string       $method     An item method name
     * @param array        $options    An options array
     * @param array        $attributes An attributes array
     */
    public function __construct($name, $method, array $options = array(), array $attributes = array())
    {
        $this->name   = $name;
        $this->method = $method;

        if (!empty($options)) {
            $this->options = $options;
        }

        $this->attributes = $attributes;
    }

    /**
     * Returns field name
     *
     * @return string|array
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns method name
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns option value
     *
     * @param string $option  An option name
     * @param mixed  $default A default value
     *
     * @return mixed
     */
    public function get($option, $default = false)
    {
        return isset($this->options[$option]) ? $this->options[$option] : $default;
    }

    /**
     * Returns attributes to be added to this item field
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
