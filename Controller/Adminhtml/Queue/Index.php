<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Controller\Adminhtml\Queue;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Buzzi_Publish::publish_queue');

        $resultPage->addBreadcrumb(__('Buzzi.io'), __('Buzzi.io'));
        $resultPage->addBreadcrumb(__('Publish Queue'), __('Publish Queue'));
        $resultPage->getConfig()->getTitle()->prepend(__('Buzzi Publish Queue'));

        return $resultPage;
    }
}
