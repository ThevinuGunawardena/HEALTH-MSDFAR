<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "depature_boat_payment".
 *
 * @property int $id
 * @property int $boat_reg_id
 * @property float $amount
 * @property string|null $from_month
 * @property string|null $to_date
 * @property int $added_by
 * @property string|null $file
 * @property string|null $affidavit_file
 * @property int $Affidavit_status
 * @property string|null $reference_code
 */
class DepatureBoatPayment extends ActiveRecord
{
    /**
     * UploadedFile object for the normal payment attachment.
     *
     * The database `file` column must contain only the filename string.
     */
    public $fileUpload;

    /**
     * UploadedFile object for the affidavit attachment.
     *
     * The database `affidavit_file` column must contain only the filename.
     */
    public $affidavitFileUpload;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'depature_boat_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            /*
             * Default database values.
             */
            [
                [
                    'from_month',
                    'to_date',
                    'file',
                    'affidavit_file',
                    'reference_code',
                ],
                'default',
                'value' => null,
            ],

            [
                ['Affidavit_status'],
                'default',
                'value' => 0,
            ],

            /*
             * Required database values.
             */
            [
                [
                    'boat_reg_id',
                    'amount',
                    'added_by',
                ],
                'required',
            ],

            /*
             * Integer fields.
             */
            [
                [
                    'boat_reg_id',
                    'added_by',
                    'Affidavit_status',
                ],
                'integer',
            ],

            /*
             * Payment amount validation.
             */
            [
                ['amount'],
                'number',
                'min' => 6000,
                'tooSmall' => Yii::t(
                    'app',
                    'Amount must be at least 6,000.'
                ),
            ],

            [
                ['amount'],
                'validatePaymentAmount',
            ],

            /*
             * Date fields.
             *
             * The controller converts YYYY-MM into YYYY-MM-01.
             */
            [
                [
                    'from_month',
                    'to_date',
                ],
                'date',
                'format' => 'php:Y-m-d',
            ],

            /*
             * Database filename columns.
             */
            [
                [
                    'file',
                    'affidavit_file',
                ],
                'string',
                'max' => 255,
            ],

            /*
             * Reference code.
             */
            [
                ['reference_code'],
                'string',
                'max' => 255,
            ],

            /*
             * Uploaded file validation.
             */
            [
                [
                    'fileUpload',
                    'affidavitFileUpload',
                ],
                'file',
                'extensions' => [
                    'jpg',
                    'jpeg',
                    'png',
                    'pdf',
                ],
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'application/pdf',
                ],
                'checkExtensionByMimeType' => true,
                'skipOnEmpty' => true,
                'maxSize' => 5 * 1024 * 1024,
                'tooBig' => Yii::t(
                    'app',
                    'The uploaded file cannot be larger than 5 MB.'
                ),
                'wrongExtension' => Yii::t(
                    'app',
                    'Only JPG, JPEG, PNG, and PDF files are allowed.'
                ),
            ],
        ];
    }

    /**
     * Validate that the amount is a multiple of 6,000.
     *
     * @param string $attribute
     * @param mixed $params
     */
    public function validatePaymentAmount($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        $amount = (float) $this->$attribute;

        if (fmod($amount, 6000) !== 0.0) {
            $this->addError(
                $attribute,
                Yii::t(
                    'app',
                    'Amount must be a multiple of 6,000.'
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),

            'boat_reg_id' => Yii::t(
                'app',
                'Boat Registration'
            ),

            'amount' => Yii::t(
                'app',
                'Amount'
            ),

            'from_month' => Yii::t(
                'app',
                'Applicable From'
            ),

            'to_date' => Yii::t(
                'app',
                'Applicable Until'
            ),

            'added_by' => Yii::t(
                'app',
                'Added By'
            ),

            'file' => Yii::t(
                'app',
                'Payment File'
            ),

            'fileUpload' => Yii::t(
                'app',
                'Payment File'
            ),

            'affidavit_file' => Yii::t(
                'app',
                'Affidavit File'
            ),

            'affidavitFileUpload' => Yii::t(
                'app',
                'Affidavit File'
            ),

            'Affidavit_status' => Yii::t(
                'app',
                'Affidavit Status'
            ),

            'reference_code' => Yii::t(
                'app',
                'Reference Code'
            ),
        ];
    }
}