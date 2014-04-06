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
 * ChannelFieldInterface
 *
 * This is the channel field interface
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
interface ChannelFieldInterface
{
    /**
     * Returns field name
     *
     * @return string
     */
    public function getName();
}
