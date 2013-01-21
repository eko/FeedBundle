<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Tests\Entity\Reader;

use Doctrine\ORM\Mapping as ORM;
use Eko\FeedBundle\Item\Reader\ItemInterface;

/**
 * Fake
 *
 * A fake entity implementing ItemInterface for tests
 */
class FakeItemInterfaceEntity implements ItemInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * Returns a fake title
     *
     * @return string
     */
    public function setFeedItemTitle($title)
    {
        $this->title = $title;
    }


    /**
     * Returns a fake description or content
     *
     * @return string
     */
    public function setFeedItemDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns a fake item link
     *
     * @return string
     */
    public function setFeedItemLink($link)
    {
        $this->link = $link;
    }

    /**
     * Returns a fake publication date
     *
     * @return \DateTime
     */
    public function setFeedItemPubDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Get publication date
     *
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->date;
    }
}