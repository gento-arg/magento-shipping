<?php
namespace Gento\Shipping\Model\Pickup;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Gento\Shipping\Model\ResourceModel\Pickup\CollectionFactory as PickupCollectionFactory;

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
     * constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param PickupCollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PickupCollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
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
            $this->loadedData[$pickup->getId()] = $pickup->getData();
        }
        $data = $this->dataPersistor->get('gento_shipping_pickup');
        if (!empty($data)) {
            $pickup = $this->collection->getNewEmptyItem();
            $pickup->setData($data);
            $this->loadedData[$pickup->getId()] = $pickup->getData();
            $this->dataPersistor->clear('gento_shipping_pickup');
        }
        return $this->loadedData;
    }
}
