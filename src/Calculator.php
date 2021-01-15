<?php

namespace InvoiceGenerator;

use NumberFormatter;

class Calculator
{
    public function __construct(array $params, ?array &$data)
    {
        $data = $params;

        foreach ($data['positions'] as &$position) {
            $this->calcPriceAfterDiscount($data, $position);
            $this->calcTaxPrice($data, $position);
            $this->calcNetPrice($data, $position);
            $this->calcGrossPrice($data, $position);
            $this->calcTaxAmount($position);
            $this->calcNetAmount($position);
            $this->calcGrossAmount($position);
        }
        $this->calcTaxSummary($data);
        $this->calcTaxTotal($data);
        $this->calcNetTotal($data);
        $this->calcGrossTotal($data);
        $this->roundValues($data);
        $this->setTotalsInWords($data);
    }

    private function calcPriceAfterDiscount($data, &$position)
    {
        if(!isset($position['discount']) || $position['discount'] == 0) {
            $position['discount'] = 0;
            $position['discount_price'] = 0;
            $position['discount_amount'] = 0;
            $position['price_after_discount'] = $position['price'];
            return;
        }

        if ($data['discount_type'] === Constrains::DISCOUNT_TYPE_VALUE_PRICE) {
            $position['discount_price'] = $position['discount'];
        }
        if ($data['discount_type'] === Constrains::DISCOUNT_TYPE_VALUE_AMOUNT) {
            $position['discount_price'] = $position['discount'] / $position['quantity'];
        }
        if ($data['discount_type'] === Constrains::DISCOUNT_TYPE_PERCENTAGE_PRICE) {
            $position['discount_price'] = $position['price'] * ($position['discount'] / 100);
        }
        if ($data['discount_type'] === Constrains::DISCOUNT_TYPE_PERCENTAGE_AMOUNT) {
            $position['discount_price'] = ($position['price'] * $position['quantity'] * ($position['discount'] / 100)) / $position['quantity'];
        }
        $position['discount_amount'] = $position['discount_price'] * $position['quantity'];
        $position['price_after_discount'] = $position['price'] - $position['discount_price'];
    }

    private function calcTaxPrice($data, &$position)
    {
        if ($data['invoicing_mode'] === Constrains::INVOICING_MODE_NET) {
            $position['tax_price'] = $position['price_after_discount'] * ($position['tax_rate'] / 100);
        }
        if ($data['invoicing_mode'] === Constrains::INVOICING_MODE_GROSS) {
            $position['tax_price'] = $position['price_after_discount'] - ($position['price_after_discount'] / (($position['tax_rate'] / 100) + 1));
        }
    }

    private function calcNetPrice($data, &$position)
    {
        if ($data['invoicing_mode'] === Constrains::INVOICING_MODE_NET) {
            $position['net_price'] = $position['price_after_discount'];
        }
        if ($data['invoicing_mode'] === Constrains::INVOICING_MODE_GROSS) {
            $position['net_price'] = $position['price_after_discount'] - $position['tax_price'];
        }
    }

    private function calcGrossPrice($data, &$position)
    {
        if ($data['invoicing_mode'] === Constrains::INVOICING_MODE_NET) {
            $position['gross_price'] = $position['price_after_discount'] + $position['tax_price'];
        }
        if ($data['invoicing_mode'] === Constrains::INVOICING_MODE_GROSS) {
            $position['gross_price'] = $position['price_after_discount'];
        }
    }

    private function calcTaxAmount(&$position)
    {
        $position['tax_amount'] = $position['tax_price'] * $position['quantity'];
    }

    private function calcNetAmount(&$position)
    {
        $position['net_amount'] = $position['net_price'] * $position['quantity'];
    }

    private function calcGrossAmount(&$position)
    {
        $position['gross_amount'] = $position['gross_price'] * $position['quantity'];
    }

    private function calcTaxSummary(&$data)
    {
        $data['tax_summary'] = [];
        foreach ($data['positions'] as &$position) {
            if (!isset($data['tax_summary'][$position['tax_rate']])) {
                $data['tax_summary'][$position['tax_rate']] = 0;
            }
            $data['tax_summary'][$position['tax_rate']] += $position['tax_amount'];
        }
        foreach($data['tax_summary'] as $rate => $value) {
            $data['tax_summary'][$rate] = round($value, 2, PHP_ROUND_HALF_UP);
        }
    }

    private function calcTaxTotal(&$data)
    {
        $data['tax_total'] = 0;
        foreach($data['tax_summary'] as $value) {
            $data['tax_total'] += $value;
        }
    }

    private function calcNetTotal(&$data)
    {
        $data['net_total'] = 0;
        foreach ($data['positions'] as &$position) {
            $data['net_total'] += $position['net_amount'];
        }
    }

    private function calcGrossTotal(&$data)
    {
        $data['gross_total'] = $data['tax_total'] + $data['net_total'];
    }

    private function roundValues(&$data)
    {
        foreach ($data['positions'] as &$position) {
            $position['discount_price'] = round($position['discount_price'], 2, PHP_ROUND_HALF_UP);
            $position['discount_amount'] = round($position['discount_amount'], 2, PHP_ROUND_HALF_UP);
            $position['net_price'] = round($position['net_price'], 2, PHP_ROUND_HALF_UP);
            $position['net_amount'] = round($position['net_amount'], 2, PHP_ROUND_HALF_UP);
            $position['gross_price'] = round($position['gross_price'], 2, PHP_ROUND_HALF_UP);
            $position['gross_amount'] = round($position['gross_amount'], 2, PHP_ROUND_HALF_UP);
            $position['tax_price'] = round($position['tax_price'], 2, PHP_ROUND_HALF_UP);
            $position['tax_amount'] = round($position['tax_amount'], 2, PHP_ROUND_HALF_UP);
        }
        $data['net_total'] = round($data['net_total'], 2, PHP_ROUND_HALF_UP);
        $data['gross_total'] = round($data['gross_total'], 2, PHP_ROUND_HALF_UP);
    }

    private function setTotalsInWords(&$data)
    {
        $formatter = new NumberFormatter($data['language'], NumberFormatter::SPELLOUT);
        foreach(['net_total', 'gross_total', 'tax_total'] as $key) {
            $value = number_format($data[$key], 2, '.', '');
            $integers = explode('.', $value)[0];
            $decimals = explode('.', $value)[1];
            $unit = Constrains::CURRENCY_LIST[$data['currency']]['Unit'];
            $subunit = Constrains::CURRENCY_LIST[$data['currency']]['Subunit'];
            $result = $formatter->format($integers) . ' ' . $unit . ' ' . $formatter->format($decimals) . ' ' . $subunit;
            $data[$key.'_in_words'] = $result;
        }
    }
}