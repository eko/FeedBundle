<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle;

use Eko\FeedBundle\DependencyInjection\Compiler\FeedDumpServicePass;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * EkoFeedBundle
 *
 * This is the main bundle class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class EkoFeedBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FeedDumpServicePass());
    }
}
