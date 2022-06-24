<?php 
namespace Blueskytechco\PageBuilderCustom\Plugin\Framework\Data;
 
class Collection
{
    public function aroundAddItem(\Magento\Framework\Data\Collection $subject, \Closure $process, \Magento\Framework\DataObject $dataObject)
    {
        try{
            return $process($dataObject);
        }catch ( \Exception $e){
            return $this;
        }
    }
}