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

        $cart = $observer->getCart();
        $quote = $cart->getQuote();
        $cartItems = $quote->getItemsCount();
        $this->logger->debug($cartItems);

        if ($cartItems > 5) {
            // Just to Check if the function is called or not
            $this->logger->debug('Function called');

            /* Receiver Detail */
            $receiverInfo = [
                'name' => 'Geetansh',
                'email' => 'geetansh.bhatnagar@sigmainfo.net'
            ];
            
//            This line gets the current store information.

            $store = $this->storeManager->getStore();

//            This array defines the template variables for the email.

            $templateParams = ['administrator_name' => $receiverInfo['name'],
                'cartItems' => $cartItems ];

            $transport = $this->transportBuilder->setTemplateIdentifier(
                'GEET_EventsObservers_email_cart_template'
            )->setTemplateOptions(
                ['area' => 'frontend', 'store' => $store->getId()]
            )->addTo(
                $receiverInfo['email'], $receiverInfo['name']
            )->setTemplateVars(
                $templateParams
            )->setFrom(
                'general'
            )->getTransport();

            try {
                // Send an email
                $transport->sendMessage();
            } catch (\Exception $e) {
                // Write a log message whenever get errors
                $this->logger->critical($e->getMessage());
            }
    }
}}
