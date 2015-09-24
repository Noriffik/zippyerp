<?php

namespace ZippyERP\ERP\Entity\Doc;

use \ZippyERP\System\System;
use \ZippyERP\ERP\Entity\Item;
use \ZippyERP\ERP\Entity\SubConto;
use \ZippyERP\ERP\Entity\Entry;
use \ZippyERP\ERP\Helper as H;

/**
 * Класс-сущность  локумент акт  о  выполненных работах
 * сторонней организацией
 *
 */
class ServiceIncome extends Document
{

    public function generateReport()
    {


        $i = 1;
        $total = 0;
        $detail = array();
        foreach ($this->detaildata as $value) {
            $detail[] = array("no" => $i++,
                "itemname" => $value['itemname'],
                "measure" => $value['measure_name'],
                "quantity" => $value['quantity']/ 1000,
                "price" => H::fm($value['price']),
                "pricends" => H::fm($value['pricends']),
                "amount" => H::fm(($value['quantity']/1000) * $value['price'])
            );
            $total += ($value['quantity']/1000) * $value['price'];
        }

        $header = array('date' => date('d.m.Y', $this->document_date),
            "customer" => $this->headerdata["customername"],
            "document_number" => $this->document_number,
            "nds" => H::fm($this->headerdata["nds"]),
            "totalnds" => H::fm($this->headerdata["totalnds"]),
            "total" => H::fm($this->headerdata["total"])
        );


        $report = new \ZippyERP\ERP\Report('serviceincome.tpl');

        $html = $report->generate($header, $detail);

        return $html;
    }

    public function Execute()
    {


        $total = $this->headerdata['total'];
        $customer_id = $this->headerdata["customer"];


        if ($this->headerdata['cash'] == true) {

            $cash = MoneyFund::getCash();
            Entry::AddEntry("63", "30", $total, $this->document_id, $this->document_date);
            $sc = new SubConto($this->document_id, $this->document_date, 63);
            $sc->setCustomer($this->headerdata["customer"]);
            $sc->setAmount( $total);
            $sc->save();
            $sc = new SubConto($this->document_id, $this->document_date, 30);
            $sc->setMoneyfund($cash->id);
            $sc->setAmount($total);
            // $sc->save();
        }

        if ($this->headerdata['totalnds'] > 0) {
            $total = $total - $this->headerdata['totalnds'];
            Entry::AddEntry("644", "63", $this->headerdata['totalnds'], $this->document_id, 0, $customer_id);
            $sc = new SubConto($this->document_id, $this->document_date, 63);
            $sc->setCustomer($customer_id);
            $sc->setAmount(0 - $this->headerdata['totalnds']);
            $sc->save();
            $sc = new SubConto($this->document_id, $this->document_date, 644);
            $sc->setExtCode(TAX_NDS);
            $sc->setAmount($this->headerdata['totalnds']);
            //$sc->save();
        }


        Entry::AddEntry("91", "63", $total, $this->document_id, $this->document_date);
        $sc = new SubConto($this->document_id, $this->document_date, 63);
        $sc->setCustomer($customer_id);
        $sc->setAmount(0 - $value);
        $sc->save();


        return true;
    }

    public function getRelationBased()
    {
        $list = array();
        $list['TaxInvoiceIncome'] = 'Входящая НН';

        return $list;
    }

}