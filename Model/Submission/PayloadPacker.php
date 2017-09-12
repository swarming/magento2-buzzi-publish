<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\Submission;

use Buzzi\Publish\Api\Data\SubmissionInterface;

class PayloadPacker
{
    /**
     * @var \Buzzi\Base\Model\PayloadFile
     */
    protected $payloadFile;

    /**
     * @param \Buzzi\Base\Model\PayloadFile $payloadFile
     */
    public function __construct(
        \Buzzi\Base\Model\PayloadFile $payloadFile
    ) {
        $this->payloadFile = $payloadFile;
    }

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @param array $payload
     * @return void
     */
    public function packPayload($submission, array $payload)
    {
        $jsonPayload = json_encode($payload);
        $useFile = $submission->getUseFile() || mb_strlen($jsonPayload) >= SubmissionInterface::MAX_PAYLOAD_LENGTH;
        $jsonPayload = $useFile ? $this->payloadFile->save($jsonPayload) : $jsonPayload;

        $submission->setUseFile($useFile);
        $submission->setPayload($jsonPayload);
    }

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @return array
     */
    public function unpackPayload($submission)
    {
        $jsonPayload = $submission->getUseFile() ? $this->payloadFile->load($submission->getPayload()) : $submission->getPayload();
        return json_decode($jsonPayload, true);
    }

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @return void
     */
    public function cleanPayload($submission)
    {
        if ($submission->getUseFile()) {
            $this->payloadFile->delete($submission->getPayload());
            $submission->setPayload('');
        }
    }
}
