<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Status
 * @package Aitoc\OrdersExportImport\Component\Listing\Columns
 */
class IsCron extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Status constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['is_cron']) {
                    $item[$this->getData('name')] = __('Yes');
                } else {
                    $item[$this->getData('name')] = __('No');
                }
            }
        }

        return $dataSource;
    }
}
