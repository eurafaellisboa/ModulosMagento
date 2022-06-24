<?php
namespace Blueskytechco\OnePageCheckout\Helper;

use Exception;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Customer\Helper\Address as CustomerAddressHelper;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Directory\Model\Region;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Address
 * @package Blueskytechco\OnePageCheckout\Helper
 */
class Address extends Data
{
    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * @var Resolver
     */
    protected $_localeResolver;

    /**
     * @var Region
     */
    protected $_regionModel;

    /**
     * @var CustomerAddressHelper
     */
    protected $addressHelper;

    protected $jsonHelper;

    /**
     * @var AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @var Config
     */
    private $resourceConfig;

    /**
     * @var ReinitableConfigInterface
     */
    private $appConfig;

    /**
     * Address constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param DirectoryList $directoryList
     * @param Resolver $localeResolver
     * @param Region $regionModel
     * @param CustomerAddressHelper $addressHelper
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param Config $resourceConfig
     * @param ReinitableConfigInterface $appConfig
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        Resolver $localeResolver,
        Region $regionModel,
        CustomerAddressHelper $addressHelper,
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        Config $resourceConfig,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        ReinitableConfigInterface $appConfig
    ) {
        $this->_directoryList                = $directoryList;
        $this->_localeResolver               = $localeResolver;
        $this->_regionModel                  = $regionModel;
        $this->addressHelper                 = $addressHelper;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->resourceConfig                = $resourceConfig;
        $this->appConfig                     = $appConfig;
        $this->jsonHelper                     = $jsonHelper;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Address Fields
     *
     * @return array
     */
    public function getAddressFields()
    {
        $fieldPosition = $this->getAddressFieldPosition();

        $fields = array_keys($fieldPosition);
        if (!in_array('country_id', $fields, true)) {
            array_unshift($fields, 'country_id');
        }

        if (in_array('region_id', $fields, true)) {
            $fields[] = 'region_id_input';
        }

        return $fields;
    }

    /**
     * Get position to display on one step checkout
     *
     * @return array
     */
    public function getAddressFieldPosition()
    {
        $fieldPosition = [];
        $sortedField   = $this->getSortedField();
        foreach ($sortedField as $field) {
            $fieldPosition[$field->getAttributeCode()] = [
                'sortOrder' => $field->getSortOrder(),
                'colspan'   => $field->getColspan(),
                'required'  => $field->getIsRequiredMp(),
                'isNewRow'  => $field->getIsNewRow(),
            ];
        }

        return $fieldPosition;
    }

    /**
     * Get attribute collection to show on process checkout
     *
     * @param bool|true $onlySorted
     *
     * @return array
     */
    public function getSortedField($onlySorted = true)
    {
        $availableFields = [];
        $sortedFields    = [];
        $sortOrder       = 1;

        $collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );
        /** @var Attribute $field */
        foreach ($collection as $field) {
            if ($this->isAddressAttributeVisible($field)) {
                $availableFields[$field->getAttributeCode()] = $field;
            }
        }

        $collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer',
            'customer_account_create'
        );
        /** @var Attribute $field */
        foreach ($collection as $field) {
            if ($this->isCustomerAttributeVisible($field)) {
                $availableFields[$field->getAttributeCode()] = $field;
            }
        }

        $collection = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'onestepcheckout_index_index'
        );
        /** @var Attribute $field */
        foreach ($collection as $field) {
            if ($field->getIsVisible()) {
                $availableFields[$field->getAttributeCode()] = $field;
            }
        }

        // apply Custom Field label config
        for ($i = 1; $i <= 3; $i++) {
            $key   = 'custom_field_' . $i;
            $field = $availableFields[$key] ?? null;
            if ($field) {
                $field->setDefaultFrontendLabel($this->getCustomFieldLabel($i));
            }
        }

        $colCount = 0;
        foreach ($this->getFieldPosition() as $field) {
            foreach ($availableFields as $code => $avField) {
                if ($field['code'] === $code) {
                    unset($availableFields[$code]);
                    $avField
                        ->setColspan($field['colspan'])
                        ->setSortOrder($sortOrder++)
                        ->setColStyle($this->getColStyle($field['colspan']))
                        ->setIsNewRow($this->getIsNewRow($field['colspan'], $colCount));

                    if (isset($field['required'])) {
                        // cannot set IS_REQUIRED because attribute is not user defined
                        $avField->setIsRequiredMp($field['required']);
                    }

                    $sortedFields[$code] = $avField;
                    break;
                }
            }
        }

        return $onlySorted ? $sortedFields : [$sortedFields, $availableFields];
    }

    /**
     * Check if address attribute can be visible on frontend
     *
     * @param Attribute $attribute
     *
     * @return bool|null|string
     */
    public function isAddressAttributeVisible($attribute)
    {
//        if ($this->isEnableCustomerAttributes() && $attribute->getIsUserDefined()) {
//            return false; // Prevent duplicated customer attributes
//        }

        $code   = $attribute->getAttributeCode();
        $result = $attribute->getIsVisible();
        switch ($code) {
            case 'vat_id':
                $result = $this->addressHelper->isVatAttributeVisible();
                break;
            case 'region':
                $result = false;
                break;
        }

        return $result;
    }

    /**
     * Check if customer attribute can be visible on frontend
     *
     * @param Attribute $attribute
     *
     * @return bool|null|string
     */
    public function isCustomerAttributeVisible($attribute)
    {
        $code = $attribute->getAttributeCode();
        if (in_array($code, ['gender', 'taxvat', 'dob'])) {
            return $attribute->getIsVisible();
        }

        return $attribute->getIsUserDefined();
    }

    /**
     * @return array
     */
    public function getFieldPosition()
    {
        $fields = $this->getConfigValue(self::XML_PATH_SORTED_FIELD_POSITION);
        if(!$fields)
        {
            $fields = '[{"code":"firstname","colspan":12,"required":true},{"code":"lastname","colspan":12,"required":true},{"code":"street","colspan":12,"required":false},{"code":"city","colspan":12,"required":false},{"code":"postcode","colspan":12,"required":false},{"code":"country_id","colspan":12,"required":true},{"code":"region_id","colspan":12,"required":true},{"code":"company","colspan":12,"required":false},{"code":"telephone","colspan":12,"required":true}]';
        }

        return $this->jsonHelper->jsonDecode($fields);
    }


    /**
     * @param int $colSpan
     *
     * @return string
     */
    public function getColStyle($colSpan)
    {
        switch ($colSpan) {
            case 12:
                return 'wide';
            case 9:
                return 'medium';
            case 3:
                return 'short';
            default:
                return '';
        }
    }

    /**
     * @param int $colSpan
     * @param int $colCount
     *
     * @return bool
     */
    public function getIsNewRow($colSpan, &$colCount)
    {
        $result = $colCount === 0;

        $colCount += $colSpan;

        if ($colCount >= 12) {
            $colCount = 0;
        }

        return $result;
    }

}
