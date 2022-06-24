<?php
namespace Blueskytechco\ProductWidgetAdvanced\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;


class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    private $_eavConfig;
    
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_eavConfig = $eavConfig;
    }

    public function isProductAttributeExists($field)
    {
        $attr = $this->_eavConfig->getAttribute(Product::ENTITY, $field);

        return ($attr && $attr->getId()) ? true : false;
    }
   
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if(!$this->isProductAttributeExists('advanced_is_featured'))
        {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'advanced_is_featured',
                [
                    'group' => 'Product Widget Advanced',
                    'sort_order' => 10,
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Is Featured',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
        }
        if(!$this->isProductAttributeExists('advanced_is_new'))
        {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'advanced_is_new',
                [
                    'group' => 'Product Widget Advanced',
                    'sort_order' => 100,
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Is New',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
        }

        if(!$this->isProductAttributeExists('advanced_is_trending'))
        {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'advanced_is_trending',
                [
                    'group' => 'Product Widget Advanced',
                    'sort_order' => 200,
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Is Trending',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => 0,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => ''
                ]
            );
        }
    }
}
