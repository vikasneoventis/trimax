<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;

class Stores extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'store_ids';

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     * @deprecated
     */
    public function prepareDataSource(array $dataSource)
    {
        $websitesData = $this->getData('options')->toOptionArray();
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $result = '';
                if (array_key_exists($fieldName, $item)) {
                    foreach ($websitesData as $website) {
                        $websiteDisplayed = false;
                        foreach ($website['value'] as $store) {
                            $storeDisplayed = false;
                            foreach ($store['value'] as $storeView) {
                                $storeViewId = $storeView['value'];
                                if (in_array($storeViewId, $item[$fieldName])) {
                                    if (!$websiteDisplayed) {
                                        $result .= $website['label'] . '<br/>';
                                        $websiteDisplayed = true;
                                    }
                                    if (!$storeDisplayed) {
                                        $result .= str_repeat('&nbsp;', 4) . $store['label'] . '<br/>';
                                        $storeDisplayed = true;
                                    }
                                    $result .= str_repeat('&nbsp;', 8) . $storeView['label'] . '<br/>';
                                }
                            }
                        }
                    }
                }
                $item[$fieldName] = $result;
            }
        }

        return $dataSource;
    }

    /**
     * Prepare component configuration
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if ($this->storeManager->isSingleStoreMode()) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }
}
