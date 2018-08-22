<?php

namespace Aitoc\CheckoutFieldsManager\Model\Order\Pdf;

class Shipment extends \Magento\Sales\Model\Order\Pdf\Shipment
{
    use \Aitoc\CheckoutFieldsManager\Traits\CustomFields;

    /**
     * @param \Zend_Pdf_Page             $page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool                       $putOrderId
     *
     * @throws \Zend_Pdf_Exception
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        parent::insertOrder($page, $obj, $putOrderId);

        if ($obj instanceof \Magento\Sales\Model\Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof \Magento\Sales\Model\Order\Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }
        $this->prepareCheckoutFieldsData($order->getId());
        $count = count($this->checkoutFieldsData);
        if ($count) {
            $top = $this->y;
            $top += 10;
            $yStepText = 15;
            $aitocCheckoutFieldsBlockSize = 30 * $count;
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.75));
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $top, 570, $top - $aitocCheckoutFieldsBlockSize);

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $top, 570, $top - 25);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $this->_setFontBold($page, 12);
            $checkoutFieldsDataLeft = self::setShowValue($this->checkoutFieldsData);
            $page->drawText(__('Custom Fields:'), 35, $top - 15, 'UTF-8');
            $top -= 40;
            foreach ($checkoutFieldsDataLeft as $oneField) {
                $page->drawText(strip_tags(ltrim($oneField['field_name'])), 35, $top, 'UTF-8');
                $page->drawText(strip_tags(ltrim($oneField['value'])), 285, $top, 'UTF-8');
                $top -= $yStepText;
            }
            $this->y = $top;
        }
    }
}
