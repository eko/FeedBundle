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

/**
 * ConfigTest
 *
 * This is the configuration test class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $this->assertEquals('test', 'test');
    }
}
