<?php
/**
 * Class Image
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AjaxInfiniteScroll
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AjaxInfiniteScroll\Model\Config\Backend;

/**
 * Class Image
 *
 * @category Sparsh
 * @package  Sparsh_AjaxInfiniteScroll
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Image extends \Magento\Config\Model\Config\Backend\Image
{
    /**
     * UPLOAD_DIR
     */
    const UPLOAD_DIR = 'sparsh/ajax_infinite_scroll'; // Folder save image

    /**
     * @return string
     */
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * @return bool
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * @return array|string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
