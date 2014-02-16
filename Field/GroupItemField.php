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
     * @var array ItemField instances
     */
    protected $itemFields;

    /**
     * Constructor
     *
     * @param string          $name       A group field name
     * @param array|ItemField $itemFields An array or a single ItemField instance
     */
    public function __construct($name, $itemFields)
    {
        $this->name = $name;

        if (!is_array($itemFields) && !$itemFields instanceof ItemField) {
            throw new \RuntimeException('GroupItemField second arguments should be an array or a single ItemField instance');
        }

        if (!is_array($itemFields)) {
            $itemFields = array($itemFields);
        }

        $this->itemFields = $itemFields;
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
     * Returns item fields
     *
     * @return array
     */
    public function getItemFields()
    {
        return $this->itemFields;
    }
}
