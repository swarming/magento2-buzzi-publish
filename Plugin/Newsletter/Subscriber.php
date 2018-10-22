<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

namespace Buzzi\Publish\Plugin\Newsletter;

use Magento\Newsletter\Model\Subscriber as NewsletterSubscriber;
use Buzzi\Publish\Helper\AcceptsMarketing;

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
            $this->updateAcceptsMarketing($subject->getCustomerId(), true);
        }

        return $result;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subject
     * @param string $result
     * @return string
     */
    public function afterSubscribeCustomerById(NewsletterSubscriber $subject, $result)
    {
        if ($subject->getCustomerId()
            && $subject->isStatusChanged()
            && $subject->getStatus() === NewsletterSubscriber::STATUS_SUBSCRIBED
        ) {
            $this->updateAcceptsMarketing($subject->getCustomerId(), true, true);
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
            $this->updateAcceptsMarketing($subject->getCustomerId(), true);
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
            $this->updateAcceptsMarketing($subject->getCustomerId(), false);
        }

        return $result;
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subject
     * @param string $result
     * @return string
     */
    public function afterUnsubscribeCustomerById(NewsletterSubscriber $subject, $result)
    {
        if ($subject->getCustomerId() && $subject->isStatusChanged()) {
            $this->updateAcceptsMarketing($subject->getCustomerId(), false, true);
        }

        return $result;
    }

    /**
     * @param int $customerId
     * @param bool $acceptsMarketing
     * @param bool $updateSubscribeFlag
     * @return void
     */
    private function updateAcceptsMarketing($customerId, $acceptsMarketing, $updateSubscribeFlag = false)
    {
        $customer = $this->customerRepository->getById($customerId);
        $acceptsMarketingAttribute = $customer->getCustomAttribute(AcceptsMarketing::CUSTOMER_ATTR);

        if ($updateSubscribeFlag) {
            $customer->getExtensionAttributes()->setIsSubscribed($acceptsMarketing);
        }

        if (!$acceptsMarketingAttribute || (bool)$acceptsMarketingAttribute->getValue() !== $acceptsMarketing) {
            $customer->setCustomAttribute(AcceptsMarketing::CUSTOMER_ATTR, (int)$acceptsMarketing);
            $this->customerRepository->save($customer);
        }
    }
}
