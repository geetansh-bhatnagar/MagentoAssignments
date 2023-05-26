<?php

namespace Sigma\ShippingAddress\Plugin\Checkout\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteRepository;

class ShippingInformationManagement
{
    /**
     * @param QuoteRepository $quoteRepository
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        private QuoteRepository $quoteRepository,
        private CartRepositoryInterface $cartRepository
    )
    {
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @throws NoSuchEntityException
     * @throws \Zend_Log_Exception
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation,
    ) {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/testlog.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('I am here!!!');

        if(!$addressInformation->getExtensionAttributes())
        {
            return;
        }
//
//        $quote = $this->quoteRepository->getActive($cartId);
        $quote = $this->cartRepository->getActive($cartId);
//        $middleName = $addressInformation->getShippingAddress()->getExtensionAttributes()->getMiddleName();
//        $quote->setMiddleName($middleName);
//        $this->cartRepository->save($quote);
//        return [$cartId, $addressInformation];
        $custom_middle_name_value = $addressInformation->getExtensionAttributes()->getMiddleName();
        $quote->setMiddleName($custom_middle_name_value);
        $this->cartRepository->save($quote);
        return [$cartId, $addressInformation];
//        $quote->setMiddleName($extAttributes->getMiddleName());
    }
}
