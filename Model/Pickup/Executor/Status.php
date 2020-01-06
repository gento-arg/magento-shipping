<?php
namespace Gento\Shipping\Model\Pickup\Executor;

use Gento\Shipping\Api\ExecutorInterface;
use Gento\Shipping\Api\PickupRepositoryInterface;

class Status implements ExecutorInterface
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
        if (!is_array($params) || !isset($params['active'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid param "active"'));

        }
        $status = $params['active'];
        $this->pickupRepository->changeStatus($id, $status);
    }
}
