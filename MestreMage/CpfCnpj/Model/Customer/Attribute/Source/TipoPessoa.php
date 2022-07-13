<?php


namespace MestreMage\CpfCnpj\Model\Customer\Attribute\Source;

class TipoPessoa extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => '1', 'label' => __('Fisica')],
                ['value' => '2', 'label' => __('Juridica')]
            ];
        }
        return $this->_options;
    }
}
