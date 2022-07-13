<?php

/**

 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)

 * @author Jamacio Rocha <jamacio@iw3.com.br> <@jamacio>

 */

namespace MestreMage\CpfCnpj\Controller\Index;

use Magento\Framework\App\Action\Action;

use Magento\Framework\App\ResponseInterface;

use Magento\Framework\Controller\ResultFactory;

class Index extends Action

{

    /**

     * Dispatch request

     *

     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface

     * @throws \Magento\Framework\Exception\NotFoundException

     */

    public function execute()

    {

        return $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);

    }

}