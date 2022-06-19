<?php

namespace MestreMage\Core\Block\Adminhtml\System\Config;

class Extensions extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<iframe id="mestremage_store" width="100%" src="https://www.modulomagento.com.br/coreModulos.php?id=' . uniqid() .'" ></iframe>';
    }
}
