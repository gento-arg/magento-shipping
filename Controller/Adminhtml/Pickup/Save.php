<?php
namespace Gento\Shipping\Controller\Adminhtml\Pickup;

use Gento\Shipping\Api\Data\PickupInterface;
use Gento\Shipping\Api\Data\PickupInterfaceFactory;
use Gento\Shipping\Api\PickupRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json as JsonHelper;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    /**
     * Pickup factory
     * @var PickupInterfaceFactory
     */
    protected $pickupFactory;

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
     * Pickup repository
     * @var PickupRepositoryInterface
     */
    protected $pickupRepository;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * Save constructor.
     * @param Context $context
     * @param PickupInterfaceFactory $pickupFactory
     * @param PickupRepositoryInterface $pickupRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $registry
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        PickupInterfaceFactory $pickupFactory,
        PickupRepositoryInterface $pickupRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        Registry $registry,
        JsonHelper $jsonHelper
    ) {
        $this->pickupFactory = $pickupFactory;
        $this->pickupRepository = $pickupRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->registry = $registry;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var PickupInterface $pickup */
        $pickup = null;
        $postData = $this->getRequest()->getPostValue();
        $data = $postData;
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $dates = [];
        foreach ($days as $day) {
            $dates[$day] = $data[$day];
            unset($data[$day]);
        }
        $data['dates'] = $this->jsonHelper->serialize($dates);
        $id = !empty($data['pickup_id']) ? $data['pickup_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $pickup = $this->pickupRepository->get((int) $id);
            } else {
                unset($data['pickup_id']);
                $pickup = $this->pickupFactory->create();
            }
            $this->dataObjectHelper->populateWithArray($pickup, $data, PickupInterface::class);
            $this->pickupRepository->save($pickup);
            $this->messageManager->addSuccessMessage(__('You saved the Pickup'));
            $this->dataPersistor->clear('gento_shipping_pickup');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/edit', ['pickup_id' => $pickup->getId()]);
            } else {
                $resultRedirect->setPath('*/*');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('gento_shipping_pickup', $postData);
            $resultRedirect->setPath('*/*/edit', ['pickup_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Pickup'));
            $this->dataPersistor->set('gento\shipping_pickup', $postData);
            $resultRedirect->setPath('*/*/edit', ['pickup_id' => $id]);
        }
        return $resultRedirect;
    }
}
