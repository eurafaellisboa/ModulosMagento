##Sparsh Ajax Layered Navigation Extension
This extension extends the Magento 2 layered navigation by allowing your customers to filter, sort and paginate your product catalog with AJAX technology without page refresh.

##Support: 
version - 2.3.x, 2.4.x

##How to install Extension

1. Download the archive file.
2. Unzip the file
3. Create a folder [Magento_Root]/app/code/Sparsh/AjaxLayeredNavigation
4. Drop/move the unzipped files to directory '[Magento_Root]/app/code/Sparsh/AjaxLayeredNavigation'

#Enable Extension:
- php bin/magento module:enable Sparsh_AjaxLayeredNavigation
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Sparsh_AjaxLayeredNavigation
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush
