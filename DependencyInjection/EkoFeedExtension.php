<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * EkoFeedExtension.
 *
 * This class loads services.xml file and tree configuration
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class EkoFeedExtension extends Extension
{
    /**
     * Configuration extension loader.
     *
     * @param array            $configs   An array of configuration settings
     * @param ContainerBuilder $container A container builder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('feed.xml');
        $loader->load('formatter.xml');
        $loader->load('hydrator.xml');
        $loader->load('command.xml');

        $container->setParameter('eko_feed.config', $config);
        $container->setParameter('eko_feed.translation_domain', $config['translation_domain']);

        $this->configureHydrator($config, $container);
    }

    /**
     * Configures feed reader hydrator service.
     *
     * @param array            $config    Bundle configuration values array
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \RuntimeException
     */
    protected function configureHydrator(array $config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition($config['hydrator'])) {
            throw new \RuntimeException(sprintf('Unable to load hydrator service "%s"', $config['hydrator']));
        }

        $container->getDefinition('Eko\FeedBundle\Feed\Reader')
            ->addMethodCall('setHydrator', [new Reference($config['hydrator'])]);
    }
}
