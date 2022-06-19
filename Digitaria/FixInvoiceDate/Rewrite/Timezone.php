<?php
namespace Digitaria\FixInvoiceDate\Rewrite;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Phrase;

/**
 * Timezone library
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Timezone extends \Magento\Framework\Stdlib\DateTime\Timezone
{
    /**
     * @param ScopeResolverInterface $scopeResolver
     * @param ResolverInterface $localeResolver
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param ScopeConfigInterface $scopeConfig
     * @param string $scopeType
     * @param string $defaultTimezonePath
     */
    public function __construct(
        ScopeResolverInterface $scopeResolver,
        ResolverInterface $localeResolver,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        ScopeConfigInterface $scopeConfig,
        $scopeType,
        $defaultTimezonePath
    ) {
        parent::__construct($scopeResolver, $localeResolver, $dateTime, $scopeConfig, $scopeType, $defaultTimezonePath);
        $this->_scopeResolver = $scopeResolver;
        $this->_localeResolver = $localeResolver;
        $this->_dateTime = $dateTime;
        $this->_defaultTimezonePath = $defaultTimezonePath;
        $this->_scopeConfig = $scopeConfig;
        $this->_scopeType = $scopeType;
    }
   
    public function scopeDate($scope = null, $date = null, $includeTime = false)
    {
        $timezone = new \DateTimeZone(
            $this->_scopeConfig->getValue($this->getDefaultTimezonePath(), $this->_scopeType, $scope)
        );
        switch (true) {
            case (empty($date)):
                $date = new \DateTime('now', $timezone);
                break;
            case ($date instanceof \DateTime):
            case ($date instanceof \DateTimeImmutable):
                $date = $date->setTimezone($timezone);
                break;            
            default:
                $date = new \DateTime(is_numeric($date) ? '@' . $date : $date);
                $date->setTimezone($timezone);
                break;
        }

        if (!$includeTime) {
            $date->setTime(0, 0, 0);
        }

        return $date;
    }   
}
