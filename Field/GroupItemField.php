<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Field;

use Eko\FeedBundle\Field\ItemField;
use Eko\FeedBundle\Field\ItemFieldInterface;

/**
 * GroupField
 *
 * This is items group field class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class GroupItemField implements ItemFieldInterface
{
    /**
     * @var string $name Field name
     */
    protected $name;

    /**
     * @var ItemField ItemField instance
     */
    protected $itemField;

    /**
     * Constructor
     *
     * @param string    $name      A group field name
     * @param ItemField $itemField A ItemField instance
     */
    public function __construct($name, ItemField $itemField)
    {
        $this->name      = $name;
        $this->itemField = $itemField;
    }

    /**
     * Returns group field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns item field
     *
     * @return ItemField
     */
    public function getItemField()
    {
        return $this->itemField;
    }
}
