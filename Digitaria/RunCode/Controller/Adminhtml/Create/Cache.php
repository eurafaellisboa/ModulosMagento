<?php
namespace Digitaria\RunCode\Controller\Adminhtml\Create;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;

class Cache extends Action
{
    protected $cacheManager;
    protected $messageManager;

    public function __construct(
        Action\Context $context,
        CacheManager $cacheManager,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->cacheManager = $cacheManager;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        try {
            $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
            $this->messageManager->addSuccessMessage(__('Cache Limpo com sucesso'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Ocorreu um erro ao limpar o cache: %1', $e->getMessage()));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
