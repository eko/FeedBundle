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

use Eko\FeedBundle\Service\FeedDumpService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * FeedDumpCommand.
 *
 * This command generate a feed in an XML file
 *
 * @author Vincent Composieux <composieux@ekino.com>
 */
class FeedDumpCommand extends Command
{
    protected static $defaultName = 'eko:feed:dump';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FeedDumpService|null
     */
    private $feedDumpService;

    public function __construct(RouterInterface $router, FeedDumpService $feedDumpService = null)
    {
        $this->router = $router;
        $this->feedDumpService = $feedDumpService;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Generate (dump) a feed in an XML file')
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (null === $this->feedDumpService) {
            throw new \RuntimeException(sprintf(
                'The "%s" service used in this command requires Doctrine ORM to be configured.',
                FeedDumpService::class
            ));
        }

        $name = $input->getOption('name');
        $entity = $input->getOption('entity');
        $filename = $input->getOption('filename');
        $format = $input->getOption('format') ?: 'rss';
        $limit = $input->getOption('limit');
        $direction = $input->getOption('direction');
        $orderBy = $input->getOption('orderBy');

        $this->router->getContext()->setHost($input->getArgument('host'));

        $this->feedDumpService
                ->setName($name)
                ->setEntity($entity)
                ->setFilename($filename)
                ->setFormat($format)
                ->setLimit($limit)
                ->setDirection($direction)
                ->setOrderBy($orderBy);

        $this->feedDumpService->dump();

        $output->writeln('<comment>done!</comment>');
        $output->writeln(sprintf('<info>Feed has been dumped and located in "%s"</info>', $this->feedDumpService->getRootDir().$filename));

        return 0;
    }
}
