<?php

/**
 * @copyright Copyright (c) 2018 www.evincemage.com
 */

namespace Evincemage\Homepage\Block\Home;

use \Magento\Framework\View\Element\Template;

class Banner extends Template
{
    /**
     * @var \Evincemage\Homepage\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Banner constructor.
     * @param Template\Context $context
     * @param \Evincemage\Homepage\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Evincemage\Homepage\Helper\Data $dataHelper,
        array $data = []
    )
    {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return \Evincemage\Homepage\Helper\module
     */
    public function getBannerCaption()
    {
        return $this->_dataHelper->getBannerCaption();
    }

}
