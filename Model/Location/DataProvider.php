<?php
namespace Gento\Shipping\Model\Location;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Gento\Shipping\Model\ResourceModel\Location\CollectionFactory as LocationCollectionFactory;

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
     * @param LocationCollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        LocationCollectionFactory $collectionFactory,
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
        /** @var \Gento\Shipping\Model\Location $location */
        foreach ($items as $location) {
            $this->loadedData[$location->getId()] = $location->getData();
        }
        $data = $this->dataPersistor->get('gento_shipping_location');
        if (!empty($data)) {
            $location = $this->collection->getNewEmptyItem();
            $location->setData($data);
            $this->loadedData[$location->getId()] = $location->getData();
            $this->dataPersistor->clear('gento_shipping_location');
        }
        return $this->loadedData;
    }
}
