##Sparsh Ajax Infinite Scroll Extension
This extension reduces product load time by automatically loads products as users scroll down the website without reloading the whole page.

##Support: 
version - 2.3.x, 2.4.x

##How to install Extension

1. Download the archive file.
2. Unzip the file
3. Create a folder [Magento_Root]/app/code/Sparsh/AjaxInfiniteScroll
4. Drop/move the unzipped files to directory '[Magento_Root]/app/code/Sparsh/AjaxInfiniteScroll'

#Enable Extension:
- php bin/magento module:enable Sparsh_AjaxInfiniteScroll
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Sparsh_AjaxInfiniteScroll
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush
