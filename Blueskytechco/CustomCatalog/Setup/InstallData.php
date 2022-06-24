<?php
namespace Blueskytechco\CustomCatalog\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
	const PRODUCT_GROUP = 'Product Details';
    protected $state;
    protected $attributeRepository;
    protected $filesystem;
    protected $swatchHelper;
    protected $productMediaConfig;
    protected $driverFile;
    protected $eavSetupFactory;

	public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\App\State $state,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Swatches\Helper\Media $swatchHelper,
        \Magento\Catalog\Model\Product\Media\Config $productMediaConfig,
        \Magento\Framework\Filesystem\Driver\File $driverFile
    ){
        $this->eavSetupFactory = $eavSetupFactory;
        $this->state = $state;
        $this->attributeRepository = $attributeRepository;
        $this->filesystem = $filesystem;
        $this->swatchHelper = $swatchHelper;
        $this->productMediaConfig = $productMediaConfig;
        $this->driverFile = $driverFile;
    }

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
        try{
            $this->state->getAreaCode();
        }
        catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->state->setAreaCode('adminhtml');
        }
		$eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'hover',
            [
                'type' => 'varchar',
                'label' => 'Hover',
                'input' => 'media_image',
                'required' => false,
                'sort_order' => 100,
                'frontend' => \Magento\Catalog\Model\Product\Attribute\Frontend\Image::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'used_in_product_listing' => true,
                'user_defined' => true,
                'visible' => true,
                'visible_on_front' => false
            ]
        );
        $id = $eavSetup->getAttributeId(
            \Magento\Catalog\Model\Product::ENTITY,
            'hover'
        );
        $attributeSetIds = $eavSetup->getAllAttributeSetIds(\Magento\Catalog\Model\Product::ENTITY);
        if(is_array($attributeSetIds) && !empty($attributeSetIds)){
            foreach($attributeSetIds as $attributeSetId){
                $eavSetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, $attributeSetId, 'image-management', $id, 10);
            }
        }
    
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'end_date_in_stock',
            [
                'group' => 'General',
                'type' => 'datetime',
                'backend' => '',
                'frontend' => 'Magento\Eav\Model\Entity\Attribute\Frontend\Datetime',
                'label' => 'End Date In Stock',
                'input' => 'date',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'sort_order' => 100,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'brand',
            [
                'type' => 'int',
                'label' => 'Brand Product',
                'input' => 'select',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => self::PRODUCT_GROUP,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
                'user_defined' => true,
                'filterable' => 2,
                'filterable_in_search' => true,
                'used_for_promo_rules' => true,
                'is_html_allowed_on_front' => true,
                'used_for_sort_by' => true,
            ],
        );

        $attributesOptionsData = [
            'brand' => [
                \Magento\Swatches\Model\Swatch::SWATCH_INPUT_TYPE_KEY => \Magento\Swatches\Model\Swatch::SWATCH_INPUT_TYPE_VISUAL,
                'optionvisual' => [
                    'value'     => [
                    ],
                ],
                'swatchvisual' => [
                    'value'     => [
                    ],
                ],
            ]
        ];

        $this->addProductAttributes($attributesOptionsData);
	}

	public function addProductAttributes($attributesOptionsData)
    {
        foreach ($attributesOptionsData as $attributeOptionsData) {
            $order = 0;
            $swatchVisualFiles = isset($attributeOptionsData['optionvisual']['value'])
                ? $attributeOptionsData['optionvisual']['value']
                : [];
            foreach ($swatchVisualFiles as $index => $swatchVisualFile) {
                if (!isset($attributeOptionsData['optionvisual']['order'][$index])) {
                    $attributeOptionsData['optionvisual']['order'][$index] = ++$order;
                }
            }
        }

        // Prepare visual swatches files.
        $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $tmpMediaPath = $this->productMediaConfig->getBaseTmpMediaPath();
        $fullTmpMediaPath = $mediaDirectory->getAbsolutePath($tmpMediaPath);
        $this->driverFile->createDirectory($fullTmpMediaPath);
        foreach ($attributesOptionsData as $attributeOptionsData) {
            $swatchVisualFiles = $attributeOptionsData['swatchvisual']['value'] ?? [];
            foreach ($swatchVisualFiles as $index => $swatchVisualFile) {
                $this->driverFile->copy(
                    __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $swatchVisualFile,
                    $fullTmpMediaPath . DIRECTORY_SEPARATOR . $swatchVisualFile
                );
                $newFile = $this->swatchHelper->moveImageFromTmp($swatchVisualFile);
                if (substr($newFile, 0, 1) == '.') {
                    $newFile = substr($newFile, 1); // Fix generating swatch variations for files beginning with ".".
                }
                $this->swatchHelper->generateSwatchVariations($newFile);
                $attributeOptionsData['swatchvisual']['value'][$index] = $newFile;
            }
        }

        // Add attribute options.
        foreach ($attributesOptionsData as $code => $attributeOptionsData) {
            /* @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            $attribute = $this->attributeRepository->get($code);
            $attribute->addData($attributeOptionsData);
            $attribute->save();
        }
    }
}