<?php
namespace GEET\FreeShippingBar\Block;

class Freeshipping extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfig;

    protected $_cart;

    protected $_checkoutSession;

    protected $_priceHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
    ){
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        $this->_priceHelper = $priceHelper;
        parent::__construct($context,$data);
    }


    public function getMessage(){
        if($this->isEnable()){
            $subTotal = $this->getSubtotal();
            $freeshippingAmount = $this->getFreeshippingAmount();
            if ($subTotal >= $freeshippingAmount) {
                $message = __('Congratulations!! You are eligible for free shipping 🎉');
            }

            else{
                $amount = $freeshippingAmount - $subTotal;
                $message = __('You are %1 away from free shipping 😔.', $this->_priceHelper->currency($amount, true, false));

            }
            return $message;
        }
        return false;
    }

    public function getSubtotal(){
        return $this->_checkoutSession->getQuote()->getSubtotal();
    }

    public function getGrandtotal(){
        return $this->_checkoutSession->getQuote()->getGrandTotal();
    }

    public function isEnable(){
        return $this->scopeConfig->getValue('free_shipping/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getFreeshippingAmount(){
        return $this->scopeConfig->getValue('free_shipping/general/display_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
