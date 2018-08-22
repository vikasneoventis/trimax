<?php
namespace Aitoc\AbandonedCartAlertsPro\Controller\Adminhtml\Alert;

class Pending extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    public $resultFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * Class constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultFactory = $context->getResultFactory();
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Add block
     *
     * @return mixed
     */
    public function execute()
    {
        $resultLayout = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_LAYOUT);

        $output = $resultLayout->getLayout()
            ->getBlock(
                'aitocabandonedcart.extension.index.tab.alert.pending'
            )
            ->toHtml();
        $resultRaw = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_RAW);
        return $resultRaw->setContents($output);
    }
}
