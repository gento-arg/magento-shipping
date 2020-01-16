<?php
namespace Gento\Shipping\Controller\Adminhtml\Location;

use Gento\Shipping\Api\Data\LocationInterface;
use Gento\Shipping\Api\Data\LocationInterfaceFactory;
use Gento\Shipping\Api\LocationRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    /**
     * Location factory
     * @var LocationInterfaceFactory
     */
    protected $locationFactory;
    /**
     * Data Object Processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data Object Helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * Data Persistor
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * Core registry
     * @var Registry
     */
    protected $registry;
    /**
     * Location repository
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param LocationInterfaceFactory $locationFactory
     * @param LocationRepositoryInterface $locationRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        LocationInterfaceFactory $locationFactory,
        LocationRepositoryInterface $locationRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        Registry $registry
    ) {
        $this->locationFactory = $locationFactory;
        $this->locationRepository = $locationRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var LocationInterface $location */
        $location = null;
        $postData = $this->getRequest()->getPostValue();
        $data = $postData;
        $id = !empty($data['location_id']) ? $data['location_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $location = $this->locationRepository->get((int) $id);
            } else {
                unset($data['location_id']);
                $location = $this->locationFactory->create();
            }
            $this->dataObjectHelper->populateWithArray($location, $data, LocationInterface::class);
            $this->locationRepository->save($location);
            $this->messageManager->addSuccessMessage(__('You saved the Location'));
            $this->dataPersistor->clear('gento_shipping_location');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/edit', ['location_id' => $location->getId()]);
            } else {
                $resultRedirect->setPath('*/*');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('gento_shipping_location', $postData);
            $resultRedirect->setPath('*/*/edit', ['location_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Location'));
            $this->dataPersistor->set('gento\shipping_location', $postData);
            $resultRedirect->setPath('*/*/edit', ['location_id' => $id]);
        }
        return $resultRedirect;
    }
}
