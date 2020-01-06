<?php
namespace Gento\Shipping\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface LocationSearchResultInterface
{
    /**
     * get items
     *
     * @return \Gento\Shipping\Api\Data\LocationInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Gento\Shipping\Api\Data\LocationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count);
}
