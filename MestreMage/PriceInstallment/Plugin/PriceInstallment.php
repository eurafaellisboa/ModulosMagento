<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */
namespace MestreMage\PriceInstallment\Plugin;


class PriceInstallment
{

    var $_repeatcount = 0;
    function aroundToHtml($subject, callable $proceed) {
        try {
            $json = '';
            $txt_valor_vista = '';
            $script = '';
            if ($this->getconfPanel('priceInstallment/geral/ativarmodulo')) {
                $finalPreco = $subject->getSaleableItem()->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $parcelasfix = $this->getconfPanel('priceInstallment/loja/valormaximomeses');
                $juros = $this->getconfPanel('priceInstallment/loja/valorjuros');
                $valorminimo = $this->getconfPanel('priceInstallment/loja/valorminimoparcela');
                $mostrartabelaparcela = $this->getconfPanel('priceInstallment/loja/mostrartabelaparcela');
                $jurosparcela = $this->getconfPanel('priceInstallment/loja/jurosapartirdaparcela');
                $desconto = $this->getconfPanel('priceInstallment/loja/desconto');
                $valordesconto = $this->getconfPanel('priceInstallment/loja/valordesconto');
                $frase = $this->getconfPanel('priceInstallment/loja/frase');
				$frases = '<span class="menor"> à vista no boleto ou PIX</span>';
                $valor_a_vista_ativarmodulo = $this->getconfPanel('priceInstallment/loja/valor_a_vista_ativarmodulo');
                $textosemjuros = __($this->getconfPanel('priceInstallment/loja/textosemjuros'));
                $textocomjuros = __($this->getconfPanel('priceInstallment/loja/textocomjuros'));
                $type_interest = $this->getconfPanel('priceInstallment/loja/type_interest');
                $padraodotextonatabela = $this->getconfPanel('priceInstallment/loja/padraodotextonatabela');
                $titulotabela = $this->getconfPanel('priceInstallment/loja/titulotabela');
                $precoavistanatabela = $this->getconfPanel('priceInstallment/loja/precoavistanatabela');
                $textoprecoavista = __($this->getconfPanel('priceInstallment/loja/textoprecoavista'));
                if($subject->getSaleableItem()->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $finalPreco = $objectManager->get('Magento\Catalog\Model\Product')->load($subject->getSaleableItem()->getId())->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
                }

                $txtPadrao = $this->getconfPanel('priceInstallment/loja/padraodotexto');
                $valor = $finalPreco;
                $parcelas = 1;
                $final = '';
                for ($x = 1;$x <= $parcelasfix;$x++){
                    $final = ($valor / $x);
                    if($final > (int)$valorminimo) {
                        $parcelas =  $x;
                    }
                }

                    if ($valor_a_vista_ativarmodulo) {
                        if ($desconto == 1) {
                            $final = ($finalPreco - (float)$valordesconto);
                        } elseif ($desconto == 2) {
                            $percentual = (float)$valordesconto / 100.0;
                            $final = $finalPreco - ($percentual * $finalPreco);
                        }
						
						if ($subject->getZone() == 'item_view') { // pagina interna do produto
                        $txt_valor_vista = '<div id="preco-a-vista-mm"><p>' . str_replace("{valor}", $this->formatarValor($final), $frase) . '</p></div>';
						} else {
						$txt_valor_vista = '<div id="preco-a-vista-mm-grid"><p>' . str_replace("{valor}", $this->formatarValor($final), $frase) . '</p></div>';	
						}
                    }
                    if ($subject->getZone() == 'item_view') { // pagina interna do produto

                        if (!$this->_repeatcount) {
                            $this->_repeatcount++;
                            $script .= '<div class="jsr-priceinstallment"  style="list-style:none" >';
                            if ($jurosparcela > $parcelas) {
                                $script .= '<p class="mm-price-parcels-view">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, 0, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p>';
								//$script .= '<div id="preco-a-vista-mm"><p>' . str_replace("{valor}", $this->formatarValor($final), $frase) . '</p></div>';
                            } else {
                                $script .= '<p class="mm-price-parcels-view">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, $juros, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p>';
								//$script .= '<div id="preco-a-vista-mm"><p>' . str_replace("{valor}", $this->formatarValor($final), $frase) . '</p></div>';
                            }
                            $script .= '</div>';
                            $json .= '"valorDesconto": "' . $valordesconto .
                                '","tipoDesconto": "' . $desconto .
                                '","jurosParcela": "' . $jurosparcela .
                                '","juros": "' . $juros .
                                '","fraseAvista": "' . $frase .
                                '","parcelasFix": "' . $parcelasfix .
                                '","parcelasModificada": "' . $parcelas .
                                '","mostrarTabelaParcela": "' . $mostrartabelaparcela .
                                '","padraodotextonatabela": "' . $padraodotextonatabela .
                                '","tituloTabela": "' . $titulotabela .
                                '","precoAvistaTabela": "' . $precoavistanatabela .
                                '","textoPrecoAvista": "' . $textoprecoavista .
                                '","txtPadrao": "' . $txtPadrao .
                                '","valorMinimo": "' . $valorminimo .
                                '","textoSemJuros": "' . $textosemjuros .
                                '","textoComJuros": "' . $textocomjuros .
                                '","typeInterest": "' . $type_interest .
                                '","valorAvistaAtivarmodulo": "' . $valor_a_vista_ativarmodulo .
                                '","lblParcel": "' . __('parcel') .
                                '","lblPrice": "' . __('price') .
                                '"';
                            $json = "<script> var MestreMage_PriceInstallment = JSON.parse('{" . $json . "}'); </script>";
                            $script .= $txt_valor_vista;
                            if($mostrartabelaparcela){
                                $script .= $this->montarTabela($finalPreco, $juros, $parcelas,$final);
                            }
                        }
                    } else {
                        if ($jurosparcela > $parcelas) {
                            $script .= '<p class="mm-price-parcels-grid">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, 0, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p>';
							
                        }else{
                            $script .= '<p class="mm-price-parcels-grid">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, $juros, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p>';
							
                        }

                        $script .= $txt_valor_vista;
						
                    }

                if(!$this->getconfPanel('priceInstallment/loja/mostrarparcelaunica')){
                    //if($parcelas <= 1){
                        //return $proceed();
                    //}
                }
            }

            return $proceed().$script.$json;
        } catch (Exception $e) {
            $this->log($e->getMessage());
            return $proceed();
        }
    }

    public function montarTabela($valor,$juros,$parcela,$final = null){
        $jurosparcela = $this->getconfPanel('priceInstallment/loja/jurosapartirdaparcela');
        $padraodotextonatabela = $this->getconfPanel('priceInstallment/loja/padraodotextonatabela');
        $titulotabela = $this->getconfPanel('priceInstallment/loja/titulotabela');
        $precoavistanatabela = $this->getconfPanel('priceInstallment/loja/precoavistanatabela');
        $textoprecoavista = $this->getconfPanel('priceInstallment/loja/textoprecoavista');

        $valor_a_vista_ativarmodulo = $this->getconfPanel('priceInstallment/loja/valor_a_vista_ativarmodulo');
        $desconto = $this->getconfPanel('priceInstallment/loja/desconto');
        $finalPreco = $valor;
        $juros = $this->getconfPanel('priceInstallment/loja/valorjuros');
        $valordesconto = $this->getconfPanel('priceInstallment/loja/valordesconto');

        $html = '<div id="mestre-magento-table">';
        $html .= '<h3 id="abre-parcelas">'.__($titulotabela).'</h3>';
        $html .= '<table id="parcelas" style="display:none">';
        $html .= '<tr><th>'.__('parcel').'</th><th>'.__('price').'</th></tr>';

        if ($valor_a_vista_ativarmodulo) {
            if ($desconto == 1) {
                $final = ($finalPreco - (float)$valordesconto);
            } elseif ($desconto == 2) {
                $percentual = (float)$valordesconto / 100.0;
                $final = $finalPreco - ($percentual * $finalPreco);
            }

            if($precoavistanatabela)
                $html .= '<tr><td>'.__($textoprecoavista).'</td><td>'.$this->formatarValor($final).'</td></tr>';

        }

        for($i= 1; $i <= $parcela; $i++) {

            if($jurosparcela > $i)
                $html .= '<tr><td>'.str_replace("{parcel}", $i, $padraodotextonatabela).'</td><td> '.$this->aplicarPorcentagem($valor,0,$i).'</tr></td>';
            else
                $html .= '<tr><td>'.str_replace("{parcel}", $i, $padraodotextonatabela).'</td><td> '.$this->aplicarPorcentagem($valor,$juros,$i).'</tr></td>';
        }
        $html .= '</table> </div>';
        return $html;
    }

    public function jurosSimples($valor, $taxa, $parcelas) {
        if($parcelas > 1) {

            $valParcela = ((float)$valor + (float)$taxa) / (int)$parcelas;

        }else{
            $valParcela = $valor;
        }
        return $valParcela;
    }

    public function jurosComposto($valor,$juros,$parcela) {
        if($parcela > 1) {
            if ($juros) {
                $taxa = (float)$juros / 100;
                $valor_final = ($valor * $taxa) / (1 - (pow(1 / (1 + $taxa), $parcela)));

                $valor_final = explode('.',$valor_final);
                if(isset($valor_final[1])){
                    $final = $valor_final[0].'.'.substr($valor_final[1], 0, 2);
                    $valor_final = (floatval($final));
                }else{
                    $valor_final = $valor_final / $parcela;
                }


            } else {
                $valor_final = $valor / $parcela;
            }
        }else{
            $valor_final = $valor;
        }

        return $valor_final;
    }

    public function aplicarPorcentagem($valor,$juros,$parcela){
        if($this->getconfPanel('priceInstallment/loja/type_interest') == 'compound') {
            $valor_final = $this->jurosComposto($valor, $juros, $parcela);
        }else{
            $valor_final = $this->jurosSimples($valor, $juros, $parcela);
        }
        $textoFinal = '';
        $textosemjuros = __($this->getconfPanel('priceInstallment/loja/textosemjuros'));
        $textocomjuros = __($this->getconfPanel('priceInstallment/loja/textocomjuros'));

        if ($juros){
            $textoFinal .= ' ' . $textocomjuros;
        }else{
            $textoFinal .= ' ' . $textosemjuros;
        }
        return $this->formatarValor($valor_final).$textoFinal;
    }

    public function formatarValor($valor){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
        return $priceHelper->currency(str_replace(',','.',$valor), true, false);
    }

    public function verificarPag(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
        return $request->getFullActionName();
    }

    public function getconfPanel($valor){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, $storeScope);
    }

    public function log($valor){
        if($this->getconfPanel('priceInstallment/geral/log')) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/priceInstallment.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Price Installment: ".$valor);
        }
    }
}





