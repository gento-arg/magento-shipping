<?php

namespace Gento\Shipping\Model\Config\Source\Order;

use Magento\Sales\Model\Order;

class StatusPending extends Status
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = [Order::STATE_PROCESSING];
}
