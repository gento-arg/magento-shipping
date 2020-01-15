<?php

namespace Gento\Shipping\Model\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Service\OrderService;

class OrderServicePlugin
{
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundSetState(
        OrderService $subject,
        \Closure $proceed,
        OrderInterface $order,
        $state,
        $status = false,
        $comment = '',
        $isCustomerNotified = null,
        $shouldProtectState = true
    ) {
        $shippingMethod = $order->getShippingDescription()->getShippingMethod();
        $path = 'carriers/' . $shippingMethod . '/';
        if ($order->canInvoice()) {
            $path .= 'gento_shipping_status_process';
        } else {
            $path .= 'gento_shipping_status_complete';
        }
        $status = $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );
        $status = $status === '' ? true : $status;

        return $proceed($order, $state, $status, $comment, $isCustomerNotified, $shouldProtectState);
    }
}
