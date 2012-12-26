<?php

namespace Eko\FeedBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * FeedDumpCommand
 *
 * This command generate a feed in an XML file
 *
 * @author Vincent Composieux <composieux@ekino.com>
 */
class FeedDumpCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Generate (dump) a feed in an XML file')
            ->setName('eko:feed:dump')
            ->addOption('name',      null, InputOption::VALUE_REQUIRED, 'Feed name defined in eko_feed configuration')
            ->addOption('entity',    null, InputOption::VALUE_REQUIRED, 'Entity to use to generate the feed')
            ->addOption('filename',  null, InputOption::VALUE_REQUIRED, 'Defines feed filename')
            ->addOption('orderBy',   null, InputOption::VALUE_OPTIONAL, 'Order field to sort by using findBy() method')
            ->addOption('direction', null, InputOption::VALUE_OPTIONAL, 'Direction to give to sort field with findBy() method')
            ->addOption('format',    null, InputOption::VALUE_OPTIONAL, 'Formatter to use to generate, "rss" is default')
            ->addOption('limit',     null, InputOption::VALUE_OPTIONAL, 'Defines a limit of entity items to retrieve');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getOption('name');
        $entity = $input->getOption('entity');
        $filename = $input->getOption('filename');
        $format = $input->getOption('format') ?: 'rss';
        $limit = $input->getOption('limit');

        $direction = $input->getOption('direction');
        $orderBy = $input->getOption('orderBy');

        if (null !== $orderBy) {
            switch ($direction) {
                case 'ASC':
                case 'DESC':
                    $orderBy = array($input->getOption('orderBy') => $direction);
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('"direction" option should be set with "orderBy" and should be ASC or DESC'));
                    break;
            }
        }

        $feed = $this->getFeed($name);

        $output->writeln(sprintf('<info>Start dumping "%s" feed from "%s" entity...</info>', $name, $entity));

        $repository = $this->getEntityManager()->getRepository($entity);
        $items = $repository->findBy(array(), $orderBy, $limit);

        $feed->addFromArray($items);
        $dump = $feed->render($format);

        $filepath = realpath($this->getWebPath() . $filename);

        $file = fopen($filepath, 'w');
        fwrite($file, $dump);
        fclose($file);

        $output->writeln('<comment>done!</comment>');
        $output->writeln(sprintf('<info>Feed has been dumped and located in "%s"</info>', $filepath));
    }

    /**
     * Get feed from specified name
     *
     * @param string $name
     *
     * @return \Eko\FeedBundle\Feed\Feed
     */
    protected function getFeed($name)
    {
        return $this->getContainer()->get('eko_feed.feed.manager')->get($name);
    }

    /**
     * Get Doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getEntityManager();
    }

    /**
     * Get Symfony web path
     *
     * @return mixed
     */
    protected function getWebPath()
    {
        return $this->getContainer()->get('kernel')->getRootDir().'/../web/';
    }
}

