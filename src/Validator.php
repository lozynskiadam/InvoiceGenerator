<?php

namespace InvoiceGenerator;

use DateTime;

class Validator
{
    private $params;
    private $config;

    /**
     * Validator constructor.
     * @param array $params
     * @param array $config
     * @throws InvoiceException
     */
    public function __construct(?array $params, ?array $config)
    {
        if($this->params = $params) {
            Validator::validateParamsLanguage();
            Validator::validateParamsInvoiceType();
            Validator::validateParamsInvoicingMode();
            Validator::validateParamsPaymentMethod();
            Validator::validateParamsCurrency();
            Validator::validateParamsDiscountType();
            Validator::validateParamsDates();
            Validator::validateParamsContractors();
            Validator::validateParamsAdditionalParams();
            Validator::validateParamsAnnotation();
            Validator::validateParamsPositions();
        }
        if($this->config = $config) {
            Validator::validateConfigTemplateFile();
            Validator::validateConfigColumns();
        }
    }

    private function validateParamsLanguage()
    {
        if (!in_array($this->params['language'], Constrains::LANGUAGE_LIST)) {
            throw new InvoiceException('invalid language');
        }
    }

    private function validateParamsInvoiceType()
    {
        if (!in_array($this->params['type'], Constrains::INVOICE_TYPE_LIST)) {
            throw new InvoiceException('invalid invoice type');
        }
    }

    private function validateParamsInvoicingMode()
    {
        if (!in_array($this->params['invoicing_mode'], Constrains::INVOICING_MODE_LIST)) {
            throw new InvoiceException('invalid invoicing mode');
        }
    }

    private function validateParamsPaymentMethod()
    {
        if (!isset($this->params['payment_method']) || !is_string($this->params['payment_method'])) {
            throw new InvoiceException('invalid payment method');
        }
    }

    private function validateParamsCurrency()
    {
        if (!key_exists($this->params['currency'], Constrains::CURRENCY_LIST)) {
            throw new InvoiceException('invalid currency');
        }
    }

    private function validateParamsDiscountType()
    {
        if (!in_array($this->params['discount_type'], Constrains::DISCOUNT_TYPE_LIST)) {
            throw new InvoiceException('invalid discount type');
        }
    }

    private function validateParamsDates()
    {
        if (!isset($this->params['issue_date'])) {
            throw new InvoiceException('undefined issue_date');
        }
        if (!isset($this->params['payment_due_date'])) {
            throw new InvoiceException('undefined payment_due_date');
        }
        if (!Validator::isValidDateFormat($this->params['issue_date'])) {
            throw new InvoiceException('invalid issue_date format');
        }
        if (!Validator::isValidDateFormat($this->params['payment_due_date'])) {
            throw new InvoiceException('invalid payment_due_date format');
        }
        if (isset($this->params['sale_date']) && !Validator::isValidDateFormat($this->params['sale_date'])) {
            throw new InvoiceException('invalid sale_date format');
        }
        if (isset($this->params['delivery_date']) && !Validator::isValidDateFormat($this->params['delivery_date'])) {
            throw new InvoiceException('invalid delivery_date format');
        }
    }

    private function validateParamsContractors()
    {
        if (!isset($this->params['buyer'])) {
            throw new InvoiceException('undefined buyer');
        }
        if (!isset($this->params['seller'])) {
            throw new InvoiceException('undefined seller');
        }
        if (!Validator::isImplementingContractorStructure($this->params['buyer'])) {
            throw new InvoiceException('invalid buyer structure');
        }
        if (!Validator::isImplementingContractorStructure($this->params['seller'])) {
            throw new InvoiceException('invalid seller structure');
        }
        if (isset($this->params['payer']) && !Validator::isImplementingContractorStructure($this->params['payer'])) {
            throw new InvoiceException('invalid payer structure');
        }
        if (isset($this->params['recipient']) && !Validator::isImplementingContractorStructure($this->params['recipient'])) {
            throw new InvoiceException('invalid recipient structure');
        }
    }

    private function validateParamsAdditionalParams()
    {
        if (!isset($this->params['additional'])) {
            return;
        }
        if (!is_array($this->params['additional'])) {
            throw new InvoiceException('additional must be type array');
        }
        foreach ($this->params['additional'] as $field) {
            if (!is_string($field)) {
                throw new InvoiceException('additional element must be type string');
            }
        }
    }

    private function validateParamsAnnotation()
    {
        if (!is_string($this->params['annotation'])) {
            throw new InvoiceException('invalid annotation structure');
        }
    }

    private function validateParamsPositions()
    {
        if (!isset($this->params['positions'])) {
            throw new InvoiceException('undefined positions');
        }
        if (!is_array($this->params['positions'])) {
            throw new InvoiceException('positions must be type array');
        }
        if (empty($this->params['positions'])) {
            throw new InvoiceException('positions can not be empty');
        }
        foreach ($this->params['positions'] as $ordinal => $position) {
            if (!Validator::isValidPositionName($position)) {
                throw new InvoiceException("position #$ordinal - invalid position name");
            }
            if (!Validator::isValidPositionUnit($position)) {
                throw new InvoiceException("position #$ordinal - invalid unit");
            }
            if (!Validator::isValidPositionQuantity($position)) {
                throw new InvoiceException("position #$ordinal - invalid quantity");
            }
            if (!Validator::isValidPositionPrice($position)) {
                throw new InvoiceException("position #$ordinal - invalid price");
            }
            if (!Validator::isValidPositionTaxRate($position)) {
                throw new InvoiceException("position #$ordinal - invalid tax rate");
            }
            if (!Validator::isValidPositionDiscount($position)) {
                throw new InvoiceException("position #$ordinal - invalid discount");
            }
        }
    }

    private function isValidDateFormat($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function isImplementingContractorStructure($contractor)
    {
        if (!isset($contractor['name'])) {
            return false;
        }
        if (!isset($contractor['address'])) {
            return false;
        }
        if (!isset($contractor['city'])) {
            return false;
        }
        if (!isset($contractor['zip_code'])) {
            return false;
        }
        if (!isset($contractor['tax_id'])) {
            return false;
        }
        return true;
    }

    private function isValidPositionName($position)
    {
        if (!isset($position['name'])) {
            return false;
        }
        if (empty($position['name'])) {
            return false;
        }
        if (!is_string($position['name'])) {
            return false;
        }
        return true;
    }

    private function isValidPositionUnit($position)
    {
        if (!isset($position['unit'])) {
            return false;
        }
        if (empty($position['unit'])) {
            return false;
        }
        if (!is_string($position['unit'])) {
            return false;
        }
        return true;
    }

    private function isValidPositionQuantity($position)
    {
        if (!isset($position['quantity'])) {
            return false;
        }
        if (!is_numeric($position['quantity'])) {
            return false;
        }
        if ($position['quantity'] < 0) {
            return false;
        }
        return true;
    }

    private function isValidPositionPrice($position)
    {
        if (!isset($position['price'])) {
            return false;
        }
        if (!is_numeric($position['price'])) {
            return false;
        }
        if ($position['price'] < 0) {
            return false;
        }
        if (isset(explode(".", $position['price'])[1]) && strlen(explode(".", $position['price'])[1]) > 2) {
            return false;
        }
        return true;
    }

    private function isValidPositionTaxRate($position)
    {
        if (!isset($position['tax_rate'])) {
            return false;
        }
        if (!is_numeric($position['tax_rate'])) {
            return false;
        }
        if ($position['tax_rate'] > 100) {
            return false;
        }
        if ($position['tax_rate'] < 0) {
            return false;
        }
        return true;
    }

    private function isValidPositionDiscount($position)
    {
        if (!isset($position['discount'])) {
            return true;
        }
        if (!is_numeric($position['discount'])) {
            return false;
        }
        if ($position['discount'] < 0) {
            return false;
        }
        if (in_array($this->params['discount_type'], [Constrains::DISCOUNT_TYPE_PERCENTAGE_PRICE, Constrains::DISCOUNT_TYPE_PERCENTAGE_AMOUNT]) && $position['discount'] > 100) {
            return false;
        }
        if ($this->params['discount_type'] == Constrains::DISCOUNT_TYPE_VALUE_PRICE && $position['discount'] > $position['price']) {
            return false;
        }
        if ($this->params['discount_type'] == Constrains::DISCOUNT_TYPE_VALUE_AMOUNT && $position['discount'] > $position['price']/$position['quantity']) {
            return false;
        }
        return true;
    }

    private function validateConfigTemplateFile()
    {
        if(!file_exists($this->config['templates_path'] . $this->config['template'])) {
            throw new InvoiceException('template file not exists');
        }
    }

    private function validateConfigColumns()
    {
        foreach($this->config['columns'] as $column) {
            if(!in_array($column, Constrains::POSITIONS_COLUMN_LIST)) {
                $exc = sprintf('invalid columns config - column "%s" is not a valid column', $column);
                throw new InvoiceException($exc);
            }
        }
    }

}