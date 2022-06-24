<?php

namespace Blueskytechco\Themeoption\Model\Config;

class Newsletterlogo extends \Magento\Config\Model\Config\Backend\Image
{
    
    const UPLOAD_DIR = 'blueskytechco/newsletter/logo';

    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'png'];
    }
}
