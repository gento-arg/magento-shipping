<?php
namespace Gento\Shipping\Model\Search;

use Magento\Framework\DataObject;
use Magento\Backend\Helper\Data;
use Gento\Shipping\Model\ResourceModel\Pickup\CollectionFactory;

/**
 * @api
 */
class Pickup extends DataObject
{
    /**
     * Adminhtml data
     *
     * @var Data
     */
    protected $adminhtmlData = null;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Data $adminhtmlData
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Data $adminhtmlData
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->adminhtmlData = $adminhtmlData;
    }

    /**
     * Load search results
     *
     * @return $this
     */
    public function load()
    {
        $result = [];
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);
            return $this;
        }

        $query = $this->getQuery();
        $collection = $this->collectionFactory->create()->addFieldToFilter(
            'title',
            ['like' => '%' . $query . '%']
        )->setCurPage(
            $this->getStart()
        )->setPageSize(
            $this->getLimit()
        )->load();

        foreach ($collection as $item) {
            $result[] = [
                'id' => 'pickup' . $item->getId(),
                'type' => __('Pickup'),
                'name' => __('Pickup %1', $item->getTitle()),
                'description' => __('Pickup %1', $item->getTitle()),
                'url' => $this->adminhtmlData->getUrl(
                    'gento_shipping/pickup/edit',
                    ['pickup_id' => $item->getId()]
                ),
            ];
        }

        $this->setResults($result);

        return $this;
    }
}
