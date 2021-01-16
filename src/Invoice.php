<?php

namespace InvoiceGenerator;

class Invoice
{
    /** @var string */
    public $language;
    /** @var string */
    public $type;
    /** @var string */
    public $invoicing_mode;
    /** @var string */
    public $issue_date;
    /** @var string */
    public $currency;
    /** @var string */
    public $discount_type;
    /** @var string */
    public $annotation;
    /** @var array */
    public $additional;
    /** @var string */
    public $payment_due_date;
    /** @var string */
    public $payment_method;
    /** @var array */
    public $seller;
    /** @var array */
    public $buyer;
    /** @var array */
    public $positions;
    /** @var string */
    public $signature;
    /** @var array */
    public $tax_summary;
    /** @var string */
    public $tax_total;
    /** @var string */
    public $net_total;
    /** @var string */
    public $gross_total;
    /** @var string */
    public $net_total_in_words;
    /** @var string */
    public $gross_total_in_words;
    /** @var string */
    public $tax_total_in_words;

    /**
     * @param $params
     * @throws InvoiceException
     */
    public function __construct(array $params)
    {
        $params = array_merge($this->getDefaultParams(), $params);
        new Validator($params, null);
        new Calculator($params, $this);
    }

    /**
     * @param array $config
     * @throws InvoiceException
     */
    public function pdf(array $config = [])
    {
        $config = array_merge($this->getDefaultConfig(), $config);
        new Validator(null, $config);
        new Generator($this, $config);
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
        foreach($this->positions as $position) if($position['discount'] > 0) {
            $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_DISCOUNT;
            break;
        }
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_TAX_RATE;
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_NET_PRICE;
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_NET_AMOUNT;
        $defaultConfig['columns'][] = Constrains::POSITIONS_COLUMN_GROSS_AMOUNT;

        return $defaultConfig;
    }

}