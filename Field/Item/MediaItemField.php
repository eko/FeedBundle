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
 * MediaItemField
 *
 * This is the media items field class that uses enclosure
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class MediaItemField extends ItemField
{
    /**
     * Constructor
     *
     * @param string $method     An item method name
     * @param array  $options    An options array
     * @param array  $attributes An attributes array
     */
    public function __construct($method, array $options = array(), array $attributes = array())
    {
        $name = array(
            'rss'  => 'enclosure',
            'atom' => 'link'
        );

        parent::__construct($name, $method, $options, $attributes);
    }
}
