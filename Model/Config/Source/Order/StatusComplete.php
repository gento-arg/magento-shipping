<?php

namespace Gento\Shipping\Model\Config\Source\Order;

use Magento\Sales\Model\Order;

class StatusComplete extends Status
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = [Order::STATE_COMPLETE];
}
