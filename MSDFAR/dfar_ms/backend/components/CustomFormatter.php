<?php

namespace app\components;

use yii\i18n\Formatter;

class CustomFormatter extends Formatter
{
    public $nullDisplay = 'N/A';

    // Optionally, override the asText method to handle additional cases
    public function asText($value)
    {
        if ($value === null || $value === '') {
            return $this->nullDisplay;
        }
        return parent::asText($value);
    }
}