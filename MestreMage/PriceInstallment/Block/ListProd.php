<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */

namespace MestreMage\PriceInstallment\Block\ListProd;
class ListProd extends \Magento\Catalog\Block\Product\ListProduct
{
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());

        if ($renderer) {
            $textoFinal = '';
            if($this->getconfPanel('priceInstallment/geral/ativarmodulo')) {
                $renderer->setProduct($product);
                $textosemjuros = $this->getconfPanel('priceInstallment/loja/textosemjuros');
                $textocomjuros = $this->getconfPanel('priceInstallment/loja/textocomjuros');
                $valorminimo = $this->getconfPanel('priceInstallment/loja/valorminimoparcela');
                $finalPreco = $product->getFinalPrice();
                $parcelas = $this->getconfPanel('priceInstallment/loja/valormaximomeses');
                $juros = $this->getconfPanel('priceInstallment/loja/valorjuros');
                $percentual = ($juros * $parcelas) / 100.0;
                $valor_final = $finalPreco + ($percentual * $finalPreco) / $parcelas;
                $txtSenTratamento = $this->getconfPanel('priceInstallment/loja/padraodotexto');
                $textoFinal = str_replace("{parcelas}", $parcelas, $txtSenTratamento);
                $textoFinal = str_replace("{preco}", $this->formatarValor($valor_final), $textoFinal);

                if ($juros)
                    $textoFinal .= ' ' . $textocomjuros;
                else
                    $textoFinal .= ' ' . $textosemjuros;

                if ($valor_final < $valorminimo)
                    $textoFinal = '';
            }

            return $textoFinal.$renderer->toHtml();
        }
        return '';
    }
    public function formatarValor($valor){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');

        return $priceHelper->currency($valor, true, false);
    }
    public function getconfPanel($valor){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, $storeScope);
    }



}