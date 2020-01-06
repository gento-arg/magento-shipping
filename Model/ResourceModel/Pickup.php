<?php
namespace Gento\Shipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Pickup extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('gento_shipping_pickup', 'pickup_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $pickupId
     * @return array
     */
    public function lookupStoreIds($pickupId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['store_table' => $this->getTable('gento_shipping_pickup_store')], 'store_id')
            ->join(
                ['main_table' => $this->getMainTable()],
                'store_table.pickup_id = main_table.pickup_id',
                []
            )->where('main_table.pickup_id = :pickup_id');
        return $connection->fetchCol($select, ['pickup_id' => (int)$pickupId]);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel | \Gento\Shipping\Model\Pickup $object
     * @return $this | AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStoreId();
        $table  = $this->getTable('gento_shipping_pickup_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                'pickup_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            ];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'pickup_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel | \Gento\Shipping\Model\Pickup $object
     * @return $this|AbstractModel
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterLoad($object);
        $object->setStoreId($this->lookupStoreIds($object->getId()));
        return $this;
    }
}
