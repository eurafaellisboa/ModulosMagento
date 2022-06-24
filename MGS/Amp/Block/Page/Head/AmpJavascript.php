<?php

namespace MGS\Amp\Block\Page\Head;

class AmpJavascript extends \Magento\Framework\View\Element\Template
{
	/**
	 * Array of scripts
	 * @var array
	 */
	protected $_js = [];

	/**
	 * String of scripts prepared  for output
	 * @var string
	 */
    protected $_html;

    /**
     * Return list of used scripts
     * @var void
     * @return array
     */
    public function getAmpScripts()
    {
        return array_keys($this->_js);
    }

    /**
     * Add new amp js
     * @param string $src
     * @param string $type
     */
	public function addJs($src, $type, $element = null) {
        $type = trim((string)$type);
        $this->_js[$type]['src'] = $src;
        $this->_js[$type]['element'] = $element ? $element : 'element';
        return $this;
	}

    public function removeJs($type)
    {
        if (array_key_exists($type, $this->_js)) {
            unset($this->_js[$type]);
        }

        return $this;
    }

    /**
     * Retrieve prepared string of scripts
     * @return string
     */
    protected function _toHtml()
    {
        $this->_html = '<script async src="https://cdn.ampproject.org/v0.js"></script>';
        foreach ($this->_js as $type => $data) {
            $this->_html .= '<script async ' . ($type ? 'custom-' . $data['element'] . '="' . $type . '"' : '') . ' src="' . $data['src'] . '"></script>
			';
        }

        
        return $this->_html;
    }

}