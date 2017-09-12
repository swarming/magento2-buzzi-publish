<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Controller\Adminhtml\Queue;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var \Buzzi\Publish\Api\QueueInterface
     */
    protected $queue;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Buzzi\Publish\Api\QueueInterface $queue
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Buzzi\Publish\Api\QueueInterface $queue
    ) {
        $this->queue = $queue;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('buzzi_publish/queue/index');

        $excluded = $this->getRequest()->getParam('excluded') === 'false' ? false : true;
        $submissionIds = (array)$this->getRequest()->getParam('selected');
        if (empty($submissionIds) && $excluded) {
            $this->messageManager->addWarningMessage(__("You haven't selected any item!"));
            return $resultRedirect;
        }

        try {
            $this->queue->deleteByIds($submissionIds);
            $this->messageManager->addSuccessMessage(__('Submission(s) were deleted successfully.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while deleting submissions.'));
        }

        return $resultRedirect;
    }
}
