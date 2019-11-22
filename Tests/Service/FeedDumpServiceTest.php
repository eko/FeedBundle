<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Eko\FeedBundle\Feed\Feed;
use Eko\FeedBundle\Feed\FeedManager;
use Eko\FeedBundle\Service\FeedDumpService;
use Eko\FeedBundle\Tests\Entity\Writer\FakeItemInterfaceEntity;
use Symfony\Component\Filesystem\Filesystem;

/**
 * FeedDumpServiceTest.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class FeedDumpServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FeedManager
     */
    protected $feedManager;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var FeedDumpService
     */
    protected $service;

    /**
     * Sets up a dump service.
     */
    protected function setUp(): void
    {
        $this->feedManager = $this->getMockBuilder(FeedManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new FeedDumpService($this->feedManager, $this->entityManager, $this->filesystem);
    }

    /**
     * Tests the dump() method with an invalid order
     * Should throw a \InvalidArgumentException.
     */
    public function testDumpWithInvalidOrder()
    {
        if (!method_exists($this->filesystem, 'dumpFile')) {
            $this->markTestSkipped('Test skipped as Filesystem::dumpFile() is not available in this version.');
        }

        $this->expectException('\InvalidArgumentException');

        $this->service->setOrderBy('unexistant-order');
        $this->service->dump();
    }

    /**
     * Tests the dump() method with an entity.
     */
    public function testDumpWithAnEntity()
    {
        if (!method_exists($this->filesystem, 'dumpFile')) {
            $this->markTestSkipped('Test skipped as Filesystem::dumpFile() is not available in this version.');
        }

        // Given
        $this->service->setRootDir('/unit/test/');
        $this->service->setFilename('feed.rss');
        $this->service->setEntity(FakeItemInterfaceEntity::class);
        $this->service->setDirection('ASC');

        $entity = $this->createMock(FakeItemInterfaceEntity::class);

        $repository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects($this->once())->method('findBy')->will($this->returnValue([$entity, $entity]));

        $this->entityManager->expects($this->once())->method('getRepository')->will($this->returnValue($repository));

        $feed = $this->getMockBuilder(Feed::class)
            ->disableOriginalConstructor()
            ->getMock();

        $feed->expects($this->once())->method('addFromArray');
        $feed->expects($this->once())->method('render')->will($this->returnValue('XML content'));

        $this->feedManager->expects($this->once())->method('get')->will($this->returnValue($feed));

        $this->filesystem
            ->expects($this->once())
            ->method('dumpFile')
            ->with('/unit/test/feed.rss', 'XML content');

        // When - Expects actions
        $this->service->dump();
    }

    /**
     * Tests the dump() method without any items or entity set.
     */
    public function testDumpWithoutItemsOrEntity()
    {
        if (!method_exists($this->filesystem, 'dumpFile')) {
            $this->markTestSkipped('Test skipped as Filesystem::dumpFile() is not available in this version.');
        }

        $this->expectException('\LogicException');
        $this->expectExceptionMessage('An entity should be set OR you should use setItems() first');

        // Given
        $this->service->setRootDir('/unit/test/');
        $this->service->setFilename('feed.rss');
        $this->service->setDirection('ASC');

        $feed = $this->getMockBuilder(Feed::class)
            ->disableOriginalConstructor()
            ->getMock();

        $feed->expects($this->once())->method('hasItems')->will($this->returnValue(true));

        $this->feedManager->expects($this->once())->method('get')->will($this->returnValue($feed));

        // When - Expects exception
        $this->service->dump();
    }
}
