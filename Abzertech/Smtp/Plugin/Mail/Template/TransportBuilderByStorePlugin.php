<?php

namespace Abzertech\Smtp\Plugin\Mail\Template;

class TransportBuilderByStorePlugin
{

    /**
     * @var storeModel
     */
    protected $storeModel;

    /**
     * @var senderResolver
     */
    private $senderResolver;

    /**
     * TransportBuilderByStorePlugin constructor.
     *
     * @param Store $storeModel
     * @param SenderResolverInterface $senderResolver
     */
    public function __construct(
        \Abzertech\Smtp\Model\Store $storeModel,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
    ) {
        $this->storeModel = $storeModel;
        $this->senderResolver = $senderResolver;
    }

    /**
     * Before Set From By Store
     *
     * @param  TransportBuilderByStore $element
     * @return string
     */
    public function beforeSetFromByStore(
        \Magento\Framework\Mail\Template\TransportBuilderByStore $subject,
        $from,
        $store
    ) {
        if (!$this->storeModel->getStoreId()) {
            $this->storeModel->setStoreId($store);
        }

        $email = $this->senderResolver->resolve($from, $store);
        $this->storeModel->setFrom($email);

        return [$from, $store];
    }
}
