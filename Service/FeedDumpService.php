<?php

namespace Eko\FeedBundle\Service;

use Eko\FeedBundle\Feed\FeedManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;

class FeedDumpService
{
    private $feedManager;
    private $em;
    private $filesystem;

    private $name;
    private $entity;
    private $filename;
    private $format;
    private $limit;
    private $direction;
    private $orderBy;
    private $rootDir;

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

        if(null !== $this->entity) {
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

    public function setItems(array $items)
    {
        $this->feedManager->get($this->name)->addFromArray($items);

        return $this;
    }

    public function setName($name){
        $this->name = $name;

        return $this;
    }

    public function setEntity($entity){
        $this->entity = $entity;

        return $this;
    }

    public function setFilename($filename){
        $this->filename = $filename;

        return $this;
    }

    public function setFormat($format){
        $this->format = $format;

        return $this;
    }

    public function setLimit($limit){
        $this->limit = $limit;

        return $this;
    }

    public function setDirection($direction){
        $this->direction = $direction;

        return $this;
    }

    public function setOrderBy($orderBy){
        $this->orderBy = $orderBy;

        return $this;
    }

    public function setRootDir($rootDir){
        $this->rootDir = $rootDir;

        return $this;
    }
}