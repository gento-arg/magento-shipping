<?php
namespace Gento\Shipping\Api;

use Gento\Shipping\Api\Data\LocationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface LocationRepositoryInterface
{
    /**
     * @param LocationInterface $Location
     * @return LocationInterface
     */
    public function save(LocationInterface $Location);

    /**
     * @param $id
     * @return LocationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Gento\Shipping\Api\Data\LocationSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param LocationInterface $Location
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(LocationInterface $Location);

    /**
     * @param int $LocationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($LocationId);

    /**
     * clear caches instances
     * @return void
     */
    public function clear();
}
