<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Topmenu\Plugin;

class Topmenu
{
    /**
     * @var \Evincemage\Topmenu\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Data\Tree\NodeFactory
     */
    protected $nodeFactory;

    /**
     * Topmenu constructor.
     * @param \Evincemage\Topmenu\Helper\Data $helper
     * @param \Magento\Framework\Data\Tree\NodeFactory $nodeFactory
     */
    public function __construct(
        \Evincemage\Topmenu\Helper\Data $helper,
        \Magento\Framework\Data\Tree\NodeFactory $nodeFactory
    )
    {
        $this->helper = $helper;
        $this->nodeFactory = $nodeFactory;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $topmenu
     * @param $html
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetHtml(\Magento\Theme\Block\Html\Topmenu $topmenu, $html)
    {
        if($this->helper->showInTopmenu()) {
            $brandHtml = $topmenu->getLayout()->createBlock('Evincemage\Topmenu\Block\Downloadcategory')
                ->setTemplate('Evincemage_Topmenu::topmenu.phtml')->toHtml();

            return $html . $brandHtml;
        }
        return $html;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     */
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

    /**
     * @return array
     */
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