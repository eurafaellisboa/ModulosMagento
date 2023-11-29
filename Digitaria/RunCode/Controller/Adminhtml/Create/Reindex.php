<?php
namespace Digitaria\RunCode\Controller\Adminhtml\Create;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Indexer\Model\IndexerFactory;
use Magento\Indexer\Model\Indexer\CollectionFactory;

class Reindex extends Action
{
    protected $messageManager;
    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $_indexerFactory;
    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    protected $_indexerCollectionFactory;
      

    public function __construct(
        Action\Context $context,
        ManagerInterface $messageManager,
        IndexerFactory $indexerFactory,
        CollectionFactory $indexerCollectionFactory
    ) {
        parent::__construct($context);
        $this->messageManager = $messageManager;
        $this->_indexerFactory = $indexerFactory;
        $this->_indexerCollectionFactory = $indexerCollectionFactory;
    }

    public function execute()
    {
        try {
            /* Regenerate indexes for all indexers */
            $indexerCollection = $this->_indexerCollectionFactory->create();
            $ids = $indexerCollection->getAllIds();
            foreach ($ids as $id) {
                $idx = $this->_indexerFactory->create()->load($id);
                $idx->reindexAll($id); // this reindexes all
            }
       

            // Exibe mensagem de sucesso
            $this->messageManager->addSuccessMessage(__('Índices reindexados com sucesso'));
        } catch (\Exception $e) {
            // Exibe mensagem de erro com detalhes da exceção
            $this->messageManager->addErrorMessage(__('Ocorreu um erro ao reindexar os índices: %1', $e->getMessage()));
        }

        /** @var Redirect $resultRedirect **/
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
