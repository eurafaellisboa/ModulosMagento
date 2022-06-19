<?php
/**
* 
* Core para Magento 2
* 
* @category     Dholi
* @package      Modulo Core
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\Core\Block\Adminhtml\System\Config;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\View\Helper\Js;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\ResourceConnection;

class Info extends \Magento\Config\Block\System\Config\Form\Fieldset {

	private $cronFactory;

	private $directoryList;

	private $resourceConnection;

	private $productMetadata;

	private $reader;

	protected $fieldRenderer;

	public function __construct(Context $context,
	                            Session $authSession,
	                            Js $jsHelper,
	                            CollectionFactory $cronFactory,
	                            DirectoryList $directoryList,
	                            Reader $reader,
	                            ResourceConnection $resourceConnection,
	                            ProductMetadataInterface $productMetadata,
	                            array $data = []) {
		parent::__construct($context, $authSession, $jsHelper, $data);

		$this->cronFactory = $cronFactory;
		$this->directoryList = $directoryList;
		$this->resourceConnection = $resourceConnection;
		$this->productMetadata = $productMetadata;
		$this->reader = $reader;
	}

	public function render(AbstractElement $element) {
		$html = $this->_getHeaderHtml($element);

		$html .= $this->getMagentoMode($element);
		$html .= $this->getMagentoPathInfo($element);
		$html .= $this->getSystemTime($element);

		$html .= $this->_getFooterHtml($element);

		return $html;
	}

	private function getFieldRenderer() {
		if (empty($this->fieldRenderer)) {
			$this->fieldRenderer = $this->_layout->createBlock(\Magento\Config\Block\System\Config\Form\Field::class);
		}

		return $this->fieldRenderer;
	}

	private function getMagentoMode($fieldset) {
		$label = __('Magento Mode');

		$env = $this->reader->load();
		$mode = isset($env[State::PARAM_MODE]) ? $env[State::PARAM_MODE] : '';

		return $this->getFieldHtml($fieldset, 'magento_mode', $label, ucfirst($mode));
	}

	private function getMagentoPathInfo($fieldset) {
		$label = __('Magento Path');
		$path = $this->directoryList->getRoot();

		return $this->getFieldHtml($fieldset, 'magento_path', $label, $path);
	}

	private function getSystemTime($fieldset) {
		if (version_compare($this->productMetadata->getVersion(), '2.2', '>=')) {
			$time = $this->resourceConnection->getConnection()->fetchOne('select now()');
		} else {
			$time = $this->_localeDate->date()->format('H:i:s');
		}
		return $this->getFieldHtml($fieldset, 'mysql_current_date_time', __('Current Time'), $time);
	}

	protected function getFieldHtml($fieldset, $fieldName, $label = '', $value = '') {
		$field = $fieldset->addField($fieldName, 'label', [
			'name' => 'dummy',
			'label' => $label,
			'after_element_html' => $value,
		])->setRenderer($this->getFieldRenderer());

		return $field->toHtml();
	}
}
