<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Tests\Feed;

use Eko\FeedBundle\Feed\FeedManager;
use Eko\FeedBundle\Formatter\AtomFormatter;
use Eko\FeedBundle\Formatter\RssFormatter;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * FeedManagerTest.
 *
 * This is the feed manager test class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class FeedManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FeedManager A feed manager instance
     */
    protected $manager;

    /**
     * Sets up manager & configuration used in test cases.
     */
    protected function setUp(): void
    {
        $config = [
            'feeds' => [
                'article' => [
                    'title'       => 'My articles/posts',
                    'description' => 'Latests articles',
                    'link'        => 'http://github.com/eko/FeedBundle',
                    'encoding'    => 'utf-8',
                    'author'      => 'Vincent Composieux',
                ],
            ],
        ];

        $router = $this->createMock(RouterInterface::class);

        $translator = $this->createMock(TranslatorInterface::class);

        $formatters = [
            'rss'  => new RssFormatter($translator, 'test'),
            'atom' => new AtomFormatter($translator, 'test'),
        ];

        $this->manager = new FeedManager($router, $config, $formatters);
    }

    /**
     * Check if feed is correctly inserted.
     */
    public function testHasFeed()
    {
        $this->assertTrue($this->manager->has('article'));
    }

    /**
     * Check if a fake feed name is not marked as existing.
     */
    public function testFeedDoNotExists()
    {
        $this->assertFalse($this->manager->has('fake_feed_name'));
    }

    /**
     * Check if an \InvalidArgumentException is thrown
     * if requested feed does not exists.
     */
    public function testNonExistantFeedException()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage("Specified feed 'unknown_feed_name' is not defined in your configuration.");

        $this->manager->get('unknown_feed_name');
    }

    /**
     * Check if the feed data are properly loaded from configuration settings.
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
