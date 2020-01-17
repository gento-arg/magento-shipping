<?php
namespace Gento\Shipping\Model;

use Gento\Shipping\Api\Data\LocationInterface;
use Gento\Shipping\Api\Data\LocationInterfaceFactory;
use Gento\Shipping\Api\Data\LocationSearchResultInterfaceFactory;
use Gento\Shipping\Api\LocationRepositoryInterface;
use Gento\Shipping\Model\ResourceModel\Location as LocationResourceModel;
use Gento\Shipping\Model\ResourceModel\Location\Collection;
use Gento\Shipping\Model\ResourceModel\Location\CollectionFactory as LocationCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Location resource model
     *
     * @var LocationResourceModel
     */
    protected $resource;

    /**
     * Location collection factory
     *
     * @var LocationCollectionFactory
     */
    protected $locationCollectionFactory;

    /**
     * Location interface factory
     *
     * @var LocationInterfaceFactory
     */
    protected $locationInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var LocationSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     * @param LocationResourceModel $resource
     * @param LocationCollectionFactory $locationCollectionFactory
     * @param LocationnterfaceFactory $locationInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param LocationSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        LocationResourceModel $resource,
        LocationCollectionFactory $locationCollectionFactory,
        LocationInterfaceFactory $locationInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        LocationSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->locationInterfaceFactory = $locationInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Save Location.
     *
     * @param \Gento\Shipping\Api\Data\LocationInterface $location
     * @return \Gento\Shipping\Api\Data\LocationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(LocationInterface $location)
    {
        /** @var LocationInterface|\Magento\Framework\Model\AbstractModel $location */
        try {
            $this->resource->save($location);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Location: %1',
                $exception->getMessage()
            ));
        }
        return $location;
    }

    /**
     * Retrieve Location
     *
     * @param int $locationId
     * @return \Gento\Shipping\Api\Data\LocationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($locationId)
    {
        if (!isset($this->instances[$locationId])) {
            /** @var LocationInterface|\Magento\Framework\Model\AbstractModel $location */
            $location = $this->locationInterfaceFactory->create();
            $this->resource->load($location, $locationId);
            if (!$location->getId()) {
                throw new NoSuchEntityException(__('Requested Location doesn\'t exist'));
            }
            $this->instances[$locationId] = $location;
        }
        return $this->instances[$locationId];
    }

    /**
     * Retrieve Locations matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Gento\Shipping\Api\Data\LocationSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Gento\Shipping\Api\Data\LocationSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Gento\Shipping\Model\ResourceModel\Location\Collection $collection */
        $collection = $this->locationCollectionFactory->create();

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
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ?
                    SortOrder::SORT_ASC : SortOrder::SORT_DESC
                );
            }
        } else {
            $collection->addOrder('main_table.' . LocationInterface::LOCATION_ID, SortOrder::SORT_ASC);
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var LocationInterface[] $locations */
        $locations = [];
        /** @var \Gento\Shipping\Model\Location $location */
        foreach ($collection as $location) {
            /** @var LocationInterface $locationDataObject */
            $locationDataObject = $this->locationInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $locationDataObject,
                $location->getData(),
                LocationInterface::class
            );
            $locations[] = $locationDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($locations);
    }

    /**
     * Delete Location
     *
     * @param LocationInterface $location
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(LocationInterface $location)
    {
        /** @var LocationInterface|\Magento\Framework\Model\AbstractModel $location */
        $id = $location->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($location);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Location %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Location by ID.
     *
     * @param int $locationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($locationId)
    {
        $location = $this->get($locationId);
        return $this->delete($location);
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
