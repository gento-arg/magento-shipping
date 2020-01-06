<?php
namespace Gento\Shipping\Controller\Adminhtml\Location;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Gento\Shipping\Api\LocationRepositoryInterface;

class Edit extends Action
{
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Edit constructor.
     * @param Context $context
     * @param LocationRepositoryInterface $locationRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        LocationRepositoryInterface $locationRepository,
        Registry $registry
    ) {
        $this->locationRepository = $locationRepository;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * get current Location
     *
     * @return null|\Gento\Shipping\Api\Data\LocationInterface
     */
    private function initLocation()
    {
        $locationId = $this->getRequest()->getParam('location_id');
        try {
            $location = $this->locationRepository->get($locationId);
        } catch (NoSuchEntityException $e) {
            $location = null;
        }
        $this->registry->register('current_location', $location);
        return $location;
    }

    /**
     * Edit or create Location
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $location = $this->initLocation();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Gento_Shipping::shipping_location');
        $resultPage->getConfig()->getTitle()->prepend(__('Locations'));

        if ($location === null) {
            $resultPage->getConfig()->getTitle()->prepend(__('New Location'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend($location->getTitle());
        }
        return $resultPage;
    }
}
