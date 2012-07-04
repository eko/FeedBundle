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

use Eko\FeedBundle\Feed\FeedManager;
use Eko\FeedBundle\Tests\Entity\FakeEntity;

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
     * @var FeedManager $manager  A feed manager instance
     */
    protected $manager;

    /**
     * Construct elements used in test case
     */
    public function __construct() {
        $config = array(
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

        $this->manager = new FeedManager($config);
    }

    /**
     * Check if there is no item inserted during feed creation
     */
    public function testNoItem()
    {
        $feed = $this->manager->get('article');

        $this->assertEquals(0, count($feed->getItems()));
    }

    /**
     * Check if items are correctly added
     */
    public function testAdditem()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeEntity());

        $this->assertEquals(1, count($feed->getItems()));
    }


    /**
     * Check if multiple items are correctly loaded
     */
    public function testSetItems()
    {
        $feed = $this->manager->get('article');

        $items = array(new FakeEntity(), new FakeEntity());
        $feed->addFromArray($items);

        $this->assertEquals(2, count($feed->getItems()));
    }

    /**
     * Check if an \InvalidArgumentException is thrown
     * when formatter asked for rendering does not exists
     */
    public function testFormatterNotFoundException()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeEntity());

        $this->setExpectedException(
            'InvalidArgumentException',
            "Format 'unknown_formatter' is not available. Please see documentation."
        );

        $feed->render('unknown_formatter');
    }
}
