<?php
namespace Gento\Shipping\Controller\Adminhtml\Pickup;

use Gento\Shipping\Api\Data\PickupInterface;
use Gento\Shipping\Api\PickupRepositoryInterface;
use Gento\Shipping\Model\ResourceModel\Pickup as PickupResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class InlineEdit
 */
class InlineEdit extends Action
{
    /**
     * Pickup repository
     * @var PickupRepositoryInterface
     */
    protected $pickupRepository;
    /**
     * Data object processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data object helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * JSON Factory
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * Pickup resource model
     * @var PickupResourceModel
     */
    protected $pickupResourceModel;

    /**
     * constructor
     * @param Context $context
     * @param PickupRepositoryInterface $pickupRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param PickupResourceModel $pickupResourceModel
     */
    public function __construct(
        Context $context,
        PickupRepositoryInterface $pickupRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        PickupResourceModel $pickupResourceModel
    ) {
        $this->pickupRepository = $pickupRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->jsonFactory = $jsonFactory;
        $this->pickupResourceModel = $pickupResourceModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $pickupId) {
            /** @var \Gento\Shipping\Model\Pickup|\Gento\Shipping\Api\Data\PickupInterface $pickup */
            try {
                $pickup = $this->pickupRepository->get((int) $pickupId);
                $pickupData = $postItems[$pickupId];
                $this->dataObjectHelper->populateWithArray($pickup, $pickupData, PickupInterface::class);
                $this->pickupResourceModel->saveAttribute($pickup, array_keys($pickupData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithPickupId($pickup, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithPickupId($pickup, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPickupId(
                    $pickup,
                    __('Something went wrong while saving the Pickup.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }

    /**
     * Add Pickup id to error message
     *
     * @param \Gento\Shipping\Api\Data\PickupInterface $pickup
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithPickupId(PickupInterface $pickup, $errorText)
    {
        return '[Pickup ID: ' . $pickup->getId() . '] ' . $errorText;
    }
}
