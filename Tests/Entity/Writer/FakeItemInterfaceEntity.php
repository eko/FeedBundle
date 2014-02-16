<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Tests\Entity\Writer;

use Doctrine\ORM\Mapping as ORM;
use Eko\FeedBundle\Item\Writer\ItemInterface;

/**
 * Fake
 *
 * A fake entity implementing ItemInterface for tests
 */
class FakeItemInterfaceEntity implements ItemInterface
{
    protected $medias;

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

    /**
     * Returns a fake custom field
     *
     * @return string
     */
    public function getFeedItemCustom()
    {
        return 'My custom field';
    }

    /**
     * Returns a fake field author name
     *
     * @return string
     */
    public function getFeedItemAuthorName()
    {
        return 'John Doe';
    }

    /**
     * Returns a fake field author email
     *
     * @return string
     */
    public function getFeedItemAuthorEmail()
    {
        return 'john.doe@example.org';
    }

    /**
     * Sets feed media items
     *
     * @param array $medias
     */
    public function setFeedMediaItem(array $medias)
    {
        $this->medias = $medias;
    }

    /**
     * Returns a fake custom media field
     *
     * @return string
     */
    public function getFeedMediaItem()
    {
        return $this->medias;
    }

    /**
     * Returns a fake custom multiple media fields
     *
     * @return string
     */
    public function getFeedMediaMultipleItems()
    {
        return array(
            array(
                'type'   => 'image/jpeg',
                'length' => 500,
                'value'  => 'http://website.com/image.jpg'
            ),
            array(
                'type'   => 'image/png',
                'length' => 600,
                'value'  => 'http://website.com/image2.png'
            )
        );
    }

    /**
     * Returns a fake custom categories array
     *
     * @return array
     */
    public function getFeedCategoriesCustom()
    {
        return array(
            'category 1',
            'category 2',
            'category 3'
        );
    }
}