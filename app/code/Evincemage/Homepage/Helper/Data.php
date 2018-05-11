<?php
/**
 * @copyright Copyright (c) 2018 www.evincemage.com
 */
namespace Evincemage\Homepage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper {

    /**
     * @var StoreManager
     */
    protected $_storeManager;

    const XML_PATH_BANNER_CAPTION = 'homepage/general/banner_caption';
    
    

    /**
     * Data constructor.
     */
    public function __construct(
    Context $context, StoreManager $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return module status
     */

    public function getBannerCaption() {
        return $this->scopeConfig->getValue(
                        self::XML_PATH_BANNER_CAPTION, ScopeInterface::SCOPE_STORE
        );
    }
}
