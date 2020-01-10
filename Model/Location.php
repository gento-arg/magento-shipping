<?php
namespace Gento\Shipping\Model;

use Gento\Shipping\Api\Data\LocationInterface;
use Gento\Shipping\Model\ResourceModel\Location as LocationResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * @method \Gento\Shipping\Model\ResourceModel\Location _getResource()
 * @method \Gento\Shipping\Model\ResourceModel\Location getResource()
 */
class Location extends AbstractModel implements LocationInterface
{
    /**
     * @var string
     */
    const CACHE_TAG = 'gento_shipping_location';
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
    protected $_eventPrefix = 'gento_shipping_location';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'location';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LocationResourceModel::class);
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
    public function getLocationId()
    {
        return $this->getData(LocationInterface::LOCATION_ID);
    }

    /**
     * set Location id
     *
     * @param  int $locationId
     * @return LocationInterface
     */
    public function setLocationId($locationId)
    {
        return $this->setData(LocationInterface::LOCATION_ID, $locationId);
    }

    /**
     * @param string $title
     * @return LocationInterface
     */
    public function setTitle($title)
    {
        return $this->setData(LocationInterface::TITLE, $title);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(LocationInterface::TITLE);
    }

    /**
     * @param string $description
     * @return LocationInterface
     */
    public function setDescription($description)
    {
        return $this->setData(LocationInterface::DESCRIPTION, $description);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(LocationInterface::DESCRIPTION);
    }

    /**
     * @param float $price
     * @return LocationInterface
     */
    public function setPrice($price)
    {
        return $this->setData(LocationInterface::PRICE, $price);
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->getData(LocationInterface::PRICE);
    }

    /**
     * @param string $zipcode
     * @return LocationInterface
     */
    public function setZipcode($zipcode)
    {
        return $this->setData(LocationInterface::ZIPCODE, $zipcode);
    }

    /**
     * @return string
     */
    public function getZipcode()
    {
        return $this->getData(LocationInterface::ZIPCODE);
    }

    /**
     * @param int $active
     * @return LocationInterface
     */
    public function setActive($active)
    {
        return $this->setData(LocationInterface::ACTIVE, $active);
    }

    /**
     * @return int
     */
    public function getActive()
    {
        return $this->getData(LocationInterface::ACTIVE);
    }
    /**
     * @param array $storeId
     * @return LocationInterface
     */
    public function setStoreId(array $storeId)
    {
        return $this->setData(LocationInterface::STORE_ID, $storeId);
    }

    /**
     * @return int[]
     */
    public function getStoreId()
    {
        return $this->getData(LocationInterface::STORE_ID);
    }
}
