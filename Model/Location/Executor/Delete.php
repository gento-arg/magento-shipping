<?php
namespace Gento\Shipping\Model\Location\Executor;

use Gento\Shipping\Api\LocationRepositoryInterface;
use Gento\Shipping\Api\ExecutorInterface;

class Delete implements ExecutorInterface
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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id)
    {
        $this->locationRepository->deleteById($id);
    }
}
