<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

namespace Buzzi\Publish\Plugin\Newsletter;

use Magento\Newsletter\Model\Subscriber as NewsletterSubscriber;
use Buzzi\Publish\Helper\ExceptsMarketing;

class Subscriber
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subject
     * @param string $result
     * @return string
     */
    public function afterSubscribe(NewsletterSubscriber $subject, $result)
    {
        if ($subject->getCustomerId() && $result === NewsletterSubscriber::STATUS_SUBSCRIBED) {
            $this->updateExceptsMarketing($subject->getCustomerId(), true);
        }

        return $result;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subject
     * @param bool $result
     * @return bool
     */
    public function afterConfirm(NewsletterSubscriber $subject, $result)
    {
        if ($subject->getCustomerId()) {
            $this->updateExceptsMarketing($subject->getCustomerId(), true);
        }

        return $result;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subject
     * @param \Magento\Newsletter\Model\Subscriber $result
     * @return \Magento\Newsletter\Model\Subscriber
     */
    public function afterUnsubscribe(NewsletterSubscriber $subject, $result)
    {
        if ($subject->getCustomerId()) {
            $this->updateExceptsMarketing($subject->getCustomerId(), false);
        }

        return $result;
    }

    /**
     * @param int $customerId
     * @param bool $exceptsMarketing
     * @return void
     */
    private function updateExceptsMarketing($customerId, $exceptsMarketing)
    {
        $customer = $this->customerRepository->getById($customerId);
        $exceptsMarketingAttribute = $customer->getCustomAttribute(ExceptsMarketing::CUSTOMER_ATTR);

        if (!$exceptsMarketingAttribute || (bool)$exceptsMarketingAttribute->getValue() !== $exceptsMarketing) {
            $customer->setCustomAttribute(ExceptsMarketing::CUSTOMER_ATTR, (int)$exceptsMarketing);
            $this->customerRepository->save($customer);
        }
    }
}
