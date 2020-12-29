<?php
namespace Gento\Shipping\Model;

use Gento\Shipping\Api\Data\PickupInterface;
use Gento\Shipping\Model\ResourceModel\Pickup as PickupResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json as JsonHelper;

/**
 * @method \Gento\Shipping\Model\ResourceModel\Pickup _getResource()
 * @method \Gento\Shipping\Model\ResourceModel\Pickup getResource()
 */
class Pickup extends AbstractModel implements PickupInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'gento_shipping_pickup';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'gento_shipping_pickup';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'pickup';

    protected $_formattedDates = null;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        JsonHelper $jsonHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->jsonHelper = $jsonHelper;
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PickupResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Page id
     *
     * @return array
     */
    public function getPickupId()
    {
        return $this->getData(PickupInterface::PICKUP_ID);
    }

    /**
     * set Pickup id
     *
     * @param  int $pickupId
     * @return PickupInterface
     */
    public function setPickupId($pickupId)
    {
        return $this->setData(PickupInterface::PICKUP_ID, $pickupId);
    }

    /**
     * @param string $title
     * @return PickupInterface
     */
    public function setTitle($title)
    {
        return $this->setData(PickupInterface::TITLE, $title);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(PickupInterface::TITLE);
    }

    /**
     * @param string $dates
     * @return PickupInterface
     */
    public function setDates($dates)
    {
        return $this->setData(PickupInterface::DATES, $dates);
    }

    /**
     * @return string
     */
    public function getDates()
    {
        return $this->getData(PickupInterface::DATES);
    }

    /**
     * @param string $description
     * @return PickupInterface
     */
    public function setDescription($description)
    {
        return $this->setData(PickupInterface::DESCRIPTION, $description);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(PickupInterface::DESCRIPTION);
    }

    /**
     * @param float $price
     * @return PickupInterface
     */
    public function setPrice($price)
    {
        return $this->setData(PickupInterface::PRICE, $price);
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->getData(PickupInterface::PRICE);
    }

    /**
     * @param int $active
     * @return PickupInterface
     */
    public function setActive($active)
    {
        return $this->setData(PickupInterface::ACTIVE, $active);
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->getData(PickupInterface::ACTIVE);
    }
    /**
     * @param array $storeId
     * @return PickupInterface
     */
    public function setStoreId(array $storeId)
    {
        return $this->setData(PickupInterface::STORE_ID, $storeId);
    }

    /**
     * @return int[]
     */
    public function getStoreId()
    {
        return $this->getData(PickupInterface::STORE_ID);
    }

    public function getFormattedDates()
    {
        if ($this->_formattedDates === null) {
            $this->_formattedDates = '';
            if ($this->getDates() === null) {
                return $this->_formattedDates;
            }

            $dates = $this->jsonHelper->unserialize($this->getDates());
            foreach ($dates as $day => $dayConfig) {
                if (!!$dayConfig['enabled']) {
                    $this->_formattedDates .= sprintf('%s: %s-%s',
                        __(ucfirst($day)),
                        $dayConfig['start'],
                        $dayConfig['end']
                    ) . PHP_EOL;

                    if (!!$dayConfig['lunch_enabled']) {
                        $this->_formattedDates .= __('Lunch time: %1-%2',
                            $dayConfig['start_lunch'],
                            $dayConfig['end_lunch']
                        ) . PHP_EOL;
                    }
                }
            }
        }
        return $this->_formattedDates;
    }
}
