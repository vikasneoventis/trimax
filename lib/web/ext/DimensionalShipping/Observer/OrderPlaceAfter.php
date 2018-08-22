<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\DimensionalShipping\Observer;

use Aitoc\DimensionalShipping\Helper\Data;
use Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker;
use Aitoc\DimensionalShipping\Model\BoxRepository;
use Aitoc\DimensionalShipping\Model\OrderBoxRepository;
use Aitoc\DimensionalShipping\Model\OrderItemBoxRepository;
use Aitoc\DimensionalShipping\Model\ResourceModel\Box\CollectionFactory;
use Aitoc\DimensionalShipping\Model\ResourceModel\ProductOptions\CollectionFactory as DimensionalShippingProductOptionsCollectionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\ItemRepository;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFctory;

/**
 * Class OrderPlaceAfter
 *
 * @package Aitoc\DimensionalShipping\Observer
 */
class OrderPlaceAfter implements ObserverInterface
{

    const SEPARATELY_EACH_ITEM = 1;
    const SEPARATELY_ONE_BOX_MORE_ITEMS = 2;
    const ERROR_MESSAGE = 'The item can\'t be packed due to dimensions and/or weight not specified or being excessive.';

    protected $boxCollection;
    protected $orderRepository;
    protected $helper;
    protected $boxRepository;
    protected $packingAlgorithmFactory;
    protected $packItems;
    protected $boxItems;
    protected $dimensionalShippingProductOptionsCollectionFactory;
    protected $orderItemBoxRepository;
    protected $orderBoxRepository;
    protected $itemRepository;
    protected $orderItemCollectionFactory;

    /**
     * OrderPlaceAfter constructor.
     *
     * @param CollectionFactory                                  $boxCollection
     * @param OrderRepository                                    $orderRepository
     * @param BoxRepository                                      $boxRepository
     * @param Data                                               $helper
     * @param Boxpacker\PackerFactory                            $packingAlgorithmFactory
     * @param Boxpacker\TestItemFactory                          $packItems
     * @param Boxpacker\TestBoxFactory                           $boxItems
     * @param DimensionalShippingProductOptionsCollectionFactory $dimensionalShippingProductOptionsCollectionFactory
     * @param OrderItemBoxRepository                             $orderItemBoxRepository
     * @param OrderBoxRepository                                 $orderBoxRepository
     */
    public function __construct(
        CollectionFactory $boxCollection,
        OrderRepository $orderRepository,
        BoxRepository $boxRepository,
        Data $helper,
        Boxpacker\PackerFactory $packingAlgorithmFactory,
        Boxpacker\TestItemFactory $packItems,
        Boxpacker\TestBoxFactory $boxItems,
        DimensionalShippingProductOptionsCollectionFactory $dimensionalShippingProductOptionsCollectionFactory,
        OrderItemBoxRepository $orderItemBoxRepository,
        OrderBoxRepository $orderBoxRepository,
        ItemRepository $itemRepository,
        OrderItemCollectionFctory $orderItemCollectionFactory
    ) {
        $this->boxCollection                                      = $boxCollection;
        $this->orderRepository                                    = $orderRepository;
        $this->helper                                             = $helper;
        $this->boxRepository                                      = $boxRepository;
        $this->packingAlgorithmFactory                            = $packingAlgorithmFactory;
        $this->packItems                                          = $packItems;
        $this->boxItems                                           = $boxItems;
        $this->dimensionalShippingProductOptionsCollectionFactory = $dimensionalShippingProductOptionsCollectionFactory;
        $this->orderItemBoxRepository                             = $orderItemBoxRepository;
        $this->orderBoxRepository                                 = $orderBoxRepository;
        $this->itemRepository                                     = $itemRepository;
        $this->orderItemCollectionFactory                         = $orderItemCollectionFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $packingAlgorithm = $this->packingAlgorithmFactory->create();
        $this->addBoxes($packingAlgorithm);
        $orderIds                 = $observer->getEvent()->getOrderIds();
        $flagExcludeChildFromPack = false;
        $idParent                 = null;
        foreach ($orderIds as $orderid) {
            $countBoxes  = $this->boxCollection->create()->count();
            $orderNumber = $orderid;
            $order       = $this->orderRepository->get($orderNumber);
            $orderItems  = $order->getItems();
            foreach ($orderItems as $item) {
                $dSOptionsCollection = $this->dimensionalShippingProductOptionsCollectionFactory->create();
                $productOptions      = $dSOptionsCollection->addFieldToFilter('product_id', $item->getProductId())
                    ->getFirstItem();
                //if product has type bundle or configurable
                if (($item->getProductType() == 'bundle' || $item->getProductType() == 'configurable')) {
                    switch ($item->getProductType()) {
                        case 'bundle':
                            if (!$item->getProduct()->getWeightType()) {
                                $orderItemCollection = $this->orderItemCollectionFactory->create()
                                    ->addFieldToFilter('parent_item_id', $item->getItemId())
                                    ->getItems();
                                if (!$this->checkChildItems($orderItemCollection)) {
                                    $qtyIncrement = 0;
                                    while ($qtyIncrement < $item->getQtyOrdered()) {
                                        $this->saveItemWithError(self::ERROR_MESSAGE, $item);
                                        $qtyIncrement++;
                                    }
                                    $flagExcludeChildFromPack = true;
                                    $idParent            = $item->getItemId();
                                    continue;
                                }
                            }
                            break;
                        case 'configurable':
                            continue 2;
                            break;
                    }
                    if (!$this->validateProductOptions($productOptions) && $item->getProductType() != 'configurable') {
                        continue;
                    }
                    if($item->getProductType() == 'bundle') {
                        $flagExcludeChildFromPack = true;
                        $idParent            = $item->getItemId();
                    }
                }
                //if product bundle/configurable has dimensions then exclude child items from packing
                if ($flagExcludeChildFromPack && $item->getParentItemId() == $idParent) {
                    continue;
                }
                if ($this->validateProductOptions($productOptions)) {
                    if (!empty($item->getWeight())) {
                        if (!$this->helper->checkProductsType($item)) {
                            continue;
                        }
                        $productOptionsConverted = $this->helper->convertUnits($productOptions, 'item');
                        if ($productOptions->getSpecialBox()) {
                            $this->helper->saveProductsInBox(
                                $productOptions->getSelectBox(),
                                $item->getItemId(),
                                $orderNumber,
                                $item->getWeight(),
                                $item->getSku()
                            );
                        } elseif ($productOptions->getPackSeparately()) {
                            $this->packSeparatelyItem(
                                $item,
                                $orderNumber,
                                $productOptions->getPackSeparately(),
                                $productOptionsConverted
                            );
                        } else {
                            $qtyIncrement = 0;
                            while ($qtyIncrement < $item->getQtyOrdered()) {
                                $itemModel = $this->packItems->create(
                                    [
                                        'description' => $item->getName(),
                                        'width'       => $productOptionsConverted->getWidth(),
                                        'length'      => $productOptionsConverted->getHeight(),
                                        'depth'       => $productOptionsConverted->getLength(),
                                        'weight'      => $item->getWeight(),
                                        'keepFlat'    => 0,
                                        'orderItemId' => $item->getItemId()
                                    ]
                                );
                                $packingAlgorithm->addItem($itemModel);
                                $qtyIncrement++;
                            }
                        }
                    } else {
                        if ($countBoxes > 0) {
                            $qtyIncrement = 0;
                            while ($qtyIncrement < $item->getQtyOrdered()) {
                                $this->saveItemWithError(self::ERROR_MESSAGE, $item);
                                $qtyIncrement++;
                            }
                        }
                    }
                } else {
                    if ($item->getProductType() != 'downloadable') {
                        if ($countBoxes > 0) {
                            $qtyIncrement = 0;
                            while ($qtyIncrement < $item->getQtyOrdered()) {
                                $this->saveItemWithError(self::ERROR_MESSAGE, $item);
                                $qtyIncrement++;
                            }
                        }
                    }
                }
                
            }
            $packedBoxesAll = $packingAlgorithm->pack();
            if ($packedBoxesAll) {
                $this->saveBoxesAndItems($packedBoxesAll, $orderid);
            }
        }
    }

    /**
     * @param $algorithm
     */
    private function addBoxes(&$algorithm)
    {
        $boxes = $this->boxCollection->create()->getItems();
        foreach ($boxes as $box) {
            $convertedUnitsBox = $this->helper->convertUnits($box, 'box');
            $boxesModel        = $this->boxItems->create(
                [
                    'reference'   => $convertedUnitsBox->getName(),
                    'outerWidth'  => $convertedUnitsBox->getOuterWidth(),
                    'outerLength' => $convertedUnitsBox->getOuterHeight(),
                    'outerDepth'  => $convertedUnitsBox->getOuterLength(),
                    'emptyWeight' => $convertedUnitsBox->getEmptyWeight(),
                    'innerWidth'  => $convertedUnitsBox->getWidth(),
                    'innerLength' => $convertedUnitsBox->getHeight(),
                    'innerDepth'  => $convertedUnitsBox->getLength(),
                    'maxWeight'   => $convertedUnitsBox->getWeight(),
                    'boxId'       => $convertedUnitsBox->getId()
                ]
            );
            $algorithm->addBox($boxesModel);
        }
    }

    /**
     * @param $item
     * @param $orderId
     * @param $separatelyType
     * @param $productOptions
     */
    private function packSeparatelyItem($item, $orderId, $separatelyType, $productOptions)
    {
        $qtyIncrement                   = 0;
        $packingAlgorithmSeparatelyMore = $this->packingAlgorithmFactory->create();
        $this->addBoxes($packingAlgorithmSeparatelyMore);
        while ($qtyIncrement < $item->getQtyOrdered()) {
            $packingAlgorithmSeparatelyEach = $this->packingAlgorithmFactory->create();
            $this->addBoxes($packingAlgorithmSeparatelyEach);
            $itemModel = $this->packItems->create(
                [
                    'description' => $item->getName(),
                    'width'       => $productOptions->getWidth(),
                    'length'      => $productOptions->getHeight(),
                    'depth'       => $productOptions->getLength(),
                    'weight'      => $item->getWeight(),
                    'keepFlat'    => 0,
                    'orderItemId' => $item->getItemId()
                ]
            );
            $packingAlgorithmSeparatelyEach->addItem($itemModel);
            $packingAlgorithmSeparatelyMore->addItem($itemModel);
            $qtyIncrement++;
            if ($separatelyType == $this::SEPARATELY_EACH_ITEM) {
                $packedBoxes = $packingAlgorithmSeparatelyEach->pack();
                $this->saveBoxesAndItems($packedBoxes, $orderId);
            }
        }
        if ($separatelyType == $this::SEPARATELY_ONE_BOX_MORE_ITEMS) {
            $packedBoxes = $packingAlgorithmSeparatelyMore->pack();
            $this->saveBoxesAndItems($packedBoxes, $orderId);
        }
    }

    /**
     * @param $packedBoxesAll
     * @param $orderid
     */
    private function saveBoxesAndItems($packedBoxesAll, $orderid)
    {
        if ($packedBoxesAll) {
            foreach ($packedBoxesAll as $packedBox) {
                $boxType       = $packedBox->getBox();
                $itemsInTheBox = $packedBox->getItems();

                $orderBoxModel = $this->orderBoxRepository->create();
                $orderBoxModel->setOrderId($orderid);
                $orderBoxModel->setBoxId($boxType->getBoxId());
                $orderBoxModel->setWeight($packedBox->getWeight());
                $orderBoxModel = $this->orderBoxRepository->save($orderBoxModel);

                foreach ($itemsInTheBox as $item) {
                    $orderItemBoxModel = $this->orderItemBoxRepository->create();
                    $itemModel         = $this->itemRepository->get($item->getOrderItemId());
                    $orderItemBoxModel->setOrderItemId($item->getOrderItemId());
                    $orderItemBoxModel->setOrderBoxId($orderBoxModel->getItemId());
                    $orderItemBoxModel->setOrderId($orderid);
                    $orderItemBoxModel->setSku($itemModel->getSku());
                    $orderItemBoxModel->setNotPacked(0);
                    $this->orderItemBoxRepository->save($orderItemBoxModel);
                }
            }
        }
    }

    private function saveItemWithError($error, $item)
    {
        $orderItemBoxModel = $this->orderItemBoxRepository->create();
        $itemModel         = $this->itemRepository->get($item->getItemId());
        $orderItemBoxModel->setOrderItemId($itemModel->getItemId());
        $orderItemBoxModel->setOrderId($itemModel->getOrderId());
        $orderItemBoxModel->setSku($itemModel->getSku());
        $orderItemBoxModel->setErrorMessage($error);
        $orderItemBoxModel->setNotPacked(true);
        $this->orderItemBoxRepository->save($orderItemBoxModel);
    }

    private function checkChildItems($childItems)
    {
        foreach ($childItems as $childItem) {
            $dSOptionsCollection = $this->dimensionalShippingProductOptionsCollectionFactory->create();
            $productOptions      = $dSOptionsCollection->addFieldToFilter('product_id', $childItem->getProductId())
                ->getFirstItem();
            if (!$this->validateProductOptions($productOptions)) {
                return false;
            }
            if (!$childItem->getWeight()) {
                return false;
            }
        }

        return true;
    }

    private function validateProductOptions($productOptions)
    {
        $productDimensionalFIleds = $this->helper->getProductOptionsModelFields('long');
        foreach ($productDimensionalFIleds as $field) {
            $field = 'get' . $field;
            $data  = $productOptions->{$field}();
            if (empty($data) || $data < 0) {
                return false;
            }
        }
        //Validation by dimensions
        /*$pW              = $productOptions->getWeight();
        $pH              = $productOptions->getHeight();
        $pL              = $productOptions->getLength();
        $maxDimensionBox = $this->boxCollection->create()
            ->setOrder('width', 'DESC')
            ->setOrder('height', 'DESC')
            ->setOrder('length', 'DESC')
            ->getFirstItem();
        if ($pW > $maxDimensionBox->getWeight() || $pH > $maxDimensionBox->getHeight()
            || $pL > $maxDimensionBox->getLength()
        ) {
            return false;
        }*/

        return true;
    }
}
