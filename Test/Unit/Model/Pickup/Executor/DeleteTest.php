<?php
namespace Gento\Shipping\Test\Unit\Model\Pickup\Executor;

use PHPUnit\Framework\TestCase;
use Gento\Shipping\Api\PickupRepositoryInterface;
use Gento\Shipping\Api\Data\PickupInterface;
use Gento\Shipping\Model\Pickup\Executor\Delete;

class DeleteTest extends TestCase
{
    /**
     * @covers \Gento\Shipping\Model\Pickup\Executor\Delete::execute()
     */
    public function testExecute()
    {
        /** @var PickupRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject $pickupRepository */
        $pickupRepository = $this->createMock(PickupRepositoryInterface::class);
        $pickupRepository->expects($this->once())->method('deleteById');
        /** @var PickupInterface | \PHPUnit_Framework_MockObject_MockObject $pickup */
        $pickup = $this->createMock(PickupInterface::class);
        $delete = new Delete($pickupRepository);
        $delete->execute($pickup->getId());
    }
}
