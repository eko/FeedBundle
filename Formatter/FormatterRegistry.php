<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Formatter;

/**
 * FormatterRegistry.
 *
 * This class provides all registered formatters.
 *
 * @author Daniel Siepmann <coding@daniel-siepmann.de>
 */
class FormatterRegistry
{
    /**
     * @var array
     */
    protected $formatters = [];

    /**
     * Add a new formatter to the registry.
     *
     * @param string             $format    The format to render (RSS, Atom, ...)
     * @param FormatterInterface $formatter The formatter to use for given format.
     *
     * @return void
     */
    public function addFormatter($format, $formatter)
    {
        $this->formatters[$format] = $formatter;
    }

    /**
     * Returns whether the given format is supported.
     *
     * @param string $format The format to render (RSS, Atom, ...)
     *
     * @return bool
     */
    public function supportsFormat($format)
    {
        return isset($this->formatters[$format]);
    }

    /**
     * Returns the formatter for requested format.
     *
     * @param string $format The format to render (RSS, Atom, ...)
     *
     * @return FormatterInterface
     */
    public function getFormatter($format)
    {
        return $this->formatters[$format];
    }
}
