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
use Eko\FeedBundle\Formatter\FormatterRegistry;
use Eko\FeedBundle\Formatter\RssFormatter;
use Eko\FeedBundle\Tests\Entity\Writer\FakeItemInterfaceEntity;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * FeedTest.
 *
 * This is the feed test class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class FeedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FeedManager A feed manager instance
     */
    protected $manager;

    /**
     * Set up.
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

        $formatterRegistry = new FormatterRegistry();
        $formatterRegistry->addFormatter('rss', new RssFormatter($translator, 'test'));
        $formatterRegistry->addFormatter('atom', new AtomFormatter($translator, 'test'));

        $this->manager = new FeedManager($router, $config, $formatterRegistry);
    }

    /**
     * Check if there is no item inserted during feed creation.
     */
    public function testNoItem()
    {
        $feed = $this->manager->get('article');

        $this->assertEquals(0, count($feed->getItems()));
    }

    /**
     * Check if items are correctly added.
     */
    public function testAdditem()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());

        $this->assertEquals(1, count($feed->getItems()));
    }

    /**
     * Check if items are correctly added.
     */
    public function testAdditemsFromEmpty()
    {
        $feed = $this->manager->get('article');

        $items = [new FakeItemInterfaceEntity(), new FakeItemInterfaceEntity()];
        $feed->addFromArray($items);

        $this->assertEquals(2, count($feed->getItems()));
    }

    /**
     * Check if items are correctly added.
     */
    public function testAdditemsWithExistingItem()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());

        $items = [new FakeItemInterfaceEntity(), new FakeItemInterfaceEntity()];
        $feed->addFromArray($items);

        $this->assertEquals(3, count($feed->getItems()));
    }

    /**
     * Check if multiple items are correctly loaded.
     */
    public function testSetItemsFromEmpty()
    {
        $feed = $this->manager->get('article');

        $items = [new FakeItemInterfaceEntity(), new FakeItemInterfaceEntity()];
        $feed->setItems($items);

        $this->assertEquals(2, count($feed->getItems()));
    }

    /**
     * Check if multiple items are correctly loaded.
     */
    public function testSetItemsWithExistingItem()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());

        $items = [new FakeItemInterfaceEntity(), new FakeItemInterfaceEntity()];
        $feed->setItems($items);

        $this->assertEquals(2, count($feed->getItems()));
    }

    /**
     * Check if an \InvalidArgumentException is thrown
     * when formatter asked for rendering does not exists.
     */
    public function testFormatterNotFoundException()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage("Unable to find a formatter service for format 'unknown_formatter'.");

        $feed->render('unknown_formatter');
    }
}
