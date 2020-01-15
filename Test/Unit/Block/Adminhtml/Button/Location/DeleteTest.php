<?php
namespace Gento\Shipping\Test\Unit\Block\Adminhtml\Button\Location;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Gento\Shipping\Api\Data\LocationInterface;
use Gento\Shipping\Block\Adminhtml\Button\Location\Delete;

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
    protected function setUp()
    {
        $this->url = $this->createMock(UrlInterface::class);
        $this->registry = $this->createMock(Registry::class);
        $this->button = new Delete($this->registry, $this->url);
    }

    /**
     * @covers \Gento\Shipping\Block\Adminhtml\Button\Location\Delete::getButtonData()
     */
    public function testButtonDataNoLocation()
    {
        $this->registry->method('registry')->willReturn(null);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Gento\Shipping\Block\Adminhtml\Button\Location\Delete::getButtonData()
     */
    public function testButtonDataNoLocationId()
    {
        $location = $this->createMock(LocationInterface::class);
        $location->method('getId')->willReturn(null);
        $this->registry->method('registry')->willReturn($location);
        $this->url->expects($this->exactly(0))->method('getUrl');
        $this->assertEquals([], $this->button->getButtonData());
    }

    /**
     * @covers \Gento\Shipping\Block\Adminhtml\Button\Location\Delete::getButtonData()
     */
    public function testButtonData()
    {
        $location = $this->createMock(LocationInterface::class);
        $location->method('getId')->willReturn(2);
        $this->registry->method('registry')->willReturn($location);
        $this->url->expects($this->once())->method('getUrl');
        $data = $this->button->getButtonData();
        $this->assertArrayHasKey('on_click', $data);
        $this->assertArrayHasKey('label', $data);
        $this->assertArrayHasKey('class', $data);
    }
}
