<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Topmenu\Plugin;

class Topmenu
{
    protected $helper;

    protected $nodeFactory;

    public function __construct(
        \Evincemage\Topmenu\Helper\Data $helper,
        \Magento\Framework\Data\Tree\NodeFactory $nodeFactory
    )
    {
        $this->helper = $helper;
        $this->nodeFactory = $nodeFactory;
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

    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {

        $node = $this->nodeFactory->create(
            [
                'data' => $this->getNodeAsArray(),
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        $subject->getMenu()->addChild($node);
    }

    protected function getNodeAsArray()
    {
        return [
            'name' => __('Contact Us'),
            'id' => 'contact-us',
            'url' => 'contact',
            'has_active' => false,
            'is_active' => false
        ];
    }
}