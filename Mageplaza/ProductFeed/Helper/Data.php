<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductFeed\Helper;

use Liquid\Template;
use Magento\Backend\Model\UrlInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Framework\Filesystem\Io\Sftp;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Review\Model\Review\SummaryFactory;
use Magento\Review\Model\ReviewFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;
use Mageplaza\ProductFeed\Block\Adminhtml\LiquidFilters;
use Mageplaza\ProductFeed\Model\FeedFactory;
use Mageplaza\ProductFeed\Model\HistoryFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class Data
 * @package Mageplaza\ProductFeed\Helper
 */
class Data extends CoreHelper
{
    const CONFIG_MODULE_PATH = 'product_feed';
    const XML_PATH_EMAIL     = 'email';
    const FEED_FILE_PATH     = BP . '/pub/media/mageplaza/feed/';

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var LiquidFilters
     */
    protected $liquidFilters;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Ftp
     */
    protected $ftp;

    /**
     * @var Sftp
     */
    protected $sftp;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var SummaryFactory
     */
    protected $reviewSummaryFactory;

    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var UrlInterface
     */
    protected $backendUrl;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var StockRegistryInterface
     */
    protected $stockState;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $productFactory
     * @param CategoryFactory $categoryFactory
     * @param UrlInterface $backendUrl
     * @param Ftp $ftp
     * @param Sftp $sftp
     * @param JsonHelper $jsonHelper
     * @param ManagerInterface $messageManager
     * @param TransportBuilder $transportBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param DateTime $date
     * @param TimezoneInterface $timezone
     * @param Resolver $resolver
     * @param File $file
     * @param ReviewFactory $reviewFactory
     * @param SummaryFactory $reviewSummaryFactory
     * @param StockRegistryInterface $stockState
     * @param LiquidFilters $liquidFilters
     * @param HistoryFactory $historyFactory
     * @param FeedFactory $feedFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory,
        CategoryFactory $categoryFactory,
        UrlInterface $backendUrl,
        Ftp $ftp,
        Sftp $sftp,
        JsonHelper $jsonHelper,
        ManagerInterface $messageManager,
        TransportBuilder $transportBuilder,
        ProductRepositoryInterface $productRepository,
        DateTime $date,
        TimezoneInterface $timezone,
        Resolver $resolver,
        File $file,
        ReviewFactory $reviewFactory,
        SummaryFactory $reviewSummaryFactory,
        StockRegistryInterface $stockState,
        LiquidFilters $liquidFilters,
        HistoryFactory $historyFactory,
        FeedFactory $feedFactory
    )
    {
        parent::__construct($context, $objectManager, $storeManager);

        $this->productFactory       = $productFactory;
        $this->file                 = $file;
        $this->liquidFilters        = $liquidFilters;
        $this->productRepository    = $productRepository;
        $this->categoryFactory      = $categoryFactory;
        $this->ftp                  = $ftp;
        $this->sftp                 = $sftp;
        $this->messageManager       = $messageManager;
        $this->date                 = $date;
        $this->historyFactory       = $historyFactory;
        $this->transportBuilder     = $transportBuilder;
        $this->reviewFactory        = $reviewFactory;
        $this->reviewSummaryFactory = $reviewSummaryFactory;
        $this->feedFactory          = $feedFactory;
        $this->resolver             = $resolver;
        $this->timezone             = $timezone;
        $this->backendUrl           = $backendUrl;
        $this->jsonHelper           = $jsonHelper;
        $this->stockState           = $stockState;
    }

    /**
     * @param $time
     *
     * @return \DateTime|string
     * @throws \Exception
     */
    public function convertToLocaleTime($time)
    {
        $localTime = new \DateTime($time, new \DateTimeZone('UTC'));
        $localTime->setTimezone(new \DateTimeZone($this->timezone->getConfigTimezone()));
        $localTime = $localTime->format('Y-m-d H:i:s');

        return $localTime;
    }

    /**
     * @param string $code
     * @param null $storeId
     * @return mixed
     */
    public function getEmailConfig($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getModuleConfig(self::XML_PATH_EMAIL . $code, $storeId);
    }

    /**
     * @param $sendTo
     * @param $mes
     * @param $emailTemplate
     * @param $storeId
     * @return bool
     */
    public function sendMail($sendTo, $mes, $emailTemplate, $storeId)
    {
        try {
            $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions([
                    'area'  => Area::AREA_FRONTEND,
                    'store' => $storeId,
                ])
                ->setTemplateVars([
                    'viewLogUrl' => $this->backendUrl->getUrl('mpproductfeed/logs/'),
                    'mes'        => $mes
                ])
                ->setFrom('general')
                ->addTo($sendTo);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();

            return true;
        } catch (\Magento\Framework\Exception\MailException $e) {
            $this->_logger->critical($e->getLogMessage());
        }

        return false;
    }

    /**
     * @param $feed
     * @param bool $forceGenerate
     * @param bool $isCron
     * @throws \Exception
     */
    public function generateAndDeliveryFeed($feed, $forceGenerate = false, $isCron = false)
    {
        if (!$this->isEnabled()) {
            $this->messageManager->addError(__('Please enable Mageplaza_ProductFeed module'));

            return;
        }
        if (!$feed->getStatus() && !$forceGenerate) {
            return;
        }

        $status       = 'Error';
        $delivery     = 'Error';
        $productCount = 0;
        try {
            $productCount = $this->generateLiquidTemplate($feed);
            $this->messageManager->addSuccess(__('%1 feed has been generated successfully.', $feed->getName()));
            $this->feedFactory->create()->load($feed->getId())->setLastGenerated($this->date->date())->save();
            $status = 'Success';
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while generating %1 Feed. %2',
                $feed->getName(), $e->getMessage()));
        }
        if ($status == 'Success') {
            if ($feed->getDeliveryEnable()) {
                try {
                    $this->deliveryFeed($feed);
                    $this->messageManager->addSuccess(__('%1 feed has been uploaded successfully', $feed->getName()));
                    $delivery = 'Success';
                } catch (LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Something went wrong while uploading %1 Feed. %2',
                        $feed->getName(), $e->getMessage()));
                }
            } else {
                $delivery = 'Disabled';
            }
        }
        $successMessage = [];
        $errorMessage   = [];

        foreach ($this->messageManager->getMessages()->getItems() as $message) {
            if ($message->getType() == 'success') {
                $successMessage[] = $message->getText();
            } else {
                $errorMessage[] = $message->getText();
            }
        }
        $successMessage = implode("\n", $successMessage);
        $errorMessage   = implode("\n", $errorMessage);

        if ($this->getEmailConfig('enabled')) {
            $generateStt = $status == 'Success' ? 0 : 1;
            $generateMes = $generateStt == 0
                ? ('<p style="color: green">' . __('%1 feed generated successful', $feed->getName()) . '</p>')
                : ('<p style="color: red">' . __('%1 feed generated fail', $feed->getName()) . '</p>');
            $deliveryStt = $delivery == 'Success' ? 2 : ($delivery == 'Error' ? 3 : 4);
            $deliveryMes = $deliveryStt == 2
                ? '<p style="color: green">' . __('%1 feed delivery successful', $feed->getName()) . '</p>'
                : ($deliveryStt == 3
                    ? ('<p style="color: red">' . __('%1 feed delivery fail', $feed->getName()) . '</p>') : '');
            $events      = explode(',', $this->getEmailConfig('events'));
            $sendTo      = explode(',', $this->getEmailConfig('send_to'));
            if (in_array($generateStt, $events) || in_array($deliveryStt, $events)) {
                $this->sendMail(
                    $sendTo,
                    $generateMes . $deliveryMes,
                    'product_feed_email_template',
                    $feed->getStoreId()
                );
            }
        }

        $history = $this->historyFactory->create();
        $history->setData([
            'feed_id'         => $feed->getId(),
            'feed_name'       => $feed->getName(),
            'status'          => $status,
            'delivery'        => $delivery,
            'type'            => $isCron ? 'cron' : 'manual',
            'product_count'   => $productCount,
            'file'            => $feed->getFileName() . '.' . $feed->getFileType(),
            'success_message' => $successMessage,
            'error_message'   => $errorMessage
        ])->save();

        if ($isCron) {
            $this->messageManager->getMessages()->clear();
        }
    }

    /**
     * @param $protocol
     * @param $host
     * @param $passive
     * @param $user
     * @param $pass
     * @return int
     */
    public function testConnection($protocol, $host, $passive, $user, $pass)
    {
        try {
            if ($protocol == 'sftp') {
                if (!isset($args['timeout'])) {
                    $args['timeout'] = Sftp::REMOTE_TIMEOUT;
                }
                if (strpos($host, ':') !== false) {
                    list($host, $port) = explode(':', $host, 2);
                } else {
                    $port = Sftp::SSH2_PORT;
                }
                $connection = new \phpseclib\Net\SFTP($host, $port, 10);

                return $connection->login($user, $pass) ? 1 : 0;
            } else {
                $open = $this->ftp->open([
                    'host'     => $host,
                    'user'     => $user,
                    'password' => $pass,
                    'ssl'      => true,
                    'passive'  => $passive
                ]);

                return $open ? 1 : 0;
            }
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * @param $feed
     * @throws \Exception
     */
    public function deliveryFeed($feed)
    {
        $host          = $feed->getHostName();
        $username      = $feed->getUserName();
        $password      = $feed->getPassword();
        $timeout       = '20';
        $passiveMode   = $feed->getPassiveMode();
        $fileName      = $feed->getFileName() . '.' . $feed->getFileType();
        $fileUrl       = $this->getFileUrl($fileName);
        $directoryPath = $feed->getDirectoryPath();
        if ($feed->getProtocol() == 'sftp') {
            // Fix Magento bug in 2.1.x
            if (!isset($args['timeout'])) {
                $args['timeout'] = Sftp::REMOTE_TIMEOUT;
            }
            if (strpos($host, ':') !== false) {
                list($host, $port) = explode(':', $host, 2);
            } else {
                $port = Sftp::SSH2_PORT;
            }
            $connection = new \phpseclib\Net\SFTP($host, $port, $timeout);
            if (!$connection->login($username, $password)) {
                throw new \Exception(__("Unable to open SFTP connection as %1@%2", $username, $password));
            }
            $content = file_get_contents($fileUrl);
            $mode    = is_readable($content) ? \phpseclib\Net\SFTP::SOURCE_LOCAL_FILE : \phpseclib\Net\SFTP::SOURCE_STRING;
            $connection->put($directoryPath, $content, $mode);
            $connection->disconnect();

            //2.2.x

            //			$this->sftp->open([
            //				'host' => $host,
            //				'username' => $username,
            //				'password' => $password,
            //			]);
            //			$content = file_get_contents($fileUrl);
            //			$this->sftp->write($directoryPath, $content);
            //			$this->sftp->close();

        } else {
            $open = $this->ftp->open([
                'host'     => $host,
                'user'     => $username,
                'password' => $password,
                'ssl'      => true,
                'passive'  => $passiveMode
            ]);
            if ($open) {
                $content = file_get_contents($fileUrl);
                $this->ftp->write($directoryPath, $content);
                $this->ftp->close();
            } else {
                throw new \Exception(__("Unable to authenticate with server"));
            }
        }
    }

    /**
     * @param $feed
     * @return int
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Serializer_Exception
     */
    public function generateLiquidTemplate($feed)
    {
        $template       = new Template;
        $filtersMethods = $this->liquidFilters->getFiltersMethods();

        $template->registerFilter($this->liquidFilters);
        $fileType = $feed->getFileType();

        if ($fileType == 'xml') {
            $templateHtml = $feed->getTemplateHtml();
        } else {
            $fieldSeparate = $feed->getFieldSeparate() == 'tab' ? "\t" : ($feed->getFieldSeparate() == 'comma' ? ',' : ';');
            $fieldAround   = $feed->getFieldAround() == 'none' ? '' : ($feed->getFieldAround() == 'quote' ? "'" : '"');
            $includeHeader = $feed->getIncludeHeader();
            $fieldsMap     = $this->jsonHelper->jsonDecode($feed->getFieldsMap());
            $row           = [];
            foreach ($fieldsMap as $field) {
                $row[0][] = $field['col_name'];

                if ($field['col_type'] == 'attribute') {
                    $row[1][] = $fieldAround . $field['col_val'] . $fieldAround;
                } else {
                    $row[1][] = $fieldAround . $field['col_pattern_val'] . $fieldAround;
                }
            }

            $row[0] = implode($fieldSeparate, $row[0]);
            $row[1] = implode($fieldSeparate, $row[1]);

            if ($includeHeader) {
                $templateHtml = $row[0] . '
' . '{% for product in products %}' . $row[1] . '
{% endfor %}';
            } else {
                $templateHtml = '{% for product in products %}' . $row[1] . '
{% endfor %}';
            }

            $templateHtml = str_replace('}}', "| mpCorrect: '" . $feed->getFieldAround() . "', '" . $feed->getFieldSeparate() . "'}}", $templateHtml);
        }

        $productCollection = $this->getProductsData($feed);
        array_push($filtersMethods, 'mpCorrect');

        $reviewCollection = $this->getReviewCollection();

        $template->parse($templateHtml, $filtersMethods);
        $content = $template->render([
            'products' => $productCollection,
            'store'    => $this->getStoreData($feed->getStoreId()),
            'reviews'  => $reviewCollection
        ]);

        $this->file->checkAndCreateFolder(self::FEED_FILE_PATH);
        $fileName = $feed->getFileName() . '.' . $feed->getFileType();
        $fileUrl  = self::FEED_FILE_PATH . '/' . $fileName;
        $this->file->write($fileUrl, $content);

        return $productCollection->getSize();
    }

    /**
     * @param $id
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreData($id)
    {
        $store     = $this->storeManager->getStore($id);
        $locale    = $this->resolver->getLocale();
        $storeData = [
            'locale_code' => $locale,
            'base_url'    => $store->getBaseUrl()
        ];

        return $storeData;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getReviewCollection()
    {
        $collection = $this->reviewFactory->create()->getCollection();
        /** @var $review \Magento\Review\Model\Review */
        foreach ($collection as $review) {
            $review->setUrl($review->getReviewUrl());
            $product = $this->productFactory->create()->load($review->getEntityPkValue());
            $product->setUrl($product->getProductUrl());
            $rating = ($this->reviewSummaryFactory->create()->load($review->getId())->getRatingSummary()) * 5 / 100;
            $review->setRating($rating);
            $review->setProduct($product);
        }

        return $collection;
    }

    /**
     * @param \Mageplaza\ProductFeed\Model\Feed $feed
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Serializer_Exception
     */
    public function getProductsData($feed)
    {
        $campaignUrl = '?utm_source=' . $feed->getCampaignSource() .
            '&utm_medium=' . $feed->getCampaignMedium() .
            '&utm_campaign=' . $feed->getCampaignName() .
            '&utm_term=' . $feed->getCampaignTerm() .
            '&utm_content=' . $feed->getCampaignContent();
        $categoryMap = $this->unserialize($feed->getCategoryMap());

        $allCategory    = $this->categoryFactory->create()->getCollection()->addAttributeToSelect('name');
        $categoriesName = [];
        /** @var $item \Magento\Catalog\Model\Category */
        foreach ($allCategory as $item) {
            $categoriesName[$item->getId()] = $item->setStoreId($feed->getStoreId())->getName();
        }
        $matchingProductIds = $feed->getMatchingProductIds(1);
        $productCollection  = $this->productFactory->create()->getCollection()
            ->addAttributeToSelect('*')->addStoreFilter($feed->getStoreId())
            ->addIdFilter($matchingProductIds)->addMediaGalleryData();
        /** @var $product \Magento\Catalog\Model\Product */
        foreach ($productCollection as $product) {
            $typeInstance           = $product->getTypeInstance();
            $childProductCollection = $typeInstance->getAssociatedProducts($product);
            if ($childProductCollection) {
                $product->setAssociatedProducts($childProductCollection);
            } else {
                $product->setAssociatedProducts([]);
            }

            $stockItem         = $this->stockState->getStockItem(
                $product->getId(),
                $feed->getStoreId()
            );
            $qty               = $stockItem->getQty();
            $categories        = $product->getCategoryCollection()->addAttributeToSelect('*');
            $relatedProducts   = $product->getRelatedProducts();
            $crossSellProducts = $product->getCrossSellProducts();
            $upSellProducts    = $product->getUpSellProducts();
			$percent = 10;
        	$total   = $product->getFinalPrice();

        	$discount_value = ($total / 100) *$percent;

        	$finalPrices = $total - $discount_value;
           // $finalPrice        = $product->getFinalPrice();

            $productClone = $this->productRepository->getById($product->getId(), false, $feed->getStoreId());
            $productLink  = $productClone->getUrlModel()->getUrlInStore($product, ['_escape' => true]) . $campaignUrl;

            $imageLink = $this->storeManager->getStore($feed->getStoreId())
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product' . $product->getImage();
            $images    = $product->getMediaGalleryImages()->getSize() ? $product->getMediaGalleryImages() : [[]];
            /** @var $category \Magento\Catalog\Model\Category */
            $lv           = 0;
            $categoryPath = '';
            $cat          = new DataObject();
            foreach ($categories as $category) {
                if ($lv < $category->getLevel()) {
                    $lv  = $category->getLevel();
                    $cat = $category;
                }
            }
            $mapping = '';
            if (isset($categoryMap[$cat->getId()])) {
                $mapping = $categoryMap[$cat->getId()];
            }
            $catPaths = array_reverse(explode(',', $cat->getPathInStore()));
            foreach ($catPaths as $index => $catId) {
                if ($index == (count($catPaths) - 1)) {
                    $categoryPath .= isset($categoriesName[$catId]) ? $categoriesName[$catId] : '';
                } else {
                    $categoryPath .= isset($categoriesName[$catId]) ? $categoriesName[$catId] : '' . ' > ';
                }
            }

            $product->isAvailable() ? $product->setData('quantity_and_stock_status', 'in stock')
                : $product->setData('quantity_and_stock_status', 'out of stock');

            // Convert attribute value to attribute text

//            foreach ($product->getAttributes() as $attribute) {
//                try {
//                    $attributeCode = $attribute->getAttributeCode();
//                    if($attributeCode == 'quantity_and_stock_status'){
//                        continue;
//                    }
//                    $attributeText = $product->getAttributeText($attributeCode);
//                    if (is_array($attributeText)) {
//                        $attributeText = implode(',', $attributeText);
//                    }
//                    if ($attributeText) {
//                        $product->setData($attributeCode, $attributeText);
//                    }
//                } catch (\Exception $e) {
//                    continue;
//                }
//            }
            $product->setData('categoryCollection', $categories);
            $product->setData('relatedProducts', $relatedProducts);
            $product->setData('crossSellProducts', $crossSellProducts);
            $product->setData('upSellProducts', $upSellProducts);
            $product->setData('finalPrice', $finalPrices);
            $product->setData('link', $productLink);
            $product->setData('image_link', $imageLink);
            $product->setData('images', $images);
            $product->setData('category_path', $categoryPath);
            $product->setData('mapping', $mapping);
            $product->setData('qty', $qty);
        }

        return $productCollection;
    }

    /**
     * @param $filename
     * @return string
     */
    public function getFileUrl($filename)
    {
        return $this->_urlBuilder->getBaseUrl(
                ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
            ) . 'mageplaza/feed/' . $filename;
    }
}
