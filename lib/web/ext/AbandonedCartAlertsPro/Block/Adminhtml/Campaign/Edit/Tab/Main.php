<?php
namespace Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Campaign\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Email\Identity
     */
    private $identity;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $yesno;

    /**
     * @var \Magento\Email\Model\ResourceModel\Template\Collection
     */
    private $emalTemplateCollection;

    /**
     * Class constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Config\Model\Config\Source\Email\Identity $identity
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Email\Identity $identity,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Magento\Email\Model\ResourceModel\Template\Collection $emalTemplateCollection,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->identity = $identity;
        $this->yesno = $yesno;
        $this->emalTemplateCollection = $emalTemplateCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('aitocabandonedcart_campaign_page');

        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Campaign Information')]);

        $availableContacts = $this->identity->toOptionArray();

        $availableTemplates = $this->emalTemplateCollection->toOptionArray();

        if ($model->getId()) {
            $fieldset->addField('campaign_id', 'hidden', ['name' => 'campaign_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'template_id',
            'select',
            [
                'name' => 'template_id',
                'label' => __('Email Template'),
                'required' => true,
                'values' => $availableTemplates
            ]
        );

        $fieldset->addField(
            'send_interval',
            'text',
            [
                'name' => 'send_interval',
                'label' => __('Email Delay Period (hours)'),
                'required' => true,
                'value' => 2,
                'class' => 'validate-number validate-greater-than-zero input-text',
                'note' => 'The system will send the \'Abandoned Cart\' email after the above '
                    . 'mentioned amount of hours upon the cart was abandoned'
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height:24em;'
            ]
        );

        $fieldset->addField(
            'sender',
            'select',
            [
                'name' => 'sender',
                'label' => __('Sender'),
                'required' => true,
                'values' => $availableContacts
            ]
        );

        $fieldset->addField(
            'exclude_from_alert',
            'select',
            [
                'label' => __('Exclude from Alert'),
                'required' => true,
                'name' => 'exclude_from_alert',
                'values' => $this->yesno->toOptionArray(),
                'note' => __('Exclude Out-of-Stock and Disabled products from alert.')
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Settings');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
