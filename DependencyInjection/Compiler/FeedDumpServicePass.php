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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Check if the eko_feed.feed.dump is missing dependencies
 * and if so remove the service
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class FeedDumpServicePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasAlias('doctrine.orm.entity_manager')) {
            $container->removeDefinition('eko_feed.feed.dump');
        }
    }
}
