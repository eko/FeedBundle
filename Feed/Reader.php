<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Feed;

use Zend\Feed\Reader\Reader as ZendReader;
use Zend\Feed\Reader\Feed\FeedInterface;

/**
 * Reader
 *
 * This is the main feed reader class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class Reader
{
    /**
     * @var FeedInterface
     */
    protected $feed;

    /**
     * Loads feed from an url or file path
     *
     * @param string $file
     *
     * @return Reader
     */
    public function get($file)
    {
        if (file_exists($file)) {
            $this->feed = ZendReader::importFile($file);
        } else {
            $this->feed = ZendReader::import($file);
        }

        return $this->feed;
    }
}
