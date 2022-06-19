<?php

namespace Abzertech\Smtp\Plugin\Mail\Template;

use Magento\Framework\Mail\Template\TransportBuilder;

class TransportBuilderPlugin
{

    /**
     * @var storeModel
     */
    protected $storeModel;

    /**
     * Version constructor.
     *
     * @param Store $storeModel
     */
    public function __construct(\Abzertech\Smtp\Model\Store $storeModel)
    {
        $this->storeModel = $storeModel;
    }

    /**
     * @param TransportBuilder $subject
     * @param $templateOptions
     * @return array
     */
    public function beforeSetTemplateOptions(TransportBuilder $subject, $templateOptions)
    {
        if (array_key_exists('store', $templateOptions)) {
            $this->storeModel->setStoreId($templateOptions['store']);
        } else {
            $this->storeModel->setStoreId(null);
        }

        return [$templateOptions];
    }
}
