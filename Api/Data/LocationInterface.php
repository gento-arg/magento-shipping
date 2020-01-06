<?php
namespace Gento\Shipping\Api\Data;

/**
 * @api
 */
interface LocationInterface
{
    const LOCATION_ID = 'location_id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const PRICE = 'price';
    const ZIPCODE = 'zipcode';
    const ACTIVE = 'active';
    /**
     * @var string
     */
    const STORE_ID = 'store_id';
    /**
     * @param int $id
     * @return LocationInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return LocationInterface
     */
    public function setLocationId($id);

    /**
     * @return int
     */
    public function getLocationId();

    /**
     * @param string $title
     * @return LocationInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();
    /**
     * @param string $description
     * @return LocationInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();
    /**
     * @param float $price
     * @return LocationInterface
     */
    public function setPrice($price);

    /**
     * @return float
     */
    public function getPrice();
    /**
     * @param string $zipcode
     * @return LocationInterface
     */
    public function setZipcode($zipcode);

    /**
     * @return string
     */
    public function getZipcode();
    /**
     * @param int $active
     * @return LocationInterface
     */
    public function setActive($active);

    /**
     * @return int
     */
    public function getActive();

    /**
     * @param int[] $store
     * @return LocationInterface
     */
    public function setStoreId(array $store);

    /**
     * @return int[]
     */
    public function getStoreId();
}
