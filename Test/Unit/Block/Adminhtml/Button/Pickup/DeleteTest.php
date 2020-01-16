<?php
namespace Gento\Shipping\Test\Unit\Block\Adminhtml\Button\Pickup;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Gento\Shipping\Api\Data\PickupInterface;
use Gento\Shipping\Block\Adminhtml\Button\Pickup\Delete;

class DeleteTest extends TestCase
{
    /**
     * @var UrlInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $url;
    /**
     * @var Registry | \PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;
    /**
     * @var Delete
     */
    private $button;

    /**
     * set up tests
     */
    protected function setUp(): void
    {
        $this->url = $this->createMock(UrlInterface::class);
        $this->registry = $this->createMock(Registry::class);
        $this->button = new Delete($this->registry, $this->url);
    }

    /**
     * @covers \Gento\Shipping\Block\Adminhtml\Button\Pickup\Delete::getButtonData()
     */
    public function testButtonDataNoPickup()
    {
        $this->registry->method('registry')->willReturn(null);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Gento\Shipping\Block\Adminhtml\Button\Pickup\Delete::getButtonData()
     */
    public function testButtonDataNoPickupId()
    {
        $pickup = $this->createMock(PickupInterface::class);
        $pickup->method('getId')->willReturn(null);
        $this->registry->method('registry')->willReturn($pickup);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Gento\Shipping\Block\Adminhtml\Button\Pickup\Delete::getButtonData()
     */
    public function testButtonData()
    {
        $pickup = $this->createMock(PickupInterface::class);
        $pickup->method('getId')->willReturn(2);
        $this->registry->method('registry')->willReturn($pickup);
        $this->url->expects($this->once())->method('getUrl');
        $data = $this->button->getButtonData();
        $this->assertArrayHasKey('on_click', $data);
        $this->assertArrayHasKey('label', $data);
        $this->assertArrayHasKey('class', $data);
    }
}
