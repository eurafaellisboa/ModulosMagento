<?php
namespace Digitaria\RunCode\Controller\Adminhtml\Create;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Shell;
use Magento\Framework\Shell\CommandRenderer;
use Magento\Framework\Process\PhpExecutableFinderFactory;

class Crowd extends Action
{
    // Atributos
    protected $cacheManager;
    protected $indexerRegistry;
    protected $messageManager;
    protected $_shell;
    protected $phpExecutableFinder;

    // Método construtor
    public function __construct(
        Action\Context $context,
        CacheManager $cacheManager,
        IndexerRegistry $indexerRegistry,
        ManagerInterface $messageManager,
        CommandRenderer $CommandRenderer,
        PhpExecutableFinderFactory $phpExecutableFinderFactory,
        Shell $shell
    ) {
        parent::__construct($context);
        $this->cacheManager = $cacheManager;
        $this->indexerRegistry = $indexerRegistry;
        $this->messageManager = $messageManager;
        $this->CommandRenderer = $CommandRenderer;
        $this->_shell = $shell;
        $this->phpExecutableFinder = $phpExecutableFinderFactory->create();
    }

    // Método de execução do controller
    public function execute()
    {
        try {
            // Define o tempo máximo de execução do script em segundos
            ini_set('max_execution_time', 300); // 300 segundos = 5 minutos

            // Encontra o caminho do executável do PHP
            $phpPath = $this->phpExecutableFinder->find() ?: 'php';

            // Define o caminho do executável do Magento
            $magentoPath = BP . '/bin/magento';

            // Executa o comando para iniciar o consumidor de filas do Magento
            $this->_shell->execute("$phpPath $magentoPath queue:consumers:start product_action_attribute.update --max-messages=2000");

            // Exibe mensagem de sucesso ao final da execução
            $this->messageManager->addSuccessMessage(__('Peças atualizadas com sucesso'));
            
        } catch (\Exception $e) {
            // Em caso de erro, exibe mensagem de erro ao final da execução
            $this->messageManager->addErrorMessage(__('Ocorreu um erro ao atualizar as peças: %1', $e->getMessage()));
        }

        // Redireciona para a página anterior
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setRefererOrBaseUrl();
    }
} 
