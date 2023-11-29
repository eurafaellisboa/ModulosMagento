<?php
namespace Digitaria\RemoveHtmlScriptsAdmin\Framework\View\Page;

use Magento\Framework\App;
use Magento\Framework\View;

/**
 * An API for page configuration
 *
 * Has methods for managing properties specific to web pages:
 * - title
 * - related documents, linked static assets in particular
 * - meta info
 * - root element properties
 * - etc...
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 *
 * @api
 */
class Config extends \Magento\Framework\View\Page\Config
{
	
    /**
     * Return empty string instead of miscellaneous scripts/styles
     *
     * @return string
     */
    public function getIncludes()
    {
		 
       $includes = parent::getIncludes();
        $includes = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $includes);
        $includes = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/i', '', $includes);
        return $includes;
    }

}
