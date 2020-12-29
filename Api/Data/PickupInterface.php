<?php
namespace Gento\Shipping\Api\Data;

/**
 * @api
 */
interface PickupInterface
{
    const PICKUP_ID = 'pickup_id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const DATES = 'dates';
    const PRICE = 'price';
    const ACTIVE = 'active';
    /**
     * @var string
     */
    const STORE_ID = 'store_id';

    /**
     * @param int $id
     * @return PickupInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return PickupInterface
     */
    public function setPickupId($id);

    /**
     * @return int
     */
    public function getPickupId();

    /**
     * @param string $title
     * @return PickupInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $dates
     * @return PickupInterface
     */
    public function setDates($dates);

    /**
     * @return string
     */
    public function getDates();

    /**
     * @param string $description
     * @return PickupInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();
    /**
     * @param float $price
     * @return PickupInterface
     */
    public function setPrice($price);

    /**
     * @return float
     */
    public function getPrice();
    /**
     * @param int $active
     * @return PickupInterface
     */
    public function setActive($active);

    /**
     * @return int
     */
    public function getActive();

    /**
     * @param int[] $store
     * @return PickupInterface
     */
    public function setStoreId(array $store);

    /**
     * @return int[]
     */
    public function getStoreId();

    /**
     * @return string
     */
    public function getFormattedDates();
}
