<?php
namespace Gento\Shipping\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;

abstract class AbstractNewAction extends Action
{
    /**
     * @return Forward
     */
    public function execute()
    {
        /** @var Forward $forward */
        $forward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $forward->forward('edit');
        return $forward;
    }
}
