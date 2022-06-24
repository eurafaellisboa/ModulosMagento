<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\UrlInput;

use Magento\Framework\UrlInterface;

class StoreUrl implements \Magento\Ui\Model\UrlInput\ConfigInterface
{

    public function getConfig(): array
    {
        return [
            'label' => __('Store URL'),
            'component' => 'Magento_Ui/js/form/element/abstract',
            'template' => 'ui/form/element/input',
            'sortOrder' => 100,
        ];
    }
}
