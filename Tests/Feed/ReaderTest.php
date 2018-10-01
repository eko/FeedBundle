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

use Eko\FeedBundle\Feed\Reader;
use Eko\FeedBundle\Hydrator\DefaultHydrator;
use Eko\FeedBundle\Tests\Entity\Reader\FakeItemInterfaceEntity;

/**
 * ReaderTest.
 *
 * This is the feed reader test class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader A feed reader instance
     */
    protected $reader;

    /**
     * Sets up elements used in test case.
     */
    public function setUp()
    {
        $this->reader = new Reader();
        $this->reader->setHydrator(new DefaultHydrator());
    }

    /**
     * Check if feeds can be loaded from data fixtures.
     */
    public function testLoad()
    {
        $feed = $this->reader->load(__DIR__.'/../DataFixtures/Feed.xml')->get();

        $this->assertNotNull($feed, 'Returned feed should not be null');
        $this->assertInstanceOf('\Zend\Feed\Reader\Feed\FeedInterface', $feed, 'Should return an AbstractFeed instance');

        foreach ($feed as $entry) {
            $this->assertEquals('PHP 5.4.11 and PHP 5.3.21 released!', $entry->getTitle(), 'Should be equal');
            $this->assertEquals('http://php.net/index.php#id2013-01-17-1', $entry->getLink(), 'Should be equal');
            $this->assertInstanceOf('\Zend\Feed\Reader\Collection\Author', $entry->getAuthors(), 'Should be an instance of Author');
        }
    }

    /**
     * Check if feeds can populate an entity.
     */
    public function testPopulate()
    {
        $reader = $this->reader->load(__DIR__.'/../DataFixtures/Feed.xml');
        $items = $reader->populate(FakeItemInterfaceEntity::class);

        $this->assertCount(1, $items, 'Should contain an array with the only feed element');

        foreach ($items as $item) {
            $this->assertInstanceOf(FakeItemInterfaceEntity::class, $item, 'Should be an instance of populated entity name');

            $this->assertEquals('PHP 5.4.11 and PHP 5.3.21 released!', $item->getTitle(), 'Should be correct title');
            $this->assertEquals('<div>', substr($item->getDescription(), 0, 5), 'Should be correct description');
            $this->assertEquals('http://php.net/index.php#id2013-01-17-1', $item->getLink(), 'Should be correct link');
            $this->assertEquals('2013-01-17 14:54:00', $item->getPublicationDate()->format('Y-m-d H:i:s'), 'Should be the same datetime object');
        }
    }
}
