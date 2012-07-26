<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Item;

/**
 * Field
 *
 * This is the items field class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class Field
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
     * Constructor
     *
     * @param string $name    A field name
     * @param string $method  An item method name
     * @param array  $options An options array
     */
    public function __construct($name, $method, $options = array())
    {
        $this->name = $name;
        $this->method = $method;

        if (!empty($options)) {
            $this->options = $options;
        }
    }

    /**
     * Returns field name
     *
     * @return string
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
}
