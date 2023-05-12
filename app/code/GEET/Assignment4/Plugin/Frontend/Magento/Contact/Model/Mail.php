<?php

namespace GEET\Assignment4\Plugin\Frontend\Magento\Contact\Model;

use Magento\Contact\Model\ConfigInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class Mail
{
    /**
     * @param ConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $state
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private ConfigInterface $scopeConfig,
        private TransportBuilder $transportBuilder,
        private StateInterface $state,
        private StoreManagerInterface $storeManager
    ) {
    }

    // After plugin added for sending response to customer
    /**
     * @param \Magento\Contact\Model\Mail $subject
     * @param $result
     * @param $variables
     * @param $replyTo
     * @return void
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function afterSend(
        \Magento\Contact\Model\Mail $subject,
                                    $result,
                                    $variables,
                                    $replyTo
    ) : void {

//        getting Name and Email of customer
        $replyToName = !empty($replyTo['data']['name']) ? $replyTo['data']['name'] : null;
        $replyToEmail = !empty($replyTo['data']['email']) ? $replyTo['data']['email'] : null;

        $this->state->suspend();

        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('contact_email_contact_confirmation_template')
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId()
                    ]
                )
                ->setTemplateVars($replyTo)
                ->setFromByScope($this->scopeConfig->emailSender())
                ->addTo($replyToEmail, $replyToName)
                ->setReplyTo(
                    $this->scopeConfig->emailRecipient(),
                    $this->scopeConfig->emailSender()
                )
                ->getTransport();

            $transport->sendMessage();
        } finally {
            $this->state->resume();
        }
    }
}
