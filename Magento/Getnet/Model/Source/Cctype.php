<?php

namespace Magento\Getnet\Model\Source;

class CcType implements \Magento\Framework\Option\ArrayInterface
{
    const VISA = 'VI';
    const MASTERCARD = 'MC';
    const AMEX = 'AE';
    const ELO = 'EL';
    const AURA = 'AU';
    const JCB = 'JCB';
    const DINERS = 'DN';
    const DISCOVER = 'DI';

    const MASTERCARD_NAME = 'Mastercard';
    const VISA_NAME = 'Visa';
    const AMEX_NAME = 'Amex';
    const ELO_NAME = 'Elo';
    const AURA_NAME = 'Aura';
    const JCB_NAME = 'JCB';
    const DINERS_NAME = 'Diners';
    const DISCOVER_NAME = 'Discover';

    public function toOptionArray()
    {
        return [
            [
                'value' => self::VISA,
                'label' => self::VISA_NAME
            ],
            [
                'value' =>  self::MASTERCARD,
                'label' =>  self::MASTERCARD_NAME
            ],
            [
                'value' =>  self::AMEX,
                'label' =>  self::AMEX_NAME
            ],
            [
                'value' =>  self::ELO,
                'label' =>  self::ELO_NAME
            ],
            [
                'value' =>  self::AURA,
                'label' =>  self::AURA_NAME
            ],
            [
                'value' =>  self::JCB,
                'label' =>  self::JCB_NAME
            ],
            [
                'value' =>  self::DINERS,
                'label' =>  self::DINERS_NAME
            ],
            [
                'value' =>  self::DISCOVER,
                'label' =>  self::DISCOVER_NAME
            ]
        ];
    }
}
