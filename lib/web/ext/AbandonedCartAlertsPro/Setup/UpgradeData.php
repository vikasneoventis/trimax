<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AbandonedCartAlertsPro\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Email\Model\Template;
use Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Campaign\Collection as CampaignCollection;

/**
 * Upgrade Data script
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * Email template resource model
     *
     * @var emailTemplateModel
     */
    private $emailTemplateModel;

    /**
     * Campaign collection
     *
     * @var campaignCollection
     */
    private $campaignCollection;

    /**
     * Init
     *
     * @param Template           $emailTemplateModel
     * @param CampaignCollection $campaignCollection
     */
    public function __construct(Template $emailTemplateModel, CampaignCollection $campaignCollection)
    {
        $this->emailTemplateModel = $emailTemplateModel;
        $this->campaignCollection = $campaignCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addEmailTemplate();
            if ($templateId = $this->emailTemplateModel->getId()) {
                $this->addTemplateIdToCampaigns($templateId);
                $this->setDefaultSendIntervalToCampaigns();
            }
        }

        $setup->endSetup();
    }

    private function addTemplateIdToCampaigns($templateId)
    {
        foreach ($this->campaignCollection as $campaignItem) {
            $campaignItem->setTemplateId($templateId);
        }
        $this->campaignCollection->save();
    }

    private function setDefaultSendIntervalToCampaigns()
    {
        foreach ($this->campaignCollection as $campaignItem) {
            $campaignItem->setSendInterval(2);
        }
        $this->campaignCollection->save();
    }

    private function addEmailTemplate()
    {
        $emailTemplateData = [
            'template_code' => 'Abandoned Cart Alerts Pro - Quote Alert',
            'template_subject' => 'Did you forget to check out at {{var store.getFrontendName()}}?',
            'template_text' => '{{template config_path="design/email/header_template"}}
                    <p class="greeting">{{trans "%customer_name," customer_name=$customer_name}}</p>
                    {{trans "Thank you for shopping at %store_name and considering us for your purchase. '
                . 'We noticed that during your visit to our store you added the following item(s) to the cart,'
                . ' but did not complete the checkout." store_name=$store.getFrontendName()}}</p>
                    <p>{{trans "Shopping Cart Contents:"}}</p>
                    <p>{{var quote_products|nl2rb}}</p>
                    <p>{{trans "If you simply forgot to check out, you can still complete the transaction(s) '
                . 'at this time."}}</p>
                    <p>{{trans \'<a href="%recovery_url" >Click to complete your purchase</a>\' '
                . 'recovery_url=$this.getUrl($store,\'aitocabandonedcart/recover/cart/\','
                . '[id:$alert_id,code:$alert_code]) |raw}}</p><br>
                    {{depend coupon.coupon_code}}
                        <p>{{trans "Also, feel free to use this coupon code to get a %discount_amount discount:" '
                . 'discount_amount=$coupon.discount_amount}}</p></br>
                        <p><strong>{{var coupon.coupon_code}}</strong></p></br>
                        <p>{{trans "*The coupon expires in %expiry_days day(s)." expiry_days=$coupon.expiry_days}}</p>
                    {{/depend}}
                    <h5>{{trans "Was this a technical glitch?"}}</h5>
                    <p>{{trans "If you experienced a technical difficulty while shopping at our site, we\'d like to '
                . 'know about it. Thank you in advance for helping us improve our store!"}}</p>
                    <p>{{trans "Important: this email means the orders listed above have not been completed, and '
                . 'that you have not been charged. If you believe this is a mistake, please let us know."}}</p>
                    {{trans \'<a href="%unsubscribe_url" >Unsubscribe</a>\' unsubscribe_url=$this.getUrl'
                . '($store,\'aitocabandonedcart/alert/unsubscribe/\',[id:$alert_id,code:$alert_code]) |raw}}
                    {{template config_path="design/email/footer_template"}}',
            'template_type' => 2,   //1 - text; 2 - HTML;
        ];
        $this->emailTemplateModel->setData($emailTemplateData);
        $this->emailTemplateModel->save();
    }
}
