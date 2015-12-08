<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Field\Channel;

/**
 * ChannelField.
 *
 * This is the channel field class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ChannelField implements ChannelFieldInterface
{
    /**
     * @var string Field name
     */
    protected $name;

    /**
     * @var string Field value
     */
    protected $value;

    /**
     * @var array Options array (required, cdata, ...)
     */
    protected $options;

    /**
     * @var array An attributes array
     */
    protected $attributes;

    /**
     * Constructor.
     *
     * @param string $name       A field name
     * @param string $value      A field value
     * @param array  $options    An options array
     * @param array  $attributes An attributes array
     */
    public function __construct($name, $value, array $options = [], array $attributes = [])
    {
        $this->name = $name;
        $this->value = $value;

        if (!empty($options)) {
            $this->options = $options;
        }

        $this->attributes = $attributes;
    }

    /**
     * Returns field name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns field value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns option value.
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
     * Returns attributes to be added to this item field.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
