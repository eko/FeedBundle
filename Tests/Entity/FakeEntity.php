<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eko\FeedBundle\Item\ItemInterface;

/**
 * Fake
 *
 * @ORM\Table(name="fake")
 * @ORM\Entity
 */
class FakeEntity implements ItemInterface
{
    /**
     * Returns a fake title
     *
     * @return string
     */
    public function getFeedItemTitle()
    {
        return 'Fake title';
    }


    /**
     * Returns a fake description or content
     *
     * @return string
     */
    public function getFeedItemDescription()
    {
        return 'Fake description or content';
    }

    /**
     * Returns a fake item link
     *
     * @return string
     */
    public function getFeedItemLink()
    {
        return 'http://github.com/eko/FeedBundle/article/fake/url';
    }

    /**
     * Returns a fake publication date
     *
     * @return \DateTime
     */
    public function getFeedItemPubDate()
    {
        return new \DateTime();
    }
}