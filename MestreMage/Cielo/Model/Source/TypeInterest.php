<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */
namespace MestreMage\Cielo\Model\Source;

use Magento\Backend\App\Action;

class TypeInterest implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            'simple' => __('Simple Interest'),
            'compound' => __('Compound Interest'),
        ];
    }
}