<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ShareCart\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
class Pdfgen extends Action
{
    protected $fileFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \FME\ShareCart\Helper\Data $helper,
        FileFactory $fileFactory
    ) {
        
        $this->helper = $helper;
        $this->timezone = $timezone;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $pdf = new \Zend_Pdf();
        $pdf->pages[] = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
        $page = $pdf->pages[0]; // this will get reference to the first page.
        $style = new \Zend_Pdf_Style();
        $style->setLineColor(new \Zend_Pdf_Color_Rgb(0,0,0));
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_TIMES);
        $style->setFont($font,13);
        $page->setStyle($style);
        $width = $page->getWidth();
        $hight = $page->getHeight();
        $x = 30;
        $pageTopalign = 850;
        $this->y = 850 - 250;
        $font1 = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_COURIER_BOLD);
        $style->setFont($font1,15);
        $page->setStyle($style);
        $page->drawText(__("Publister of this Price List :"), $x + 5, $this->y+190, 'UTF-8');
        



        $style->setFont($font,11);
        $page->setStyle($style);
        $page->drawText(__($this->helper->getPdfCompanyName()), $x + 5, $this->y+170, 'UTF-8');
        $page->drawText(__($this->helper->getPdfAddress()), $x + 5, $this->y+150, 'UTF-8');
        $page->drawText(__($this->helper->getPdfEmail()), $x + 5, $this->y+130, 'UTF-8');
        $page->drawText(__($this->helper->getPdfPhone()), $x + 5, $this->y+110, 'UTF-8');
        $page->drawText(__($this->helper->getPdfVAT()), $x + 5, $this->y+90, 'UTF-8');
        $page->drawText(__($this->helper->getPdfReg()), $x + 5, $this->y+70, 'UTF-8');
        
        $date =$this->timezone->date()->format('Y-m-d H:i:s');
        $style->setFont($font1,13);
        $page->setStyle($style);
        $page->drawText(__("Price List made at ".$date), $x + 5, $this->y+40, 'UTF-8');
        

        $stringnet=$this->helper->getPdfWarnMsg();
      
        $str1="";
        $str2="";
        $str3="";
        if(strlen($stringnet)<=100)
        {
            $str1=substr($stringnet,0,100);
        }
        if(strlen($stringnet)>0 && strlen($stringnet)>=100 )
        {
            $str1=substr($stringnet,0,100);
        }
         if(strlen($stringnet)>100 && strlen($stringnet)>=200)
        {
            $str2=substr($stringnet,100,100);
        }
        if(strlen($stringnet)>=200)
        {
            $str3=substr($stringnet,200,100);
        }

        $style->setFont($font,11);
        $page->setStyle($style);
        $page->drawText(__($str1), $x + 5, $this->y+20, 'UTF-8');
       // , Street 1, I-9/3. Islamabad, Pakistan.
       $style->setFont($font,11);
       $page->setStyle($style);
       $page->drawText(__($str2), $x + 5, $this->y, 'UTF-8');
      
       $style->setFont($font,11);
       $page->setStyle($style);
       $page->drawText(__($str3), $x + 5, $this->y-20, 'UTF-8');
      
       $this->y=550;

        $style->setFont($font,11);
        $page->setStyle($style);
        $page->drawText(__("PRODUCT NAME"), $x + 60, $this->y-15, 'UTF-8');
        $page->drawText(__("PRODUCT PRICE"), $x + 250, $this->y-15, 'UTF-8');
        $page->drawText(__("QTY"), $x + 360, $this->y-15, 'UTF-8');
        $page->drawText(__("SUB TOTAL"), $x + 440, $this->y-15, 'UTF-8');

    
        $gap=30;
        //32 fixed page
        //52 per page
        // 3 space for each 
        foreach($this->helper->getCartData() as $item) {
            $val=$this->y-$gap;
            if($val > 40 )
            {
            $style->setFont($font,10);
            $page->setStyle($style);
            $add = 9;
            $page->drawText($item->getPrice(), $x + 260, $this->y-$gap, 'UTF-8');
            $page->drawText($item->getQty(), $x + 380, $this->y-$gap, 'UTF-8');
            $page->drawText($item->getPrice()*$item->getQty(), $x + 470, $this->y-$gap, 'UTF-8');
            $pro = $item->getName();
            $page->drawText($pro, $x + 65, $this->y-$gap, 'UTF-8');
           
            $gap=$gap+15;  
            }
            else
            {
                $page = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
                $pdf->pages[] = $page;
                $this->y=850;
                $gap=70;

            }
        }
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        
        $subTotal = $cart->getQuote()->getSubtotal();
        $grandTotal = $cart->getQuote()->getGrandTotal();
       if($this->y-$gap > 80)
       {
        $gap=$gap+30;
        $style->setFont($font,15);
        $page->setStyle($style);
        $page->drawText(__("Sub Total : %1", $subTotal), $x + 400, $this->y-$gap, 'UTF-8');
        
        $gap=$gap+30;
        $style->setFont($font,15);
        $page->setStyle($style);
        $page->drawText(__("Total : %1", $grandTotal), $x + 400, $this->y-$gap, 'UTF-8');
       }
       else{

        $page = $pdf->newPage(\Zend_Pdf_Page::SIZE_A4);
        $pdf->pages[] = $page;
        $this->y=850;
        $gap=70;
        $style->setFont($font,15);
        $page->setStyle($style);
        $page->drawText(__("Sub Total : %1", $subTotal), $x + 400, $this->y-$gap, 'UTF-8');
        
        $gap=$gap+30;
        $style->setFont($font,15);
        $page->setStyle($style);
        $page->drawText(__("Total : %1", $grandTotal), $x + 400, $this->y-$gap, 'UTF-8');

       }
            
        // $style->setFont($font,10);
        // $page->setStyle($style);
        // $page->drawText(__("Test Footer example"), ($page->getWidth()/2)-50, $this->y-200);

        $fileName = 'productlist.pdf';

        $this->fileFactory->create(
        $fileName,
        $pdf->render(),
        \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR, // this pdf will be saved in var directory with the name meetanshi.pdf
        'application/pdf'
        );
    }
}
