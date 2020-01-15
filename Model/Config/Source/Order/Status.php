<?php

namespace Gento\Shipping\Model\Config\Source\Order;

use Magento\Sales\Model\Config\Source\Order\Status as OrderStatus;

class Status extends OrderStatus
{
    public function toOptionArray()
    {
        $opts = parent::toOptionArray();
        $opts[0] = [
            'value' => '',
            'label' => __('-- Default value --'),
        ];

        return $opts;
    }
}
