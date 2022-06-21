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

use Eko\FeedBundle\Formatter\FormatterRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Retrieve all formatters and inject them into the formatter registry.
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
        $services = $container->findTaggedServiceIds('eko_feed.formatter');
        $registry = $container->getDefinition(FormatterRegistry::class);

        foreach ($services as $identifier => $options) {
            $options = current($options);
            $registry->addMethodCall(
                'addFormatter',
                [
                    $options['format'],
                    new Reference($identifier),
                ]
            );
        }
    }
}
