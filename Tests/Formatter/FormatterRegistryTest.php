<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Tests\Formatter;

use Eko\FeedBundle\Formatter\FormatterInterface;
use Eko\FeedBundle\Formatter\FormatterRegistry;

/**
 * FormatterRegistryTest.
 *
 * This is the Formatter registry test class
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class FormatterRegistryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Check if initial instance does not provide formatters.
     */
    public function testInitialStateIsEmpty()
    {
        $registry = new FormatterRegistry();

        $this->assertFalse($registry->supportsFormat('rss'));
        $this->assertFalse($registry->supportsFormat('atom'));
    }

    /**
     * Check if initial instance does not provide formatters.
     */
    public function testSupportForAddedFormatterIsProvided()
    {
        $registry = new FormatterRegistry();
        $registry->addFormatter('rss', $this->createMock(FormatterInterface::class));

        $this->assertTrue($registry->supportsFormat('rss'));
    }

    /**
     * Check if initial instance does not provide formatters.
     */
    public function testReturnsRequestedFormatter()
    {
        $formatter = $this->createMock(FormatterInterface::class);

        $registry = new FormatterRegistry();
        $registry->addFormatter('rss', $formatter);

        $this->assertSame($formatter, $registry->getFormatter('rss'));
    }
}
