<?php

namespace GEET\EventsObservers\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface as ObserverInterfaceAlias;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder as TransportBuilderAlias;
use Magento\Quote\Model\QuoteFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
class EmailTrigger implements ObserverInterfaceAlias
{
    protected $storeManager;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $quoteFactory;
    protected $customerSession;
    protected $logger;

    public function __construct(
        StoreManagerInterface $storeManager,
        TransportBuilderAlias $transportBuilder,
        ScopeConfigInterface  $scopeConfig,
        QuoteFactory          $quoteFactory,
        CustomerSession       $customerSession,
        LoggerInterface       $logger
    )
    {
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->quoteFactory = $quoteFactory;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {

        global $objectManager;
        $cart = $observer->getCart();
        $quote = $cart->getQuote();
        $cartItems = $quote->getItemsCount();

        if ($cartItems > 5) {
            $email = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('trans_email/ident_general/email');
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $transportBuilder = $objectManager->get('\Magento\Framework\Mail\Template\TransportBuilder');
            $storeId = $storeManager->getStore()->getId();


            $transport = $transportBuilder->setTemplateIdentifier('GEET_EventsObservers_email_cart_template')
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars(
                    [
                        'store' => $storeManager->getStore(),
                    ]
                )
                ->setFrom($email, 'Service')
                // you can config general email address in Store -> Configuration -> General -> Store Email Addresses
                ->addTo('geetansh.bhatnagar@sigmainfo.net', 'Customer Name')
                ->getTransport();
            $transport->sendMessage();
        }}
}
