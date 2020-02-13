<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Command;

use Laminas\Loader\Exception\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * FeedDumpCommand.
 *
 * This command generate a feed in an XML file
 *
 * @author Vincent Composieux <composieux@ekino.com>
 */
class FeedDumpCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Generate (dump) a feed in an XML file')
            ->setName('eko:feed:dump')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Feed name defined in eko_feed configuration')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'Entity to use to generate the feed')
            ->addOption('filename', null, InputOption::VALUE_REQUIRED, 'Defines feed filename')
            ->addOption('orderBy', null, InputOption::VALUE_OPTIONAL, 'Order field to sort by using findBy() method')
            ->addOption('direction', null, InputOption::VALUE_OPTIONAL, 'Direction to give to sort field with findBy() method')
            ->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Formatter to use to generate, "rss" is default')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Defines a limit of entity items to retrieve')
            ->addArgument('host', InputArgument::REQUIRED, 'Set the host');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getContainer()->has('eko_feed.feed.dump')) {
            throw new RuntimeException('The "eko_feed.feed.dump" service used in this command requires Doctrine ORM to be configured.');
        }

        $name = $input->getOption('name');
        $entity = $input->getOption('entity');
        $filename = $input->getOption('filename');
        $format = $input->getOption('format') ?: 'rss';
        $limit = $input->getOption('limit');
        $direction = $input->getOption('direction');
        $orderBy = $input->getOption('orderBy');

        $this->getContainer()->get('router')->getContext()->setHost($input->getArgument('host'));

        $feedDumpService = $this->getContainer()->get('eko_feed.feed.dump');
        $feedDumpService
                ->setName($name)
                ->setEntity($entity)
                ->setFilename($filename)
                ->setFormat($format)
                ->setLimit($limit)
                ->setDirection($direction)
                ->setOrderBy($orderBy);

        $feedDumpService->dump();

        $output->writeln('<comment>done!</comment>');
        $output->writeln(sprintf('<info>Feed has been dumped and located in "%s"</info>', $feedDumpService->getRootDir().$filename));
    }
}
