<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *
 * MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_CustomerApproval
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomerApproval\Plugin;

use Magento\Framework\UrlInterface;
use Bss\CustomerApproval\Helper\Data;

class CustomerListing
{
    /**
     * CustomerListing constructor.
     * @param UrlInterface $urlBuilder
     * @param Data $helper
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Data $helper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\View\Layout\Generic $subject
     * @param \Closure $proceed
     * @param string $component
     * @return array|mixed
     */
    public function aroundBuild(\Magento\Framework\View\Layout\Generic $subject, \Closure $proceed, $component)
    {
        if ($this->helper->isEnable()) {
            if ($component->getName() == 'customer_listing') {
                $result = $proceed($component);
                if (is_array($result)) {
                    if (isset($result['components']['customer_listing']['children']['customer_listing']['children']
                        ['listing_top']['children']['listing_massaction'])) {
                        $approveUrl = $this->urlBuilder->getUrl(
                            'customerapproval/index/massApproved', 
                            $paramsHere = []
                        );
                        $disApproveUrl = $this->urlBuilder->getUrl(
                            'customerapproval/index/massDisapproved', 
                            $paramsHere = []
                        );
                        $approvedAction = [
                            'component' => 'uiComponent',
                            'type' => 'approved',
                            'label' => 'Approved',
                            'url' => $approveUrl,
                            'confirm' => [
                                'title' => 'Approved Customer',
                                'message' => __('Are you sure to Approved selected customers ?')
                            ]
                        ];

                        $disApprovedAction = [
                            'component' => 'uiComponent',
                            'type' => 'disapproved',
                            'label' => 'Disapproved',
                            'url' => $disApproveUrl,
                            'confirm' => [
                                'title' => 'Disapproved Customer',
                                'message' => __('Are you sure to Disapproved selected customers ?')
                            ]
                        ];

                        $result['components']['customer_listing']['children']['customer_listing']['children']
                        ['listing_top']['children']['listing_massaction']['config']['actions'][] = $approvedAction;

                        $result['components']['customer_listing']['children']['customer_listing']['children']
                        ['listing_top']['children']['listing_massaction']['config']['actions'][] = $disApprovedAction;
                    }
                }
            }
        } 
        if (isset($result)) {
            return $result;
        } 

        return $proceed($component);
    }
}
