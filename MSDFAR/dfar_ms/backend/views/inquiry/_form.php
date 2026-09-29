<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Inquiry $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCss(<<<CSS
.full-page-container {
    display: flex;
    justify-content: center;
    width: 100%;
    padding: 20px;
    box-sizing: border-box;
}

.inquiry-form {
    width: 100%;
    max-width: 1000px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background: #fff;
    border-radius: 8px;
}

.upload-file-information {
    margin-top: 6px;
    font-size: 13px;
    color: #6c757d;
}

.upload-client-error {
    display: none;
    margin-top: 6px;
    color: #dc3545;
    font-size: 14px;
}

@media (max-width: 576px) {
    .full-page-container {
        padding: 10px;
    }

    .inquiry-form {
        padding: 15px;
    }
}
CSS
);

$uploadInputId = Html::getInputId($model, 'uploadedFile');
$uploadErrorId = 'uploaded-file-client-error';
$uploadInfoId = 'uploaded-file-information';
$formId = 'inquiry-form';
?>

<div class="full-page-container">
    <div class="inquiry-form">
        <?php $form = ActiveForm::begin([
            'id' => $formId,
            'options' => [
                'enctype' => 'multipart/form-data',

                // File uploads should not be submitted through PJAX.
                'data-pjax' => 0,
            ],
        ]); ?>

        <?= $form->field($model, 'Name')->textInput([
            'maxlength' => true,
            'title' => 'Name can only contain letters, spaces, and dashes.',
            'required' => true,
            'autocomplete' => 'name',
        ])->label('Name / නම / பெயர்') ?>

        <?= $form->field($model, 'phone_number')->textInput([
            'maxlength' => 16,
            'pattern' => '\+?[0-9]{10,15}',
            'title' => 'Please enter a valid phone number containing 10 to 15 digits.',
            'required' => true,
            'inputmode' => 'tel',
            'autocomplete' => 'tel',
        ])->label('Phone number / දුරකථන අංකය / தொலைபேசி எண்') ?>

        <?= $form->field($model, 'District')->dropDownList(
            ArrayHelper::map(
                \backend\models\MFiDistrict::find()
                    ->orderBy(['name' => SORT_ASC])
                    ->all(),
                'id',
                'name'
            ),
            [
                'prompt' => 'Select District',
                'required' => true,
            ]
        )->label('District / දිස්ත්රික්කය / மாவட்டம்') ?>

        <?= $form->field($model, 'Office')->textInput([
            'maxlength' => true,
            'title' => 'Enter your office name.',
            'required' => true,
            'autocomplete' => 'organization',
        ])->label(
            'Office Name (Division) / කාර්යාලයේ නම (කොට්ඨාශය) / அலுவலக பெயர் (பிரிவு)'
        ) ?>

        <?= $form->field($model, 'Email')->textInput([
            'maxlength' => true,
            'type' => 'email',
            'title' => 'Please enter a valid email address.',
            'required' => true,
            'autocomplete' => 'email',
        ])->label('Email Address / විද්‍යුත් තැපෑල / மின்னஞ்சல் முகவரி') ?>

        <?= $form->field($model, 'Inquiry_Type')->dropDownList([
            'Internet connectivity' => 'Internet connectivity',
            'System login failure' => 'System login failure',
            'System process issues' => 'System process issues',
            'Report issues' => 'Report issues',
            'Devices not working' => 'Devices not working',
            'SIM card not working' => 'SIM card not working',
            'Document upload' => 'Document upload',
            'Forgot Password' => 'Forgot Password',
            'Other issues' => 'Other issues',
        ], [
            'prompt' => 'Select Inquiry Type',
            'required' => true,
        ])->label('Inquiry Type / විමර්ශන වර්ගය / விசாரணை வகை') ?>

        <?= $form->field($model, 'Description')->textarea([
            'rows' => 6,
            'maxlength' => true,
        ])->label('Description / විස්තරය / விளக்கம்') ?>

        <?= $form->field($model, 'uploadedFile')->fileInput([
            'id' => $uploadInputId,
            'class' => 'form-control-file',
            'accept' => implode(',', [
                '.pdf',
                '.doc',
                '.docx',
                '.jpg',
                '.jpeg',
                '.png',
                '.mp3',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
                'audio/mpeg',
            ]),
        ])->hint(
            'Allowed files: PDF, DOC, DOCX, JPG, JPEG, PNG and MP3. Maximum size: 10 MB.'
        ) ?>

        <div
            id="<?= Html::encode($uploadErrorId) ?>"
            class="upload-client-error"
            role="alert"
            aria-live="polite"
        ></div>

        <div
            id="<?= Html::encode($uploadInfoId) ?>"
            class="upload-file-information"
            aria-live="polite"
        ></div>

        <?php if (
            !Yii::$app->user->isGuest &&
            in_array((int) Yii::$app->user->identity->type, [15], true)
        ): ?>
            <?= $form->field($model, 'Inquiry_Status')->dropDownList([
                'In Progress' => 'In Progress',
                'Completed' => 'Completed',
            ], [
                'prompt' => 'Select Status',
            ])->label(
                'Inquiry Status / විමර්ශන තත්ත්වය / விசாரணை நிலை'
            ) ?>

            <?= $form->field($model, 'remarks')->textarea([
                'rows' => 6,
                'maxlength' => true,
            ])->label('Remarks / අදහස් / கருத்துக்கள்') ?>
        <?php endif; ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Save'), [
                'class' => 'btn btn-success',
                'id' => 'inquiry-submit-button',
            ]) ?>

            <?= Html::resetButton(Yii::t('app', 'Reset'), [
                'class' => 'btn btn-secondary',
                'style' => 'margin-left: 10px;',
                'id' => 'inquiry-reset-button',
            ]) ?>
        </div>

        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="alert alert-danger">
                <?= Html::encode(
                    Yii::$app->session->getFlash('error')
                ) ?>
            </div>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php

$uploadInputIdJson = Json::htmlEncode($uploadInputId);
$uploadErrorIdJson = Json::htmlEncode($uploadErrorId);
$uploadInfoIdJson = Json::htmlEncode($uploadInfoId);
$formIdJson = Json::htmlEncode($formId);

$fileValidationScript = <<<JS
(function () {
    'use strict';

    const MAX_FILE_SIZE = 10 * 1024 * 1024;

    const ALLOWED_EXTENSIONS = [
        'pdf',
        'doc',
        'docx',
        'jpg',
        'jpeg',
        'png',
        'mp3'
    ];

    const ALLOWED_MIME_TYPES = {
        pdf: [
            'application/pdf'
        ],
        doc: [
            'application/msword',
            'application/octet-stream'
        ],
        docx: [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/octet-stream'
        ],
        jpg: [
            'image/jpeg'
        ],
        jpeg: [
            'image/jpeg'
        ],
        png: [
            'image/png'
        ],
        mp3: [
            'audio/mpeg',
            'audio/mp3',
            'audio/x-mpeg',
            'application/octet-stream'
        ]
    };

    const input = document.getElementById($uploadInputIdJson);
    const errorBox = document.getElementById($uploadErrorIdJson);
    const informationBox = document.getElementById($uploadInfoIdJson);
    const form = document.getElementById($formIdJson);

    let fileIsValid = true;
    let validationInProgress = false;

    if (!input || !form) {
        return;
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) {
            return bytes + ' bytes';
        }

        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }

        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function getExtension(fileName) {
        const lastDotPosition = fileName.lastIndexOf('.');

        if (
            lastDotPosition <= 0 ||
            lastDotPosition === fileName.length - 1
        ) {
            return '';
        }

        return fileName
            .substring(lastDotPosition + 1)
            .toLowerCase();
    }

    function clearValidationMessage() {
        fileIsValid = true;

        input.classList.remove('is-invalid');

        if (errorBox) {
            errorBox.textContent = '';
            errorBox.style.display = 'none';
        }

        if (informationBox) {
            informationBox.textContent = '';
        }
    }

    function showError(message) {
        fileIsValid = false;
        validationInProgress = false;

        input.value = '';
        input.classList.add('is-invalid');

        if (errorBox) {
            errorBox.textContent = message;
            errorBox.style.display = 'block';
        }

        if (informationBox) {
            informationBox.textContent = '';
        }
    }

    function showFileInformation(file) {
        if (!informationBox) {
            return;
        }

        informationBox.textContent =
            'Selected file: ' +
            file.name +
            ' (' +
            formatFileSize(file.size) +
            ')';
    }

    function startsWithBytes(bytes, signature) {
        if (bytes.length < signature.length) {
            return false;
        }

        return signature.every(function (value, index) {
            return bytes[index] === value;
        });
    }

    function hasJpegSignature(bytes) {
        return startsWithBytes(bytes, [
            0xFF,
            0xD8,
            0xFF
        ]);
    }

    function hasPngSignature(bytes) {
        return startsWithBytes(bytes, [
            0x89,
            0x50,
            0x4E,
            0x47,
            0x0D,
            0x0A,
            0x1A,
            0x0A
        ]);
    }

    function hasPdfSignature(bytes) {
        return startsWithBytes(bytes, [
            0x25,
            0x50,
            0x44,
            0x46,
            0x2D
        ]);
    }

    function hasLegacyWordSignature(bytes) {
        return startsWithBytes(bytes, [
            0xD0,
            0xCF,
            0x11,
            0xE0,
            0xA1,
            0xB1,
            0x1A,
            0xE1
        ]);
    }

    function hasZipSignature(bytes) {
        return (
            startsWithBytes(bytes, [
                0x50,
                0x4B,
                0x03,
                0x04
            ]) ||
            startsWithBytes(bytes, [
                0x50,
                0x4B,
                0x05,
                0x06
            ]) ||
            startsWithBytes(bytes, [
                0x50,
                0x4B,
                0x07,
                0x08
            ])
        );
    }

    function hasMp3Signature(bytes) {
        const hasId3Header = startsWithBytes(bytes, [
            0x49,
            0x44,
            0x33
        ]);

        const hasMpegFrameSync =
            bytes.length >= 2 &&
            bytes[0] === 0xFF &&
            (bytes[1] & 0xE0) === 0xE0;

        return hasId3Header || hasMpegFrameSync;
    }

    function signatureMatchesExtension(extension, bytes) {
        switch (extension) {
            case 'jpg':
            case 'jpeg':
                return hasJpegSignature(bytes);

            case 'png':
                return hasPngSignature(bytes);

            case 'pdf':
                return hasPdfSignature(bytes);

            case 'doc':
                return hasLegacyWordSignature(bytes);

            case 'docx':
                return hasZipSignature(bytes);

            case 'mp3':
                return hasMp3Signature(bytes);

            default:
                return false;
        }
    }

    input.addEventListener('change', function () {
        clearValidationMessage();

        const file =
            input.files && input.files.length > 0
                ? input.files[0]
                : null;

        if (!file) {
            return;
        }

        const extension = getExtension(file.name);

        if (!extension) {
            showError(
                'The selected file does not have a valid extension.'
            );

            return;
        }

        if (!ALLOWED_EXTENSIONS.includes(extension)) {
            showError(
                'Only PDF, DOC, DOCX, JPG, JPEG, PNG and MP3 files are allowed.'
            );

            return;
        }

        if (file.size <= 0) {
            showError('The selected file is empty.');

            return;
        }

        if (file.size > MAX_FILE_SIZE) {
            showError(
                'The selected file must not exceed 10 MB.'
            );

            return;
        }

        const allowedMimeTypes =
            ALLOWED_MIME_TYPES[extension] || [];

        if (
            file.type &&
            !allowedMimeTypes.includes(file.type)
        ) {
            showError(
                'The selected file type does not match its extension.'
            );

            return;
        }

        validationInProgress = true;

        const reader = new FileReader();

        reader.onload = function (event) {
            const bytes = new Uint8Array(
                event.target.result
            );

            if (!signatureMatchesExtension(extension, bytes)) {
                showError(
                    'The file content does not match the selected file extension.'
                );

                return;
            }

            validationInProgress = false;
            fileIsValid = true;
            input.classList.remove('is-invalid');

            showFileInformation(file);
        };

        reader.onerror = function () {
            showError(
                'The selected file could not be read. Please choose another file.'
            );
        };

        reader.readAsArrayBuffer(
            file.slice(0, 16)
        );
    });

    form.addEventListener('submit', function (event) {
        if (validationInProgress) {
            event.preventDefault();

            if (errorBox) {
                errorBox.textContent =
                    'Please wait until file validation is complete.';

                errorBox.style.display = 'block';
            }

            return;
        }

        if (!fileIsValid) {
            event.preventDefault();

            showError(
                'Please select a valid file before submitting the form.'
            );
        }
    });

    form.addEventListener('reset', function () {
        window.setTimeout(function () {
            clearValidationMessage();
        }, 0);
    });
})();
JS;

$this->registerJs(
    $fileValidationScript,
    \yii\web\View::POS_READY
);
?>