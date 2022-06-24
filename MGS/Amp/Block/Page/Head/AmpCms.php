<?php

namespace MGS\Amp\Block\Page\Head;

class AmpCms extends AmpAbstract
{
	/**
	 * Retrieve additional data
	 * @return array
	 */
    public function getAmpParams()
    {
        $params = parent::getAmpParams();
        return array_merge($params, [
            'type' => 'website',
            'url' => $this->_configHelper->getCanonicalUrl(
                $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true])
            ),
        ]);
    }
}
