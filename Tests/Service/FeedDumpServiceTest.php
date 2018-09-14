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

use Eko\FeedBundle\Service\FeedDumpService;

/**
 * FeedDumpServiceTest.
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class FeedDumpServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Eko\FeedBundle\Feed\FeedManager
     */
    protected $feedManager;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var FeedDumpService
     */
    protected $service;

    /**
     * Sets up a dump service.
     */
    public function setUp()
    {
        $this->feedManager = $this->getMockBuilder('Eko\FeedBundle\Feed\FeedManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystem = $this->getMockBuilder('Symfony\Component\Filesystem\Filesystem')
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

        $this->setExpectedException('\InvalidArgumentException');

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
        $this->service->setEntity('Eko\FeedBundle\Tests\Entity\Writer\FakeItemInterfaceEntity');
        $this->service->setDirection('ASC');

        $entity = $this->createMock('Eko\FeedBundle\Tests\Entity\Writer\FakeItemInterfaceEntity');

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects($this->once())->method('findBy')->will($this->returnValue([$entity, $entity]));

        $this->entityManager->expects($this->once())->method('getRepository')->will($this->returnValue($repository));

        $feed = $this->getMockBuilder('Eko\FeedBundle\Feed\Feed')
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

        $this->setExpectedException('\LogicException', 'An entity should be set OR you should use setItems() first');

        // Given
        $this->service->setRootDir('/unit/test/');
        $this->service->setFilename('feed.rss');
        $this->service->setDirection('ASC');

        $feed = $this->getMockBuilder('Eko\FeedBundle\Feed\Feed')
            ->disableOriginalConstructor()
            ->getMock();

        $feed->expects($this->once())->method('hasItems')->will($this->returnValue(true));

        $this->feedManager->expects($this->once())->method('get')->will($this->returnValue($feed));

        // When - Expects exception
        $this->service->dump();
    }
}
