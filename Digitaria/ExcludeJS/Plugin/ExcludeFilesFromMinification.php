<?php namespace Digitaria\ExcludeJS\Plugin;
use Magento\Framework\View\Asset\Minification;
 
class ExcludeFilesFromMinification
{
    public function aroundGetExcludes(Minification $subject, callable $proceed, $contentType)
    {
        $result = $proceed($contentType);
        if ($contentType != 'js') {
            return $result;
        }
        $result[] = 'https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js'; // for e.g https://www.google.com/recaptcha/api.js'
        return $result;
    }
}