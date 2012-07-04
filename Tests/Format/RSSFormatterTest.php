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
use Eko\FeedBundle\Item\Field;
use Eko\FeedBundle\Tests\Entity\FakeEntity;

/**
 * RSSFormatterTest
 *
 * This is the RSS formatter test class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class RSSFormatterTest extends \PHPUnit_Framework_TestCase
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
     * Check if RSS formatter output a valid XML
     */
    public function testRenderValidXML()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeEntity());

        $output = $feed->render('rss');

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadXML($output);

        $this->assertEquals(0, count(libxml_get_errors()));
        $this->assertContains('<rss version="2.0">', $output);
    }

    /**
     * Check if RSS formatter output item
     */
    public function testRenderItem()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeEntity());

        $output = $feed->render('rss');

        $this->assertContains('<title><![CDATA[Fake title]]></title>', $output);
        $this->assertContains('<description><![CDATA[Fake description or content]]></description>', $output);
        $this->assertContains('<link>http://github.com/eko/FeedBundle/article/fake/url</link>', $output);
    }

    /**
     * Check if a custom field is properly rendered
     */
    public function testAddCustomField()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeEntity());
        $feed->addField(new Field('fake_custom', 'getFeedItemCustom'));

        $output = $feed->render('rss');

        $this->assertContains('<fake_custom>My custom field</fake_custom>', $output);
    }
}