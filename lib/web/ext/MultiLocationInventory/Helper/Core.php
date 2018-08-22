<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Helper;

/**
 * Core helper.
 */
class Core extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $productMetadata;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
        parent::__construct($context);
    }

    public function isEnterpriseEdition()
    {
        if ($this->productMetadata->getEdition() == 'Enterprise') {
            return true;
        }
        return false;
    }
}
