<?php
/*
 * This file is part of the EkoFeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * EkoFeedExtension
 *
 * This class loads services.xml file and tree configuration
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class EkoFeedExtension extends Extension
{
    /**
     * Configuration extension loader
     *
     * @param array            $configs    An array of configuration settings
     * @param ContainerBuilder $container  A container builder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('ekofeed.config', $config);
    }

}