<?php

namespace Magento\Parcelamento\Block\ListProd;
class ListProd extends \Magento\Catalog\Block\Product\ListProduct
{
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());

        if ($renderer) {
            $textoFinal = '';
            if($this->getconfPanel('parcelamento/geral/ativarmodulo')) {
                $renderer->setProduct($product);
                $textosemjuros = $this->getconfPanel('parcelamento/loja/textosemjuros');
                $textocomjuros = $this->getconfPanel('parcelamento/loja/textocomjuros');
                $valorminimo = $this->getconfPanel('parcelamento/loja/valorminimoparcela');
                $finalPreco = $product->getFinalPrice();
                $parcelas = $this->getconfPanel('parcelamento/loja/valormaximomeses');
                $juros = $this->getconfPanel('parcelamento/loja/valorjuros');
                $percentual = ($juros * $parcelas) / 100.0;
                $valor_final = $finalPreco + ($percentual * $finalPreco) / $parcelas;
                $txtSenTratamento = $this->getconfPanel('parcelamento/loja/padraodotexto');
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