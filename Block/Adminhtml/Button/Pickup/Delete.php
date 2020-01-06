<?php
namespace Gento\Shipping\Block\Adminhtml\Button\Pickup;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete implements ButtonProviderInterface
{
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Delete constructor.
     * @param Registry $registry
     * @param UrlInterface $url
     */
    public function __construct(Registry $registry, UrlInterface $url)
    {
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getPickupId()) {
            $data = [
                'label' => __('Delete Pickup'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return \Gento\Shipping\Api\Data\PickupInterface | null
     */
    private function getPickup()
    {
        return $this->registry->registry('current_pickup');
    }

    /**
     * @return int|null
     */
    private function getPickupId()
    {
        $pickup = $this->getPickup();
        return ($pickup) ? $pickup->getId() : null;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->url->getUrl(
            '*/*/delete',
            [
                'pickup_id' => $this->getpickupId()
            ]
        );
    }
}
