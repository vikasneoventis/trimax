<?php
namespace Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Extension\Index\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Statistic extends Generic implements TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Email\Identity
     */
    private $identity;

    /**
     * @var string
     */
    public $_template = 'extension/index/tab/statistic.phtml';

    /**
     * Class constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Config\Model\Config\Source\Email\Identity $identity
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Email\Identity $identity,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->identity = $identity;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return void
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->addChild(
            'aitocabandonedcart.statistic.index',
            'Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Statistic\Index'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Statistics');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Statistics');
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
