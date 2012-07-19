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

/**
 * FeedManagerTest
 *
 * This is the feed manager test class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
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

        $container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->setMethods(array('get'))
            ->getMock()
        ;

        $container
            ->expects($this->any())
            ->method('get')
            ->with($this->equalTo('router'))
            ->will($this->returnValue($this->getMock('\Symfony\Bundle\FrameworkBundle\Routing\Router', array(), array(), '', false)))
        ;

        $this->manager = new FeedManager($config);
        $this->manager->setContainer($container);
    }

    /**
     * Check if feed is correctly inserted
     */
    public function testHasFeed()
    {
        $this->assertTrue($this->manager->has('article'));
    }

    /**
     * Check if a fake feed name is not marked as existing
     */
    public function testFeedDoNotExists()
    {
        $this->assertFalse($this->manager->has('fake_feed_name'));
    }

    /**
     * Check if an \InvalidArgumentException is thrown
     * if requested feed does not exists
     */
    public function testNonExistantFeedException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            "Specified feed 'unknown_feed_name' is not defined in your configuration."
        );

        $this->manager->get('unknown_feed_name');
    }

    /**
     * Check if the feed data are properly loaded from configuration settings
     */
    public function testGetFeedData()
    {
        $feed = $this->manager->get('article');

        $this->assertEquals('My articles/posts', $feed->get('title'));
        $this->assertEquals('Latests articles', $feed->get('description'));
        $this->assertEquals('http://github.com/eko/FeedBundle', $feed->get('link'));
        $this->assertEquals('utf-8', $feed->get('encoding'));
        $this->assertEquals('Vincent Composieux', $feed->get('author'));
    }
}
