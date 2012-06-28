<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Tests;

use Doctrine\ORM\Mapping as ORM;

use Eko\FeedBundle\Feed\FeedManager;
use Eko\FeedBundle\Item\ItemInterface;

/**
 * FakeArticle
 *
 * @ORM\Table(name="article")
 * @ORM\Entity
 */
class FakeArticle implements ItemInterface
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

/**
 * FeedTest
 *
 * This is the feed test class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class FeedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array $config  Configuration settings
     */
    protected $config = array(
        'feeds' => array(
            'article' => array(
                'title'       => 'My articles/posts',
                'description' => 'Latests articles',
                'link'        => 'http://github.com/eko/FeedBundle',
                'encoding'    => 'utf-8',
                'author'      => 'Vincent Composieux'
            )
        )
    );

    /**
     * Check if there is no item inserted during feed creation
     */
    public function testNoItem()
    {
        $manager = new FeedManager($this->config);
        
        $feed = $manager->get('article');
        
        $this->assertEquals(0, count($feed->getItems()));
    }

    /**
     * Check if items are correctly added
     */
    public function testAdditem()
    {
        $fakeArticle = new FakeArticle();
        
        $manager = new FeedManager($this->config);
        
        $feed = $manager->get('article');
        $feed->add($fakeArticle);
        
        $this->assertEquals(1, count($feed->getItems()));
    }
}
