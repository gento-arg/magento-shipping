<?php
namespace Gento\Shipping\Api;

use Gento\Shipping\Api\Data\PickupInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface PickupRepositoryInterface
{
    /**
     * @param PickupInterface $Pickup
     * @return PickupInterface
     */
    public function save(PickupInterface $Pickup);

    /**
     * @param $id
     * @return PickupInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Gento\Shipping\Api\Data\PickupSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param PickupInterface $Pickup
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PickupInterface $Pickup);

    /**
     * @param int $PickupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($PickupId);

    /**
     * clear caches instances
     * @return void
     */
    public function clear();
    
    /**
     * @param int $PickupId
     * @param int $status
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function changeStatus($PickupId, $status);
}
