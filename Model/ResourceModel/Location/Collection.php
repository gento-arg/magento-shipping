<?php
namespace Gento\Shipping\Model\ResourceModel\Location;

use Gento\Shipping\Model\Location;
use Gento\Shipping\Model\ResourceModel\AbstractCollection;

/**
 * @api
 */
class Collection extends AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Location::class,
            \Gento\Shipping\Model\ResourceModel\Location::class
        );
        $this->_map['fields']['store_id'] = 'store_table.store_id';
        $this->_map['fields']['location_id'] = 'main_table.location_id';
    }

    /**
     * after collection load
     */
    protected function _afterLoad()
    {
        $ids = [];
        foreach ($this as $item) {
            $ids[] = $item->getId();
        }
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                ['store_table' => $this->getTable('gento_shipping_location_store')]
            )->where('store_table.location_id IN (?)', $ids);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData['location_id']][] = $storeData['store_id'];
                }
                foreach ($this as $item) {
                    $linkedId = $item->getData('location_id');
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
        return parent::_afterLoad();
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        if (!isset($this->joinFields['store'])) {
            $this->getSelect()->join(
                [
                    'related_store' => $this->getTable('gento_shipping_location_store'),
                ],
                'related_store.location_id = main_table.location_id'
            );
            $this->getSelect()->where('related_store.store_id IN (?)', [$storeId, 0]);
            $this->joinFields['store'] = true;
        }
        return $this;
    }

    /**
     * Join store relation table
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->join(
            ['store_table' => $this->getTable('gento_shipping_location_store')],
            'main_table.location_id = store_table.location_id',
            []
        )->group(
            'main_table.location_id'
        );
        parent::_renderFiltersBefore();
    }

    public function getFilterZipcode($zipcode)
    {
        return $this->addFieldToFilter('zipcode', ['eq' => $zipcode]);
    }
}
