 <?php
namespace Digitaria\CustomInputCheckout\Block;
//use Magento\Catalog\Block\Category\View;
class Consultoras extends \Magento\Framework\View\Element\Template
{
	
	public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    array $data = []
)
{
    parent::__construct($context, $data);
}
	
    public function getConteudo()
    {
		
		//foreach ($block->getCurrentCategory()->getChildCategories() as $child) {
    		//return $child->getId();
		//}
        return 'Hello World';
    }
}
