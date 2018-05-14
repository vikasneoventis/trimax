<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *
 * MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_CustomerApproval
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomerApproval\Helper;

use Magento\Framework\App\Helper\Context;
use Bss\CustomerApproval\Helper\Data;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Bss\CustomerApproval\Helper\Data
     */
    protected $helper;
    /**
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Email constructor.
     * @param Context $context
     * @param \Bss\CustomerApproval\Helper\Data $helper
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Context $context,
        Data $helper,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param string $customerEmail
     * @param string $emailTemplate
     * @param string $customerName
     * @return mixed
     */
    public function sendEmail($customerEmail, $emailTemplate, $customerName)
    {
        try {
            $email = $this->helper->getCustomerEmailSender();
            $emailValue = 'trans_email/ident_'.$email.'/email';
            $emailNameValue = 'trans_email/ident_'.$email.'/name';
            $emailSender = $this->scopeConfig->getValue($emailValue, ScopeInterface::SCOPE_STORE);
            $emailNameSender = $this->scopeConfig->getValue($emailNameValue, ScopeInterface::SCOPE_STORE);
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml($emailNameSender),
                'email' => $this->escaper->escapeHtml($emailSender),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'varEmail'  => $customerEmail,
                    'varName' => $customerName,
                ])
                ->setFrom($sender)
                ->addTo($customerEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            //do nothing
        }
    }
}
