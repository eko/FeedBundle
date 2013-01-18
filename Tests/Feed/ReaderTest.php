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

use Eko\FeedBundle\Feed\Reader;

/**
 * ReaderTest
 *
 * This is the feed reader test class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader $reader A feed reader instance
     */
    protected $reader;

    /**
     * Construct elements used in test case
     */
    public function __construct() {
        $this->reader = new Reader();
    }

    /**
     * Check if feeds can be loaded from data fixtures
     */
    public function testLoad()
    {
        $feed = $this->reader->get(__DIR__ . '/../DataFixtures/Feed.xml');

        $this->assertNotNull($feed, 'Returned feed should not be null');
        $this->assertInstanceOf('\Zend\Feed\Reader\Feed\FeedInterface', $feed, 'Should return an AbstractFeed instance');

        foreach ($feed as $entry) {
            $this->assertEquals('PHP 5.4.11 and PHP 5.3.21 released!', $entry->getTitle(), 'Should be equal');
            $this->assertEquals('http://php.net/index.php#id2013-01-17-1', $entry->getLink(), 'Should be equal');
            $this->assertInstanceOf('\Zend\Feed\Reader\Collection\Author', $entry->getAuthors(), 'Should be an instance of Author');
        }
    }
}
