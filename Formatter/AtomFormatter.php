<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Formatter;

use Eko\FeedBundle\Feed\Feed;

/**
 * Atom formatter
 *
 * This class provides an Atom formatter
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class AtomFormatter implements FormatterInterface
{
    /**
     * @var Feed $feed  A feed instance
     */
    protected $feed;

    /**
     * @var DOMDocument $dom  XML DOMDocument
     */
    protected $dom;

    /**
     * Construct a formatter with given feed
     *
     * @param Feed $feed  A feed instance
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
        $this->initialize();
    }

    /**
     * Initialize XML DOMDocument
     */
    public function initialize()
    {
        $this->dom = new \DOMDocument('1.0', 'utf-8');
    }

    /**
     * This method render the given feed transforming the DOMDocument to XML
     *
     * @return string
     */
    public function render()
    {
        return $this->dom->saveXml();
    }
}