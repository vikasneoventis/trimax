<?php
/**
 *
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CustomerDataCheckoutAttribute;

class Save extends \Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CustomerDataCheckoutAttribute
{
    const URL_REDIRECT_EDIT = 'adminhtml/aitoccheckoutfieldsmanager/customerdatacheckoutattribute/edit';

    protected $prefixTypesOfCheckoutFields = [
        'text_',
        'textarea_',
        'date_',
        'boolean_',
        'multiselect_',
        'select_',
    ];

    /**
     * Prepare Data For Insert in DB
     *
     * @param $orderid integer
     * @param $data    array
     *
     * @return array
     */
    private function prepareDataForInsert($orderid, $data)
    {
        $insertData = [];

        if (!is_array($data)) {
            return $insertData;
        }
        foreach ($data as $key => $value) {
            $attributeId = $this->getCutePrefix($key);
            if ($attributeId == 0) {
                continue;
            }
            if (is_array($value)) {
                $value = join("\n", $value);
            }
            $insertData[] = [
                    'order_id'     => $orderid,
                    'attribute_id' => $attributeId,
                    'value'        => $value,
                ];
        }

        return $insertData;
    }

    /**
     * Сute prefix from field
     *
     * @param $key
     *
     * @return int|null
     */
    public function getCutePrefix($key)
    {
        $attributeId = null;
        foreach ($this->prefixTypesOfCheckoutFields as $prefix) {
            $offset   = 0;
            $position = strpos($key, $prefix, $offset);
            if ($position === false) {
                continue;
            }
            $attributeId = (int)substr($key, strlen($prefix));
        }

        return $attributeId;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $orderId    = $this->getRequest()->getParam('orderid');
            $insertData = $this->prepareDataForInsert((int)$orderId, $data);
            try {
                $result = $this->collection->updateCustomerDataCheckoutByOrderId((int)$orderId, $insertData);
                $this->messageManager->addSuccess(__('You checkout attributes has been successfully saved.'));
                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect(self::URL_REDIRECT_EDIT, ['order_id' => $orderId]);
                }

                return $this->_redirect('sales/order/view', ['order_id' => $orderId]);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the page.'));
            }

            return $this->_redirect('*/*/edit', ['orderid' => $orderId]);
        }

        return $this->_redirect('/');
    }
}
