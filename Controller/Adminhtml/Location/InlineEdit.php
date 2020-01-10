<?php
namespace Gento\Shipping\Controller\Adminhtml\Location;

use Gento\Shipping\Api\LocationRepositoryInterface;
use Gento\Shipping\Api\Data\LocationInterface;
use Gento\Shipping\Model\ResourceModel\Location as LocationResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;

class InlineEdit extends Action
{
    /**
     * Location repository
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;
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
     * Location resource model
     * @var LocationResourceModel
     */
    protected $locationResourceModel;

    /**
     * constructor
     * @param Context $context
     * @param LocationRepositoryInterface $locationRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param LocationResourceModel $locationResourceModel
     */
    public function __construct(
        Context $context,
        LocationRepositoryInterface $locationRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        LocationResourceModel $locationResourceModel
    ) {
        $this->locationRepository = $locationRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->jsonFactory = $jsonFactory;
        $this->locationResourceModel = $locationResourceModel;
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

        foreach (array_keys($postItems) as $locationId) {
            /** @var \Gento\Shipping\Model\Location|\Gento\Shipping\Api\Data\LocationInterface $location */
            try {
                $location = $this->locationRepository->get((int)$locationId);
                $locationData = $postItems[$locationId];
                $this->dataObjectHelper->populateWithArray($location, $locationData, LocationInterface::class);
                $this->locationResourceModel->saveAttribute($location, array_keys($locationData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithLocationId($location, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithLocationId($location, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithLocationId(
                    $location,
                    __('Something went wrong while saving the Location.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Location id to error message
     *
     * @param \Gento\Shipping\Api\Data\LocationInterface $location
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithLocationId(LocationInterface $location, $errorText)
    {
        return '[Location ID: ' . $location->getId() . '] ' . $errorText;
    }
}
