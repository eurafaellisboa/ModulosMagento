<?php


namespace Magento\ValidaCpf\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;

class InstallData implements InstallDataInterface
{

    private $customerSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'razao_social', [
            'type' => 'varchar',
            'label' => 'Razão Social',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 41,
            'system' => false,
            'backend' => ''
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'razao_social')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]
            ]);
        $attribute->save();

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'inscricao_estadual', [
            'type' => 'varchar',
            'label' => 'Inscrição Estadual',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => true,
            'position' => 42,
            'system' => false,
            'backend' => ''
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'inscricao_estadual')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]
            ]);
        $attribute->save();

        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'tipo_pessoa', [
            'type' => 'int',
            'label' => 'Tipo Pessoa',
            'input' => 'select',
            'source' => 'Magento\ValidaCpf\Model\Customer\Attribute\Source\TipoPessoa',
            'required' => false,
            'visible' => true,
            'position' => 40,
            'system' => false,
            'backend' => ''
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'tipo_pessoa')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'adminhtml_checkout',
                'customer_account_create',
                'customer_account_edit'
            ]
            ]);
        $attribute->save();
    }
}