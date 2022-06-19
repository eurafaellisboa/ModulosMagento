<?php  
namespace Digitaria\ProductCustomLabel\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class UpgradeData implements UpgradeDataInterface {

    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if(version_compare($context->getVersion(), '2.1.51') < 0)
        {

            $eavSetup->updateAttribute(
                 \Magento\Catalog\Model\Product::ENTITY, 'ad_producthighview', [
                'type' => 'int',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Exibir Etiqueta de Produto com Alta Procura?',
                'input' => 'select',
                'group' => 'General',
                'class' => 'ad_producthighview',
                'source' => 'Digitaria\ProductCustomLabel\Model\YesNo',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
                    ]
            );
			
			$eavSetup->updateAttribute(
                 \Magento\Catalog\Model\Product::ENTITY, 'ad_productfreeshipping', [
                'type' => 'int',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend' => '',
                'label' => 'Exibir Etiqueta de Produto com Frete Grátis?',
                'input' => 'select',
                'group' => 'General',
                'class' => 'ad_productfreeshipping',
                'source' => 'Digitaria\ProductCustomLabel\Model\YesNo',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
                    ]
            );
        }
        $setup->endSetup();
    }

}
        ?>
