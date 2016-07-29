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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration.
 *
 * This class generates configuration settings tree
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Builds configuration tree.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder A tree builder instance
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('eko_feed');

        $rootNode
            ->children()
                ->scalarNode('hydrator')->defaultValue('eko_feed.hydrator.default')->end()
                ->scalarNode('translation_domain')->defaultNull()->end()
                ->arrayNode('feeds')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('title')->isRequired()->end()
                            ->scalarNode('description')->isRequired()->end()
                            ->arrayNode('link')
                                ->isRequired()
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(function ($value) {
                                        return ['uri' => $value];
                                    })
                                ->end()
                                ->children()
                                    ->scalarNode('route_name')->end()
                                    ->arrayNode('route_params')
                                        ->useAttributeAsKey('key')
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->scalarNode('uri')->end()
                                ->end()
                            ->end()
                            ->scalarNode('encoding')->isRequired()->end()
                            ->scalarNode('author')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
