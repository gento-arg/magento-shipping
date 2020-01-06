<?php
namespace Gento\Shipping\Controller\Adminhtml\Pickup;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Gento\Shipping\Api\PickupRepositoryInterface;

class Edit extends Action
{
    /**
     * @var PickupRepositoryInterface
     */
    private $pickupRepository;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Edit constructor.
     * @param Context $context
     * @param PickupRepositoryInterface $pickupRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PickupRepositoryInterface $pickupRepository,
        Registry $registry
    ) {
        $this->pickupRepository = $pickupRepository;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * get current Pickup
     *
     * @return null|\Gento\Shipping\Api\Data\PickupInterface
     */
    private function initPickup()
    {
        $pickupId = $this->getRequest()->getParam('pickup_id');
        try {
            $pickup = $this->pickupRepository->get($pickupId);
        } catch (NoSuchEntityException $e) {
            $pickup = null;
        }
        $this->registry->register('current_pickup', $pickup);
        return $pickup;
    }

    /**
     * Edit or create Pickup
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $pickup = $this->initPickup();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Gento_Shipping::shipping_pickup');
        $resultPage->getConfig()->getTitle()->prepend(__('Pickups'));

        if ($pickup === null) {
            $resultPage->getConfig()->getTitle()->prepend(__('New Pickup'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend($pickup->getTitle());
        }
        return $resultPage;
    }
}
