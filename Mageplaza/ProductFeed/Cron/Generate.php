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

namespace Mageplaza\ProductFeed\Cron;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mageplaza\ProductFeed\Helper\Data;
use Mageplaza\ProductFeed\Model\FeedFactory;
use Mageplaza\ProductFeed\Model\ResourceModel\Feed\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Generate
 * @package Mageplaza\ProductFeed\Cron
 */
class Generate
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * Generate constructor.
     * @param TimezoneInterface $timezone
     * @param LoggerInterface $logger
     * @param Data $helper
     * @param DateTime $dateTime
     * @param CollectionFactory $collectionFactory
     * @param FeedFactory $feedFactory
     */
    public function __construct(
        TimezoneInterface $timezone,
        LoggerInterface $logger,
        Data $helper,
        DateTime $dateTime,
        CollectionFactory $collectionFactory,
        FeedFactory $feedFactory
    )
    {
        $this->logger            = $logger;
        $this->helper            = $helper;
        $this->timezone          = $timezone;
        $this->collectionFactory = $collectionFactory;
        $this->dateTime          = $dateTime;
        $this->feedFactory       = $feedFactory;
    }

    /**
     * Send Mail
     *
     * @return void
     */
    public function execute()
    {
        if ($this->helper->isEnabled()) {
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('execution_mode', 'cron');
            $collection->walk([$this, 'generate']);
        }
    }

    /**
     * @param $feed
     */
    public function generate($feed)
    {
        $cronRunTime = $feed->getCronRunTime();
        $lastCron    = $feed->getLastCron();
        $frequency   = $feed->getFrequency();
        $dayOfWeek   = $feed->getDayOfWeek();
        $dayOfMonth  = $feed->getDayOfMonth();
        if ($this->isGenerate($cronRunTime, $lastCron, $frequency, $dayOfWeek, $dayOfMonth)) {
            try {
                $this->helper->generateAndDeliveryFeed($feed, 0, 1);
                $this->feedFactory->create()->load($feed->getId())->setLastCron($this->dateTime->date())->save();
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }

    /**
     * @param $cronRunTime
     * @param $lastCron
     * @param $frequency
     * @param $dayOfWeek
     * @param $dayOfMonth
     * @return bool
     */
    public function isGenerate($cronRunTime, $lastCron, $frequency, $dayOfWeek, $dayOfMonth)
    {
        $lastCronTime     = strtotime($this->dateTime->date('Y-m-d', $lastCron));
        $time             = explode(',', $cronRunTime);
        $cronRunTimeStamp = $this->timezone->date()
            ->setTimezone(new \DateTimeZone('UTC'))->setTime($time[0], $time[1], 0)->getTimestamp();
        $time             = 60 * 60 * 24;

        switch ($frequency) {
            case \Magento\Cron\Model\Config\Source\Frequency::CRON_DAILY:
                return ((time() - $lastCronTime) >= $time || !$lastCron) && (time() >= $cronRunTimeStamp);
            case \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY:
                $time = $time * 5;
                return date('w') == $dayOfWeek && time() >= $cronRunTimeStamp
                    && ((time() - $lastCronTime) >= $time || !$lastCron);
            case \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY:
                $time = $time * 10;
                return date('j') == $dayOfMonth && time() >= $cronRunTimeStamp
                    && ((time() - $lastCronTime) >= $time || !$lastCron);
        }

        return ((time() - $lastCronTime) >= $time || !$lastCron) && (time() >= $cronRunTimeStamp);
    }
}
