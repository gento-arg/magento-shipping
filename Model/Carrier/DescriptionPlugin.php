<?php

namespace Gento\Shipping\Model\Carrier;

use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;

/**
 * Class Description
 *
 */
class DescriptionPlugin
{
    /**
     * @var ShippingMethodExtensionFactory
     */
    protected $extensionFactory;

    /**
     * Description constructor.
     * @param ShippingMethodExtensionFactory $extensionFactory
     */
    public function __construct(
        ShippingMethodExtensionFactory $extensionFactory
    ) {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param $subject
     * @param $result
     * @param $rateModel
     * @return mixed
     */
    public function afterModelToDataObject($subject, $result, $rateModel)
    {
        $extensionAttribute = $result->getExtensionAttributes() ?
        $result->getExtensionAttributes() :
        $this->extensionFactory->create();
        $extensionAttribute->setMethodDescription($rateModel->getMethodDescription());
        $result->setExtensionAttributes($extensionAttribute);
        return $result;
    }
}
