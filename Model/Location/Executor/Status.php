<?php
namespace Gento\Shipping\Model\Location\Executor;

use Gento\Shipping\Api\ExecutorInterface;
use Gento\Shipping\Api\LocationRepositoryInterface;

class Status implements ExecutorInterface
{
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * Delete constructor.
     * @param LocationRepositoryInterface $locationRepository
     */
    public function __construct(
        LocationRepositoryInterface $locationRepository
    ) {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param int $id
     * @param int $status
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id, $params = null)
    {
        if (!is_array($params) || !isset($params['active'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid param "active"'));

        }
        $status = $params['active'];
        $this->locationRepository->changeStatus($id, $status);
    }
}
