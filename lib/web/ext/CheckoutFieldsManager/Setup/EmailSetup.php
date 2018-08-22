<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Setup;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;

class EmailSetup
{
    /**
     * @var \Magento\Email\Model\Template\Config
     */
    protected $emailConfig;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var array
     */
    protected $data;

    /**
     * EmailSetup constructor.
     *
     * @param \Magento\Email\Model\Template\Config $emailConfig
     * @param \Magento\Framework\Filesystem        $filesystem
     * @param \Magento\Framework\App\State         $appState
     */
    public function __construct(
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\State $appState
    ) {
        $this->emailConfig = $emailConfig;
        $this->filesystem  = $filesystem;
        $this->appState    = $appState;
    }

    /**
     * Get Email params from template id
     *
     * @param $id
     *
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDataEmail($id)
    {
        $templateId = $id;
        $this->appState->setAreaCode('frontend');
        $parts      = $this->emailConfig->parseTemplateIdParts($templateId);
        $templateId = $parts['templateId'];
        $template   = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Email\Model\BackendTemplate');
        $template->setForcedArea($templateId);
        $template->loadDefault($templateId);
        $this->setData('orig_template_code', $templateId);
        $this->setData('template_text', $template->getTemplateText());
        $this->setData('template_type', $template->getTemplateType());
        $this->setData('template_subject', $template->getTemplateSubject());
        $this->setData('orig_template_variables', $template->getOrigTemplateVariables());
        $this->setData('template_code', __('New Order with CFM'));

        return $this->getData();
    }

    /**
     * @param $key
     * @param $value
     */
    protected function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param null $key
     *
     * @return array|null
     */
    protected function getData($key = null)
    {
        if (!$key) {
            return $this->data;
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }
}
