  
**Exploded View Product 1.0** for Magento 2.2  Oct 5 2017

Check the latest README file:
http://hottons.com/demo/m2/pe/README.html

This extension changes page layout of Magento Grouped product.
So that it is possible to upload large exploded view image and the products are listed below.


 
**Index:**

*   Installation

*   How to use it

*   Configuration

*   Troubleshooting



### Installation

**1)** Upload 41 new files :  

    app/code/ProductExploded/Block/Adminhtml/Pe/Product/Labels.php
    app/code/ProductExploded/Block/Adminhtml/Product/Edit/Js.php
    app/code/ProductExploded/Block/Product/View/Type/Grouped.php
    app/code/ProductExploded/composer.json
    app/code/ProductExploded/Controller/Adminhtml/Pe/Product/Labels/Index.php
    app/code/ProductExploded/Controller/Adminhtml/Pe/Product/Labels.php
    app/code/ProductExploded/etc/adminhtml/di.xml
    app/code/ProductExploded/etc/adminhtml/events.xml
    app/code/ProductExploded/etc/adminhtml/routes.xml
    app/code/ProductExploded/etc/adminhtml/system.xml
    app/code/ProductExploded/etc/config.xml
    app/code/ProductExploded/etc/frontend/di.xml
    app/code/ProductExploded/etc/module.xml
    app/code/ProductExploded/Helper/Data.php
    app/code/ProductExploded/i18n/en_US.csv
    app/code/ProductExploded/LICENSE.txt
    app/code/ProductExploded/Model/Label.php
    app/code/ProductExploded/Model/Link.php
    app/code/ProductExploded/Model/Observer/ProductSaveAfter.php
    app/code/ProductExploded/Model/ResourceModel/Label.php
    app/code/ProductExploded/Model/ResourceModel/Link.php
    app/code/ProductExploded/Plugin/GroupedProduct/Model/Product/Type/Grouped.php
    app/code/ProductExploded/Plugin/GroupedProduct/Ui/DataProvider/Product/Form/Modifier/Grouped.php
    app/code/ProductExploded/README.md
    app/code/ProductExploded/registration.php
    app/code/ProductExploded/Setup/InstallSchema.php
    app/code/ProductExploded/Ui/DataProvider/Product/Form/Modifier/Labels.php
    app/code/ProductExploded/view/adminhtml/layout/catalog_product_new.xml
    app/code/ProductExploded/view/adminhtml/templates/product/edit/js.phtml
    app/code/ProductExploded/view/adminhtml/templates/product/labels.phtml
    app/code/ProductExploded/view/adminhtml/web/product/main.css
    app/code/ProductExploded/view/adminhtml/web/product/main.js
    app/code/ProductExploded/view/adminhtml/web/template/form/components/labels_js.html
    app/code/ProductExploded/view/frontend/layout/catalog_product_view_type_grouped.xml
    app/code/ProductExploded/view/frontend/requirejs-config.js
    app/code/ProductExploded/view/frontend/templates/product/view/type/grouped.phtml
    app/code/ProductExploded/view/frontend/web/images/minus.png
    app/code/ProductExploded/view/frontend/web/images/plus.png
    app/code/ProductExploded/view/frontend/web/js/main.js
    app/code/ProductExploded/view/frontend/web/js/widget.js
    app/code/ProductExploded/view/frontend/web/main.css
  


**2)** Connect to your website via SSH:  
Type in Terminal of your computer:  
```
ssh -p 2222 username@yourdomain.com  
```
Then enter your server password  

If you are connected to your server change directory with command:  
```
cd /<full_path_to_your_magento_root_directory>  
```
Update magento modules with command:  
```
./bin/magento setup:upgrade  
```
NOTE: If it shows permission error make it executable with command:` chmod +x bin/magento `

**3)** Manually remove cached _requirejs diectory:
  
    pub/static/_requirejs  


**4)** Refresh magento cache:  
Go to _Magento admin panel -> System -> Cache Managment_  
Click the "Flush Magento Cache" button. 



### Example of how to use it

**1)** Create new or select existing Grouped product

**2)** Upload exploded view image as "Base" product image

**3)** Add Grouped products and set Number on Image.
These numbers will be visible on front-end and they will mean spare part numbers on the exploded view image

**4)** Add clickable areas on the product image and move them with mouse in the Exploded View Labels section.

**5)** (Optional) To display extra product data, you can create product attribute with code "manufacturer_number" in:
_Stores -> Attributes -> Product_
Then apply it to the attribute set of your product in:
_Stores -> Attributes -> Attribute Set_
The atribute title will be used as a column title:

**6)** Check result on front-end. It should highlight products and scroll the page when you click on a selectable area.



### Configuration

You can set attribute code for the optional Manufacturer Number column in:

_Stores -> Settings -> Configuration -> EXPLODED VIEW PRODUCT_

The attribute title will be used as column title: (screenshot)



### Troubleshooting

*   If you are not sure whether a problem is because of this extension or not.
    
    Try to disable this extension by setting: 
   
    ```
    'Pektsekye_ProductExploded' => 0,
    ```
       
    in the file:  
    
        app/etc/config.php

