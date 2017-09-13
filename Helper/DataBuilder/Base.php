<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper\DataBuilder;

class Base
{
    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $session;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        $this->session = $session;
        $this->dateTime = $dateTime;
    }

    /**
     * @param string $eventType
     * @return array
     */
    public function initBaseData($eventType)
    {
        $data = [
            'event_type' => $eventType,
            'session_id' => md5($this->session->getSessionId()),
            'timestamp' => $this->dateTime->gmDate(\DateTime::ATOM, (new \DateTime())->getTimestamp())
        ];

        return $data;
    }
}
