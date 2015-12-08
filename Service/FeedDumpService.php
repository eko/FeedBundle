<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Service;

use Doctrine\ORM\EntityManager;
use Eko\FeedBundle\Feed\FeedManager;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FeedDumpService.
 *
 * This class helps to dump your feeds on filesystem
 *
 * @author Thomas P. <http://github.com/ScullWM>
 */
class FeedDumpService
{
    /**
     * @var FeedManager
     */
    private $feedManager;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $format;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var string
     */
    private $direction;

    /**
     * @var string
     */
    private $orderBy;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * Constructor.
     *
     * @param FeedManager   $feedManager   A Feed manager
     * @param EntityManager $entityManager A Doctrine entity manager
     * @param Filesystem    $filesystem    A Symfony Filesystem component
     */
    public function __construct(FeedManager $feedManager, EntityManager $entityManager, Filesystem $filesystem)
    {
        $this->feedManager = $feedManager;
        $this->em = $entityManager;
        $this->filesystem = $filesystem;
    }

    /**
     * Dumps a feed from an entity or feed items using Filesystem component.
     *
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function dump()
    {
        if (!method_exists($this->filesystem, 'dumpFile')) {
            throw new \RuntimeException('Method dumpFile() is not available on your Filesystem component version, you should upgrade it.');
        }

        $this->initDirection();
        $feed = $this->feedManager->get($this->name);

        if ($this->entity) {
            $repository = $this->em->getRepository($this->entity);
            $items = $repository->findBy([], $this->orderBy, $this->limit);
            $feed->addFromArray($items);
        } elseif ($feed->hasItems()) {
            throw new \LogicException(sprintf('An entity should be set OR you should use setItems() first'));
        }

        $dump = $feed->render($this->format);
        $filepath = $this->rootDir.$this->filename;

        $this->filesystem->dumpFile($filepath, $dump);
    }

    /**
     * Initialize ordering.
     *
     * @throws \InvalidArgumentException
     */
    private function initDirection()
    {
        if (null !== $this->orderBy) {
            switch ($this->direction) {
                case 'ASC':
                case 'DESC':
                    $this->orderBy = [$this->orderBy => $this->direction];
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('"direction" option should be set with "orderBy" and should be ASC or DESC'));
                    break;
            }
        }
    }

    /**
     * Sets items to the feed.
     *
     * @param array $items items list
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->feedManager->get($this->name)->addFromArray($items);

        return $this;
    }

    /**
     * Sets the value of name.
     *
     * @param mixed $name the name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the value of entity.
     *
     * @param mixed $entity the entity
     *
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Sets the value of filename.
     *
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Sets the value of format.
     *
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Sets the value of limit.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Sets the value of direction.
     *
     * @param string $direction
     *
     * @return $this
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Sets the value of orderBy.
     *
     * @param string $orderBy
     *
     * @return $this
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Sets the value of rootDir.
     *
     * @param string $rootDir
     *
     * @return $this
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;

        return $this;
    }

    /**
     * Return rootdir.
     *
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }
}
