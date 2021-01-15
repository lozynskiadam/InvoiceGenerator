<?php

namespace InvoiceGenerator;

class Constrains
{
    const INVOICE_TYPE_BASIC = 'basic';
    const INVOICE_TYPE_CORRECTION = 'correction';
    const INVOICE_TYPE_ADVANCE = 'advance';
    const INVOICE_TYPE_LIST = [
        self::INVOICE_TYPE_BASIC,
        self::INVOICE_TYPE_CORRECTION,
        self::INVOICE_TYPE_ADVANCE,
    ];

    const LANGUAGE_ENGLISH = 'en';
    const LANGUAGE_POLISH = 'pl';
    const LANGUAGE_LIST = [
        self::LANGUAGE_ENGLISH,
        self::LANGUAGE_POLISH,
    ];

    const CURRENCY_UNITED_STATES_DOLLAR = [
        'Label' => 'USD',
        'Symbol' => '$',
        'Unit' => 'dollars',
        'Subunit' => 'cents',
    ];
    const CURRENCY_EURO = [
        'Label' => 'EUR',
        'Symbol' => '€',
        'Unit' => 'euro',
        'Subunit' => 'cents',
    ];
    const CURRENCY_POUND_STERLING = [
        'Label' => 'GBP',
        'Symbol' => '£',
        'Unit' => 'pounds',
        'Subunit' => 'pence',
    ];
    const CURRENCY_POLISH_ZLOTY = [
        'Label' => 'PLN',
        'Symbol' => null,
        'Unit' => 'złotych',
        'Subunit' => 'groszy',
    ];
    const CURRENCY_LIST = [
        self::CURRENCY_UNITED_STATES_DOLLAR['Label'] => self::CURRENCY_UNITED_STATES_DOLLAR,
        self::CURRENCY_POLISH_ZLOTY['Label'] => self::CURRENCY_POLISH_ZLOTY,
        self::CURRENCY_EURO['Label'] => self::CURRENCY_EURO,
        self::CURRENCY_POUND_STERLING['Label'] => self::CURRENCY_POUND_STERLING,
    ];

    const INVOICING_MODE_NET = 'net';
    const INVOICING_MODE_GROSS = 'gross';
    const INVOICING_MODE_LIST = [
        self::INVOICING_MODE_NET,
        self::INVOICING_MODE_GROSS,
    ];

    const DISCOUNT_TYPE_VALUE_PRICE = 'VP';
    const DISCOUNT_TYPE_VALUE_AMOUNT = 'VA';
    const DISCOUNT_TYPE_PERCENTAGE_PRICE = 'PP';
    const DISCOUNT_TYPE_PERCENTAGE_AMOUNT = 'PA';
    const DISCOUNT_TYPE_LIST = [
        self::DISCOUNT_TYPE_VALUE_PRICE,
        self::DISCOUNT_TYPE_VALUE_AMOUNT,
        self::DISCOUNT_TYPE_PERCENTAGE_PRICE,
        self::DISCOUNT_TYPE_PERCENTAGE_AMOUNT
    ];

    const POSITIONS_COLUMN_POSITION_NUMBER = 'position_number';
    const POSITIONS_COLUMN_NAME = 'name';
    const POSITIONS_COLUMN_UNIT = 'unit';
    const POSITIONS_COLUMN_QUANTITY = 'quantity';
    const POSITIONS_COLUMN_DISCOUNT = 'discount';
    const POSITIONS_COLUMN_DISCOUNT_PRICE = 'discount_price';
    const POSITIONS_COLUMN_DISCOUNT_AMOUNT = 'discount_amount';
    const POSITIONS_COLUMN_TAX_RATE = 'tax_rate';
    const POSITIONS_COLUMN_TAX_PRICE = 'tax_price';
    const POSITIONS_COLUMN_TAX_AMOUNT = 'tax_amount';
    const POSITIONS_COLUMN_NET_PRICE = 'net_price';
    const POSITIONS_COLUMN_NET_AMOUNT = 'net_amount';
    const POSITIONS_COLUMN_GROSS_PRICE = 'gross_price';
    const POSITIONS_COLUMN_GROSS_AMOUNT = 'gross_amount';
    const POSITIONS_COLUMN_LIST = [
        self::POSITIONS_COLUMN_POSITION_NUMBER,
        self::POSITIONS_COLUMN_NAME,
        self::POSITIONS_COLUMN_UNIT,
        self::POSITIONS_COLUMN_QUANTITY,
        self::POSITIONS_COLUMN_DISCOUNT,
        self::POSITIONS_COLUMN_DISCOUNT_PRICE,
        self::POSITIONS_COLUMN_DISCOUNT_AMOUNT,
        self::POSITIONS_COLUMN_TAX_RATE,
        self::POSITIONS_COLUMN_TAX_PRICE,
        self::POSITIONS_COLUMN_TAX_AMOUNT,
        self::POSITIONS_COLUMN_NET_PRICE,
        self::POSITIONS_COLUMN_NET_AMOUNT,
        self::POSITIONS_COLUMN_GROSS_PRICE,
        self::POSITIONS_COLUMN_GROSS_AMOUNT
    ];
}