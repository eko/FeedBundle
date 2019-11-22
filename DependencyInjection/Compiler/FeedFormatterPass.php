<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\DependencyInjection\Compiler;

use Eko\FeedBundle\Feed\FeedManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Retrieve all formatters and inject them into the feed manager service.
 *
 * @author Vincent Composieux <vincent.composieux@gail.com>
 */
class FeedFormatterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $formatters = [];

        $services = $container->findTaggedServiceIds('eko_feed.formatter');

        foreach ($services as $identifier => $options) {
            $options = current($options);
            $formatters[$options['format']] = new Reference($identifier);
        }

        $manager = $container->getDefinition(FeedManager::class);
        $manager->replaceArgument(2, $formatters);
    }
}
