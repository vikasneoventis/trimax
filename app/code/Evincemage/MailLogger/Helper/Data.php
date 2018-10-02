<?php
/**
 * @author Evince Team
 * @copyright Copyright © 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\MailLogger\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_LOGGING = 'evincemage_maillogger/maillogger/logging';

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LOGGING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}