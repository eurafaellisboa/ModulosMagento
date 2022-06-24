<?php

namespace Blueskytechco\PageBuilderCustom\Block\Widget\StoreUrl;

class Link extends \Magento\Framework\View\Element\Html\Link implements \Magento\Widget\Block\BlockInterface
{

    protected $_href;
    protected $_title;
    protected $_anchorText;
    protected $_storeLink;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Blueskytechco\PageBuilderCustom\Helper\StoreUrl $storeLink,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeLink = $storeLink;
    }

    public function getHref()
    {
        if (!$this->_href) {
            $this->_href = '';
            if ($this->getData('href')) {
                $this->_href = $this->getData('href');
            } elseif ($this->getData('id_path')) {
                $this->_href = $this->_storeLink->getStoreLinkUrl($this->getData('id_path'));
            }
        }

        return $this->_href;
    }

    public function getTitle()
    {
        if (!$this->_title) {
            $this->_title = '';
            if ($this->getData('title') !== null) {
                $this->_title = $this->getData('title');
            } elseif ($this->getData('id_path')) {
                $this->_title = $this->getData('id_path');
            } elseif ($this->getData('href')) {
                $this->_title = $this->getData('href');
            }
        }

        return $this->_title;
    }

    public function getLabel()
    {
        if ($this->getData('anchor_text')) {
            $this->_anchorText = $this->getData('anchor_text');
        } elseif ($this->getData('href')) {
            $this->_anchorText = $this->getData('href');
        } elseif ($this->getData('id_path')) {
            $this->_anchorText = $this->getData('id_path');
        } elseif ($this->getTitle()) {
            $this->_anchorText = $this->getTitle();
        } else {
            $this->_anchorText = $this->getData('href');
        }

        return $this->_anchorText;
    }
}