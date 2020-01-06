<?php
namespace Gento\Shipping\Model\Pickup\Executor;

use Gento\Shipping\Api\ExecutorInterface;
use Gento\Shipping\Api\PickupRepositoryInterface;

class Delete implements ExecutorInterface
{
    /**
     * @var PickupRepositoryInterface
     */
    private $pickupRepository;

    /**
     * Delete constructor.
     * @param PickupRepositoryInterface $pickupRepository
     */
    public function __construct(
        PickupRepositoryInterface $pickupRepository
    ) {
        $this->pickupRepository = $pickupRepository;
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id, $params = null)
    {
        $this->pickupRepository->deleteById($id);
    }
}
