<?php

namespace Blueskytechco\Themeoption\Model\Config;

class Validatetransparent extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        $v = $this->getValue();
        if ($v == 'rgba(0, 0, 0, 0)')
        {
            $this->setValue('transparent');
        }
        return $this;
    }
}
