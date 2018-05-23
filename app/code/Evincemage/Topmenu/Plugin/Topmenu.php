<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Topmenu\Plugin;


class Topmenu
{
    protected $helper;

    public function __construct(
        \Evincemage\Topmenu\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }

    public function afterGetHtml(\Magento\Theme\Block\Html\Topmenu $topmenu, $html)
    {
        if($this->helper->showInTopmenu()) {
            $brandHtml = $topmenu->getLayout()->createBlock('Evincemage\Topmenu\Block\Downloadcategory')
                ->setTemplate('Evincemage_Topmenu::topmenu.phtml')->toHtml();

            return $html . $brandHtml;
        }
        return $html;
    }
}