<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Topmenu\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_DOWNLOADS_DISPLAY_SIZE  = 'mageworx_downloads/main/section_topmenu';

    /**
     * @param null $storeId
     * @return bool
     */
    public function showInTopmenu($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_DOWNLOADS_DISPLAY_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}