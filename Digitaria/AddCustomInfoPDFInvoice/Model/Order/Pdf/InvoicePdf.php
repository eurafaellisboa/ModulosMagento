<?php
namespace Digitaria\AddCustomInfoPDFInvoice\Model\Order\Pdf;
use \Magento\Sales\Model\Order\Pdf\Invoice;
class InvoicePdf extends Invoice
{
    /**
     * We only need to override the getPdf of Invoice,
     *  most of this method is copied directly from parent class
     *
     * @param array $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = []) {
    $this->_beforeGetPdf(); // Execute qualquer configuração necessária antes de gerar o PDF
    $this->_initRenderer('invoice');
    $pdf = new \Zend_Pdf();
    $this->_setPdf($pdf);
    $style = new \Zend_Pdf_Style();
    $this->_setFontBold($style, 10);

    foreach ($invoices as $invoice) {
        if ($invoice->getStoreId()) {
            $this->_localeResolver->emulate($invoice->getStoreId());
            $this->_storeManager->setCurrentStore($invoice->getStoreId());
        }
        $page = $this->newPage();
        
        // Inserir a caixa de aviso no topo
        $this->drawNotice($page, $invoice);

        $order = $invoice->getOrder();
        /* Add image */
        $this->insertLogo($page, $invoice->getStore());
        /* Add address */
        $this->insertAddress($page, $invoice->getStore());
        /* Add head */
        $this->insertOrder(
            $page,
            $order,
            $this->_scopeConfig->isSetFlag(
                self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            )
        );
        /* Add document text and number */
        $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
        /* Add table */
        $this->_drawHeader($page);
        /* Add body */
        foreach ($invoice->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }
            /* Draw item */
            $this->_drawItem($item, $page, $order);
            $page = end($pdf->pages);
        }
        /* Add totals */
        $this->insertTotals($page, $invoice);
        if ($invoice->getStoreId()) {
            $this->_localeResolver->revert();
        }
    }

    $this->_afterGetPdf();
    return $pdf;
}

    /**
 * draw notice below content
 *
 * @param \Zend_Pdf_Page $page
 * @param \Magento\Sales\Model\Order\Invoice $invoice
 */
    protected function drawNotice(\Zend_Pdf_Page $page, \Magento\Sales\Model\Order\Invoice $invoice) {
        $iFontSize = 10;     // font size
        $iColumnWidth = 280; // whole page width
        $iWidthBorder = 0; // half page width
        $order = $invoice->getOrder();
        // Texto da data de nascimento formatado
        $customerDob = $this->formatCustomerDob($order->getCustomerDob());
        $sNotice = 'Data de Nascimento: ' . $customerDob . ' | Email: ' . $order->getCustomerEmail();
        $iXCoordinateText = 30;
        $sEncoding = 'UTF-8';
        $this->y -= 10; // move down on page
        try {
            $oFont = $this->_setFontRegular($page, $iFontSize);
            $iXCoordinateText = $this->getAlignCenter($sNotice, $iXCoordinateText, $iColumnWidth, $oFont, $iFontSize);  // center text coordinate
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(1, 0, 0));                                             // red lines
            $iXCoordinateBorder = $iXCoordinateText - 10;                                                               // border is wider than text
            $this->y -= 15;                                                                                             // further down
            $page->drawText($sNotice, $iXCoordinateText, $this->y, $sEncoding);
            $this->y -= 10; // further down
            $this->y -= 10;
        } catch (\Exception $exception) {
            // handle
        }
    }

    /**
    * Formatar a data de nascimento no estilo "25 de setembro de 2023"
    *
    * @param string $customerDob
    * @return string
    */
    protected function formatCustomerDob($customerDob) {
        // Converter a data de nascimento em um formato legível
        $dobDate = date('d/m/Y', strtotime($customerDob));
        return $dobDate;
    }

    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 770, $this->y - 15);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Products'), 'feed' => 35];

        $lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];

        // custom column

        $lines[0][] = ['text' => __('Qty'), 'feed' => 435, 'align' => 'right'];

        $lines[0][] = ['text' => __('Price'), 'feed' => 360, 'align' => 'right'];

        $lines[0][] = ['text' => __('Tax'), 'feed' => 495, 'align' => 'right'];

        $lines[0][] = ['text' => __('Subtotal'), 'feed' => 565, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 5];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }
}