<?php

namespace ZippyERP\ERP\Entity\Doc;

use ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  документ гарантийного талон
 *
 */
class Warranty extends Document
{

    public function generateReport() {

        $detail = array();
        $total = 0;
        foreach ($this->detaildata as $value) {
            $detail[] = array(
                "tovar_name" => $value['itemname'],
                "quantity" => $value['quantity'] / 1000,
                "price" => H::fm($value['price']),
                "amount" => H::fm(($value['quantity'] / 1000) * $value['price']),
                "sn" => $value['sn'],
                "warranty" => $value['warranty']
            );
            $total += $value['quantity'] * $value['price'] / 100;
        }

        $firm = \ZippyERP\System\System::getOptions("firmdetail");


        $header = array('date' => date('d.m.Y', $this->document_date),
            "firmname" => $firm['name'],
            "customer" => strlen($this->headerdata["customer"]) > 0 ? $this->headerdata["customer"] : '',
            "document_number" => $this->document_number
        );

        $report = new \ZippyERP\ERP\Report('warranty.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute() {

        return true;
    }

}
