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

use Eko\FeedBundle\Feed;

/**
 * RSS formatter
 *
 * This class provides an RSS formatter
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class RssFormatter implements FormatterInterface
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
        $this->dom = new DOMDocument('1.0', 'utf-8');

        $root = $this->dom->createElement('rss');
        $root->setAttribute('version', '2.0');
        $root = $this->dom->appendChild($root);

        $channel = $this->dom->createElement('channel');
        $channel = $root->appendChild($channel);
    }

    /**
     * This method render the given feed transforming the DOMDocument to XML
     *
     * @return string
     */
    public function render()
    {
        $processor = new XSLTProcessor();
        $xml = $processor->transformToXML($this->dom);

        return $xml;
    }
}