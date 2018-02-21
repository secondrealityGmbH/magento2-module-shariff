<?php

namespace Dreipunktnull\Shariff\Model\Config\Settings;

use Magento\Framework\Option\ArrayInterface;

class Theme implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'standard', 'label' => 'standard'],
            ['value' => 'grey', 'label' => 'grey'],
            ['value' => 'white', 'label' => 'white'],
        ];
    }
}
