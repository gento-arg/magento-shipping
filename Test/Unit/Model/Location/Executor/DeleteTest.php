<?php
namespace Gento\Shipping\Test\Unit\Model\Location\Executor;

use PHPUnit\Framework\TestCase;
use Gento\Shipping\Api\LocationRepositoryInterface;
use Gento\Shipping\Api\Data\LocationInterface;
use Gento\Shipping\Model\Location\Executor\Delete;

class DeleteTest extends TestCase
{
    /**
     * @covers \Gento\Shipping\Model\Location\Executor\Delete::execute()
     */
    public function testExecute()
    {
        /** @var LocationRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject $locationRepository */
        $locationRepository = $this->createMock(LocationRepositoryInterface::class);
        $locationRepository->expects($this->once())->method('deleteById');
        /** @var LocationInterface | \PHPUnit_Framework_MockObject_MockObject $location */
        $location = $this->createMock(LocationInterface::class);
        $delete = new Delete($locationRepository);
        $delete->execute($location->getId());
    }
}
