<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Api;

use Buzzi\Publish\Api\Data\SubmissionInterface;

interface SubmissionRepositoryInterface
{
    /**
     * @param mixed[] $data
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function getNew(array $data = []);

    /**
     * @param int $submissionId
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($submissionId);

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(SubmissionInterface $submission);

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(SubmissionInterface $submission);

    /**
     * @param int $submissionId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($submissionId);
}
