Read below instruction to update stock sucessfully.

1. "stock" folder should have read and write permission(777).

2. csv filename should have website code capital letters.
   - "NZ" for New Zealand website. EX: import_product_stocks_NZ.csv
   - "GB" or "UK" for United Kingdom website. EX: import_product_stocks_GB.csv
   - "AU" for Australia website. EX: import_product_stocks_AU.csv
   - "NA" for North America website. EX: import_product_stocks_NA.csv
   - "MAIN" for default or main website. EX: import_product_stocks_MAIN.csv

3. Formate in csv file should be:
    - "101-114-443;12;1" in single colume.
    - "101-114-443,12,1" in single colume.
    - "101-114-443" "12" "1" each in 1 colume.

 Where 1st value define SKU, 2nd QTY and 3rd In Stock or Out of Stock.

4.SKU should exists in website.

5.QTY and Stock value should be number.

6.Find error message in "error.log" file and sucess update message in "success.log" file.
