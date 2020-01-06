<?php
namespace Gento\Shipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Location extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('gento_shipping_location', 'location_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $locationId
     * @return array
     */
    public function lookupStoreIds($locationId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['store_table' => $this->getTable('gento_shipping_location_store')], 'store_id')
            ->join(
                ['main_table' => $this->getMainTable()],
                'store_table.location_id = main_table.location_id',
                []
            )->where('main_table.location_id = :location_id');
        return $connection->fetchCol($select, ['location_id' => (int)$locationId]);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel | \Gento\Shipping\Model\Location $object
     * @return $this | AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStoreId();
        $table  = $this->getTable('gento_shipping_location_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                'location_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            ];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'location_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel | \Gento\Shipping\Model\Location $object
     * @return $this|AbstractModel
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterLoad($object);
        $object->setStoreId($this->lookupStoreIds($object->getId()));
        return $this;
    }
}
