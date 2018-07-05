<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

error_reporting(1);


use \Magento\Framework\App\Bootstrap;

require __DIR__ . '/app/bootstrap.php';
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$instance = \Magento\Framework\App\ObjectManager::getInstance();
$state = $objectManager->get('\Magento\Framework\App\State');
$state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);

$text = '411-160-842';
$resourceFulltext = $instance ->get('\Magento\CatalogSearch\Model\ResourceModel\Fulltext');
$queryFactory = $instance ->get('\Magento\Search\Model\QueryFactory');

$resourceFulltext->resetSearchResults();
$query = $queryFactory->get();
$query->unsetData();
$query->setQueryText($text);
$query->saveIncrementalPopularity();
$products = [];
$collection = $instance->create(
    'Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection',
    [
        'searchRequestName' => 'quick_search_container'
    ]
);
$collection->addSearchFilter($text);
$collection->addAttributeToSelect('sku');

$collection->setPageSize(50)->setCurPage(1);

$collection->getSelect()
    ->reset('order')
    ->order('search_result.score DESC');


foreach ($collection as $product) {
    $products[] = $product->getData();
}


echo '<pre>'; print_r($products);die;
exit;
$connection = $objectManager->get('\Magento\Framework\App\ResourceConnection')->getConnection();
$storeTable = $connection->getTableName('store');
$query = "SELECT `store_id`,`code` FROM $storeTable";
$store = $connection->fetchAll($query);

$storeData = array();
foreach ($store as $_store){
    $storeData[$_store['code']] = $_store['store_id'];
}

$stockData = array();
$file_handle = fopen('Stock-update.csv', 'r');
while (!feof($file_handle) ) {
    $stockData[] = fgetcsv($file_handle, 1024);
}
fclose($file_handle);

$respone = [];
foreach ($stockData as $key=> $_data) {
    $sku = trim($_data['0']);
    $storeCode = trim($_data['1']);
    $qty = (int)trim($_data['2']);

    if($key == 0 || $sku == null || $storeCode == null || $qty == null)continue;

    $product = $objectManager->create('\Magento\Catalog\Model\ProductRepository')->get($sku);

    $productId = $product->getId();

    if ($productId) {

        $stockId = $storeData[$storeCode];
        $stockTable = $connection->getTableName('warehouseinventory_stock_item');
        $query = "SELECT `item_id` FROM $stockTable WHERE `product_id`= $productId AND `stock_id` = $stockId";
        $itemData = $connection->fetchRow($query);

        $itemModel = $objectManager->get('\Eadesigndev\Warehouses\Model\StockItemsRepository');
        $items = $objectManager->get('\Eadesigndev\Warehouses\Model\StockItemsRepository')->getById($itemData['item_id']);

        try {

            $items->setData(array_merge($items->getData(), array('qty'=>$qty)));
            $itemModel->save($items);
        } catch (\Exception $e) {
            $respone[] = $e->getMessage();
        }

    } else {
        $respone[]=  $sku." sku not found";
    }
}

echo '<pre>'; print_r($respone); die;