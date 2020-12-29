<?php
namespace Gento\Shipping\Model\Pickup;

use Gento\Shipping\Model\ResourceModel\Pickup\CollectionFactory as PickupCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonHelper;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * Loaded data cache
     *
     * @var array
     */
    protected $loadedData;

    /**
     * Data persistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param PickupCollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param JsonHelper $jsonHelper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PickupCollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        JsonHelper $jsonHelper,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Gento\Shipping\Model\Pickup $pickup */
        foreach ($items as $pickup) {
            $data = $pickup->getData();
            $data = $this->parseDates($data);
            $this->loadedData[$pickup->getId()] = $data;
        }
        $data = $this->dataPersistor->get('gento_shipping_pickup');
        if (!empty($data)) {
            $pickup = $this->collection->getNewEmptyItem();
            $pickup->setData($data);
            $data = $pickup->getData();
            $data = $this->parseDates($data);
            $this->loadedData[$pickup->getId()] = $data;
            $this->dataPersistor->clear('gento_shipping_pickup');
        }
        return $this->loadedData;
    }

    protected function parseDates($data)
    {
        if (isset($data['dates'])) {
            $dates = $this->jsonHelper->unserialize($data['dates']);
            foreach ($dates as $day => $dayData) {
                $data[$day] = $dayData;
            }
        }

        return $data;
    }
}
