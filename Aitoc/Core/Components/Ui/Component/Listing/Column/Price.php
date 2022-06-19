<?php

namespace Aitoc\Core\Components\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class Price extends Column
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->priceFormatter = $priceFormatter;
        $this->storeManager = $storeManager;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $currencyCode = $this->storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item[$this->getData('name')] !== null) {
                    $item[$this->getData('name')] = $this->priceFormatter->format(
                        $item[$this->getData('name')],
                        false,
                        null,
                        null,
                        $currencyCode
                    );
                }
            }
        }

        return $dataSource;
    }
}
