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

use Eko\FeedBundle\Feed\FeedManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;

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
     * @var string $name
     */
    private $name;

    /**
     * @var string $entity
     */
    private $entity;

    /**
     * @var string $filename
     */
    private $filename;

    /**
     * @var string $format
     */
    private $format;

    /**
     * @var integer $limit
     */
    private $limit;

    /**
     * @var string $direction
     */
    private $direction;

    /**
     * @var string $orderBy
     */
    private $orderBy;

    /**
     * @var string $rootDir
     */
    private $rootDir;

    /**
     * @param FeedManager   $feedManager
     * @param EntityManager $entityManager
     * @param Filesystem    $filesystem
     */
    public function __construct(FeedManager $feedManager, EntityManager $entityManager, Filesystem $filesystem)
    {
        $this->feedManager = $feedManager;
        $this->em          = $entityManager;
        $this->filesystem  = $filesystem;
    }

    public function dump()
    {
        $this->initDirection();
        $feed = $this->feedManager->get($this->name);

        if($this->entity) {
            $repository = $this->em->getRepository($this->entity);
            $items = $repository->findBy(array(), $this->orderBy, $this->limit);
            $feed->addFromArray($items);
        }

        $dump = $feed->render($this->format);

        $filepath = $this->rootDir . $this->filename;

        $this->filesystem->dumpFile($filepath, $dump);
    }

    private function initDirection()
    {
        if (null !== $this->orderBy) {
            switch ($this->direction) {
                case 'ASC':
                case 'DESC':
                    $this->orderBy = array($this->orderBy => $this->direction);
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('"direction" option should be set with "orderBy" and should be ASC or DESC'));
                    break;
            }
        }
    }

    /**
     * Sets items to the feed
     *
     * @param array $items items list
     *
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Sets the value of filename.
     *
     * @param mixed $filename the filename
     *
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Sets the value of format.
     *
     * @param mixed $format the format
     *
     * @return self
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Sets the value of limit.
     *
     * @param mixed $limit the limit
     *
     * @return self
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Sets the value of direction.
     *
     * @param mixed $direction the direction
     *
     * @return self
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Sets the value of orderBy.
     *
     * @param mixed $orderBy the order by
     *
     * @return self
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * Sets the value of rootDir.
     *
     * @param mixed $rootDir the root dir
     *
     * @return self
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;

        return $this;
    }
}