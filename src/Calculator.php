<?php

namespace InvoiceGenerator;

use NumberFormatter;

class Calculator
{
    public function __construct(array $params, Invoice $invoice)
    {
        foreach ($params['positions'] as &$position) {
            $this->calcPriceAfterDiscount($params, $position);
            $this->calcTaxPrice($params, $position);
            $this->calcNetPrice($params, $position);
            $this->calcGrossPrice($params, $position);
            $this->calcTaxAmount($position);
            $this->calcNetAmount($position);
            $this->calcGrossAmount($position);
        }
        $this->calcTaxSummary($params);
        $this->calcTaxTotal($params);
        $this->calcNetTotal($params);
        $this->calcGrossTotal($params);
        $this->roundValues($params);
        $this->setTotalsInWords($params);

        foreach($params as $key => $value) {
            if(property_exists($invoice, $key)) {
                $invoice->$key = $value;
            }
        }
    }

    private function calcPriceAfterDiscount($params, &$position)
    {
        if(!isset($position['discount']) || $position['discount'] == 0) {
            $position['discount'] = 0;
            $position['discount_price'] = 0;
            $position['discount_amount'] = 0;
            $position['price_after_discount'] = $position['price'];
            return;
        }

        if ($params['discount_type'] === Constrains::DISCOUNT_TYPE_VALUE_PRICE) {
            $position['discount_price'] = $position['discount'];
        }
        if ($params['discount_type'] === Constrains::DISCOUNT_TYPE_VALUE_AMOUNT) {
            $position['discount_price'] = $position['discount'] / $position['quantity'];
        }
        if ($params['discount_type'] === Constrains::DISCOUNT_TYPE_PERCENTAGE_PRICE) {
            $position['discount_price'] = $position['price'] * ($position['discount'] / 100);
        }
        if ($params['discount_type'] === Constrains::DISCOUNT_TYPE_PERCENTAGE_AMOUNT) {
            $position['discount_price'] = ($position['price'] * $position['quantity'] * ($position['discount'] / 100)) / $position['quantity'];
        }
        $position['discount_amount'] = $position['discount_price'] * $position['quantity'];
        $position['price_after_discount'] = $position['price'] - $position['discount_price'];
    }

    private function calcTaxPrice($params, &$position)
    {
        if ($params['invoicing_mode'] === Constrains::INVOICING_MODE_NET) {
            $position['tax_price'] = $position['price_after_discount'] * ($position['tax_rate'] / 100);
        }
        if ($params['invoicing_mode'] === Constrains::INVOICING_MODE_GROSS) {
            $position['tax_price'] = $position['price_after_discount'] - ($position['price_after_discount'] / (($position['tax_rate'] / 100) + 1));
        }
    }

    private function calcNetPrice($params, &$position)
    {
        if ($params['invoicing_mode'] === Constrains::INVOICING_MODE_NET) {
            $position['net_price'] = $position['price_after_discount'];
        }
        if ($params['invoicing_mode'] === Constrains::INVOICING_MODE_GROSS) {
            $position['net_price'] = $position['price_after_discount'] - $position['tax_price'];
        }
    }

    private function calcGrossPrice($params, &$position)
    {
        if ($params['invoicing_mode'] === Constrains::INVOICING_MODE_NET) {
            $position['gross_price'] = $position['price_after_discount'] + $position['tax_price'];
        }
        if ($params['invoicing_mode'] === Constrains::INVOICING_MODE_GROSS) {
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

    private function calcTaxSummary(&$params)
    {
        $params['tax_summary'] = [];
        foreach ($params['positions'] as &$position) {
            if (!isset($params['tax_summary'][$position['tax_rate']])) {
                $params['tax_summary'][$position['tax_rate']] = 0;
            }
            $params['tax_summary'][$position['tax_rate']] += $position['tax_amount'];
        }
        foreach($params['tax_summary'] as $rate => $value) {
            $params['tax_summary'][$rate] = round($value, 2, PHP_ROUND_HALF_UP);
        }
    }

    private function calcTaxTotal(&$params)
    {
        $params['tax_total'] = 0;
        foreach($params['tax_summary'] as $value) {
            $params['tax_total'] += $value;
        }
    }

    private function calcNetTotal(&$params)
    {
        $params['net_total'] = 0;
        foreach ($params['positions'] as &$position) {
            $params['net_total'] += $position['net_amount'];
        }
    }

    private function calcGrossTotal(&$params)
    {
        $params['gross_total'] = $params['tax_total'] + $params['net_total'];
    }

    private function roundValues(&$params)
    {
        foreach ($params['positions'] as &$position) {
            $position['discount_price'] = round($position['discount_price'], 2, PHP_ROUND_HALF_UP);
            $position['discount_amount'] = round($position['discount_amount'], 2, PHP_ROUND_HALF_UP);
            $position['net_price'] = round($position['net_price'], 2, PHP_ROUND_HALF_UP);
            $position['net_amount'] = round($position['net_amount'], 2, PHP_ROUND_HALF_UP);
            $position['gross_price'] = round($position['gross_price'], 2, PHP_ROUND_HALF_UP);
            $position['gross_amount'] = round($position['gross_amount'], 2, PHP_ROUND_HALF_UP);
            $position['tax_price'] = round($position['tax_price'], 2, PHP_ROUND_HALF_UP);
            $position['tax_amount'] = round($position['tax_amount'], 2, PHP_ROUND_HALF_UP);
        }
        $params['net_total'] = round($params['net_total'], 2, PHP_ROUND_HALF_UP);
        $params['gross_total'] = round($params['gross_total'], 2, PHP_ROUND_HALF_UP);
    }

    private function setTotalsInWords(&$params)
    {
        $formatter = new NumberFormatter($params['language'], NumberFormatter::SPELLOUT);
        foreach(['net_total', 'gross_total', 'tax_total'] as $key) {
            $value = number_format($params[$key], 2, '.', '');
            $integers = explode('.', $value)[0];
            $decimals = explode('.', $value)[1];
            $unit = Constrains::CURRENCY_LIST[$params['currency']]['Unit'];
            $subunit = Constrains::CURRENCY_LIST[$params['currency']]['Subunit'];
            $result = $formatter->format($integers) . ' ' . $unit . ' ' . $formatter->format($decimals) . ' ' . $subunit;
            $params[$key.'_in_words'] = $result;
        }
    }
}