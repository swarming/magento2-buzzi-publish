<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Ui\Component\Listing\Column\Status;

use Buzzi\Publish\Api\Data\SubmissionInterface;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => SubmissionInterface::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => SubmissionInterface::STATUS_DONE, 'label' => __('Done')],
            ['value' => SubmissionInterface::STATUS_FAIL, 'label' => __('Fail')]
        ];
    }
}
