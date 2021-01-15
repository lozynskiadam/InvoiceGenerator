<?php

namespace InvoiceGenerator;

class Invoice
{
    private $Params;
    private $Data;

    /**
     * @param $params
     * @throws InvoiceException
     */
    public function __construct(array $params)
    {
        $this->Params = array_merge($this->getDefaultParams(), $params);
        new Validator($this->Params, null);
        new Calculator($this->Params, $this->Data);
    }

    /**
     * @param array $config
     * @throws InvoiceException
     */
    public function pdf(array $config = [])
    {
        $config = array_merge($this->getDefaultConfig(), $config);
        new Validator(null, $config);
        new Generator($this->Data, $config);
    }

    private function getDefaultParams()
    {
        return [
            'language' => Constrains::LANGUAGE_ENGLISH,
            'type' => Constrains::INVOICE_TYPE_BASIC,
            'invoicing_mode' => Constrains::INVOICING_MODE_NET,
            'issue_date' => date('Y-m-d'),
            'currency' => Constrains::CURRENCY_POUND_STERLING['Label'],
            'discount_type' => Constrains::DISCOUNT_TYPE_VALUE_PRICE,
            'payment_amount' => 0,
            'annotation' => '',
            'additional' => []
        ];
    }

    private function getDefaultConfig()
    {
        $defaultConfig = [
            'templates_path' => __DIR__ . '/templates/',
            'template' => 'default.twig',
            'output' => '',
            'columns' => []
        ];
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_POSITION_NUMBER;
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_NAME;
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_UNIT;
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_QUANTITY;
        foreach($this->Params['positions'] as $position) {
            if(isset($position['discount']) && $position['discount'] > 0) {
                $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_DISCOUNT;
                break;
            }
        }
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_TAX_RATE;
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_NET_PRICE;
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_NET_AMOUNT;
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_GROSS_AMOUNT;

        return $defaultConfig;
    }

}