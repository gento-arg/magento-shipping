<?php
namespace Gento\Shipping\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Gento\Shipping\Api\Data\PickupInterface;
use Gento\Shipping\Api\Data\PickupInterfaceFactory;
use Gento\Shipping\Api\Data\PickupSearchResultInterfaceFactory;
use Gento\Shipping\Api\PickupRepositoryInterface;
use Gento\Shipping\Model\ResourceModel\Pickup as PickupResourceModel;
use Gento\Shipping\Model\ResourceModel\Pickup\Collection;
use Gento\Shipping\Model\ResourceModel\Pickup\CollectionFactory as PickupCollectionFactory;

class PickupRepository implements PickupRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Pickup resource model
     *
     * @var PickupResourceModel
     */
    protected $resource;

    /**
     * Pickup collection factory
     *
     * @var PickupCollectionFactory
     */
    protected $pickupCollectionFactory;

    /**
     * Pickup interface factory
     *
     * @var PickupInterfaceFactory
     */
    protected $pickupInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var PickupSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     * @param PickupResourceModel $resource
     * @param PickupCollectionFactory $pickupCollectionFactory
     * @param PickupnterfaceFactory $pickupInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param PickupSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        PickupResourceModel $resource,
        PickupCollectionFactory $pickupCollectionFactory,
        PickupInterfaceFactory $pickupInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        PickupSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource             = $resource;
        $this->pickupCollectionFactory = $pickupCollectionFactory;
        $this->pickupInterfaceFactory  = $pickupInterfaceFactory;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save Pickup.
     *
     * @param \Gento\Shipping\Api\Data\PickupInterface $pickup
     * @return \Gento\Shipping\Api\Data\PickupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(PickupInterface $pickup)
    {
        /** @var PickupInterface|\Magento\Framework\Model\AbstractModel $pickup */
        try {
            $this->resource->save($pickup);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Pickup: %1',
                $exception->getMessage()
            ));
        }
        return $pickup;
    }

    /**
     * Retrieve Pickup
     *
     * @param int $pickupId
     * @return \Gento\Shipping\Api\Data\PickupInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($pickupId)
    {
        if (!isset($this->instances[$pickupId])) {
            /** @var PickupInterface|\Magento\Framework\Model\AbstractModel $pickup */
            $pickup = $this->pickupInterfaceFactory->create();
            $this->resource->load($pickup, $pickupId);
            if (!$pickup->getId()) {
                throw new NoSuchEntityException(__('Requested Pickup doesn\'t exist'));
            }
            $this->instances[$pickupId] = $pickup;
        }
        return $this->instances[$pickupId];
    }

    /**
     * Retrieve Pickups matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Gento\Shipping\Api\Data\PickupSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Gento\Shipping\Api\Data\PickupSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Gento\Shipping\Model\ResourceModel\Pickup\Collection $collection */
        $collection = $this->pickupCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        } else {
            $collection->addOrder('main_table.' . PickupInterface::PICKUP_ID, SortOrder::SORT_ASC);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var PickupInterface[] $pickups */
        $pickups = [];
        /** @var \Gento\Shipping\Model\Pickup $pickup */
        foreach ($collection as $pickup) {
            /** @var PickupInterface $pickupDataObject */
            $pickupDataObject = $this->pickupInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $pickupDataObject,
                $pickup->getData(),
                PickupInterface::class
            );
            $pickups[] = $pickupDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($pickups);
    }

    /**
     * Delete Pickup
     *
     * @param PickupInterface $pickup
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PickupInterface $pickup)
    {
        /** @var PickupInterface|\Magento\Framework\Model\AbstractModel $pickup */
        $id = $pickup->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($pickup);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to removePickup %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Pickup by ID.
     *
     * @param int $pickupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($pickupId)
    {
        $pickup = $this->get($pickupId);
        return $this->delete($pickup);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }

    /**
     * clear caches instances
     * @return void
     */
    public function clear()
    {
        $this->instances = [];
    }
}
