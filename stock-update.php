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


$connection = $objectManager->get('\Magento\Framework\App\ResourceConnection')->getConnection();
$storeTable = $connection->getTableName('store');
$query = "SELECT `store_id`,`code` FROM $storeTable";
$store = $connection->fetchAll($query);

$storeData = array();
foreach ($store as $_store){
    $storeData[$_store['code']] = $_store['store_id'];
}

$errors = array();
<<<<<<< HEAD
$uploadedFiles = array_diff(scandir('stock'), array('.', '..'));
=======
$uploadedFiles = array_diff(scandir(BP.'/stock'), array('.', '..'));
>>>>>>> 120448d3f8e03fd5712cbb630f81e1e6d61f82f5

foreach ($uploadedFiles as $file) {

    $fileInfo = pathinfo($file);
    if(strtolower($fileInfo['extension']) != 'csv'){
        continue;
    }

    $storeCode = '';
    if(strpos($fileInfo['filename'], 'NZ')){
        $storeCode = 'nz_en';
    }

    if(strpos($fileInfo['filename'], 'GB') || strpos($fileInfo['filename'], 'UK')){
        $storeCode = 'uk_en';
    }

    if(strpos($fileInfo['filename'], 'AU')){
        $storeCode = 'au_en';
    }

    if(strpos($fileInfo['filename'], 'MAIN')){
        $storeCode = 'default';
    }

    if(strpos($fileInfo['filename'], 'NA')){
        $storeCode = 'na_en';
    }

    if(!$storeCode){
        $errors[] = "Invalid filename name $file. Filename should have valid store code. EX: NZ, GB or UK, AU, MAIN, NA.";
    }

    $stockData = array();
<<<<<<< HEAD
    $fileName = 'stock/'.$file;
=======
    $fileName = BP . '/stock/'.$file;
>>>>>>> 120448d3f8e03fd5712cbb630f81e1e6d61f82f5
    $file_handle = fopen($fileName, 'r');
    while (!feof($file_handle)) {
        $stockData[] = fgetcsv($file_handle, 1024);
    }
    fclose($file_handle);

    $respone = [];
    foreach ($stockData as $key => $_data) {

        if(!is_array($_data)){
            continue;
        }
        if(count($_data) && count($_data) != 3 ){
            if($_data[0]){
                $_data = explode(',',$_data[0]);

                if(count($_data) != 3){
                    $_data = explode(';',$_data[0]);
                }
            }
        }

        if(count($_data) && count($_data) != 3 ){
            $errors[] = "Invalid row $_data.";
            continue;
        }

        $sku = trim($_data['0']);
        $qty = (int)trim($_data['1']);
        $isInStock = (boolean)trim($_data['2']);

        if ($sku == null || $isInStock == null || !is_numeric($qty)){
            $errors = "Invalid row data $sku";
            continue;
        }

        $productId = $objectManager->create('\Magento\Catalog\Model\ResourceModel\Product')->getIdBySku($sku);

        if ($productId) {

            $stockId = $storeData[$storeCode];
            $stockTable = $connection->getTableName('warehouseinventory_stock_item');
            $query = "SELECT `item_id` FROM $stockTable WHERE `product_id`= $productId AND `stock_id` = $stockId";
            $itemData = $connection->fetchRow($query);

            $itemModel = $objectManager->get('\Eadesigndev\Warehouses\Model\StockItemsRepository');
            $items = $objectManager->get('\Eadesigndev\Warehouses\Model\StockItemsRepository')->getById($itemData['item_id']);

            try {

                $items->setData(array_merge($items->getData(), array('qty' => $qty,'is_in_stock'=>$isInStock)));
                $itemModel->save($items);
                $respone[$file] = "$file : Stock updated successfully.";
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

        } else {
            $errors[] = "$sku : SKU not found";
        }
    }

    if(count($respone)){

        if (!unlink($fileName))
        {
            $errors[] = "Error deleting $file";
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/stock/success.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($respone, true));
    }

    if(count($errors)) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/stock/error.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($errors, true));
    }
}

