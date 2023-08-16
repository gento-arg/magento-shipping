<?php

namespace Gento\Shipping\Model\Carrier;

use Gento\Shipping\Model\ResourceModel\Pickup\CollectionFactory as ModelCollectionFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;

class Pickup extends AbstractCarrier implements CarrierInterface
{
    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = 'gento_shipping_carrier_pickup';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param ModelCollectionFactory
     */
    private $modelCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        ModelCollectionFactory $modelCollectionFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->modelCollectionFactory = $modelCollectionFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    public function getAllowedMethods()
    {
        return [
            $this->_code => $this->getConfigData('title'),
        ];
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $rateResult = $this->_rateResultFactory->create();

        $modelCollection = $this->modelCollectionFactory->create();
        $modelCollection->addFieldToFilter('active', ['eq' => 1]);

        $freeShipping = true;
        foreach ($request->getAllItems() as $item) {
            if (!$item->getFreeShipping() && !$item->getProduct()->isVirtual()) {
                $freeShipping = false;
            }
        }

        foreach ($modelCollection->getFilterActive() as $model) {
            $formattedDates = $model->getFormattedDates();

            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod($model->getId());
            $method->setMethodTitle($model->getTitle());
            $description = $model->getDescription();

            if ($formattedDates) {
                $description .= '<br>';
                $description .= __('Working days:');
                $description .= '<br>';
                $description .= nl2br($formattedDates);
            }

            $method->setMethodDescription($description);
            $method->setPrice($freeShipping ? 0 : $model->getPrice());
            $method->setCost($freeShipping ? 0 : $model->getPrice());

            $rateResult->append($method);
        }

        return $rateResult;
    }

    protected function getStoreConfig($path)
    {
        return $this->_scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );
    }

}
