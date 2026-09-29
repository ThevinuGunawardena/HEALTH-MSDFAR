<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var backend\models\ProfileOfficer $model
 */

$maxFileSize = 5 * 1024 * 1024; // 5 MB

$imageInputOptions = [
    'accept' => '.jpg,.jpeg,.png,image/jpeg,image/png',
    'data-file-validation' => 'image',
    'data-max-file-size' => $maxFileSize,
];

$documentInputOptions = [
    'accept' => '.pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png',
    'data-file-validation' => 'document',
    'data-max-file-size' => $maxFileSize,
];

$selectedDivision = Json::encode($model->division);

$script = <<<JS
(function () {
    var districtField = $('#profileofficer-district, #profileofficers-district').first();

    if (districtField.length && districtField.val()) {
        districtField.trigger('change');
    }
})();
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="profile-officer-form">
    <?php $form = ActiveForm::begin([
        'id' => 'profile-officer-form',
        'options' => [
            'enctype' => 'multipart/form-data',
            'novalidate' => true,
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nic')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'current_designation')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'current_workplace_type')->dropDownList([
                'District Office' => 'District Office',
                'Head Office' => 'Head Office',
            ], ['prompt' => 'Select Type']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'ministry_dept')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'district')->dropDownList(
                CommonService::getFIDistrictArray(),
                [
                    'prompt' => 'Select...',
                    'onchange' => "loadDivisionsAjaxOfficer(this.value, {$selectedDivision});",
                ]
            ) ?>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'division')->dropDownList([], ['prompt' => 'Select']) ?>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'harbour')->dropDownList(
                Constant::$departureHarbours,
                ['prompt' => 'Select']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'device_serial')->textInput() ?>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'user_level')->dropDownList(
                Constant::$USER_RANKS,
                ['prompt' => 'Select']
            ) ?>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'public_service_appointment_date')->textInput(['type' => 'date']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'appointment_letter')->fileInput(array_merge(
                $documentInputOptions,
                ['id' => 'appointment-letter-input']
            )) ?>
            <div id="appointment-letter-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'dfar_appointment_date')->textInput(['type' => 'date']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'dfar_appointment_letter')->fileInput(array_merge(
                $documentInputOptions,
                ['id' => 'dfar-appointment-letter-input']
            )) ?>
            <div id="dfar-appointment-letter-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'recruitment_method')->dropDownList([
                'New Appointment' => 'New Appointment',
                'Transfer' => 'Transfer',
                'Hire' => 'Hire',
                'Other' => 'Other',
            ], ['prompt' => 'Select Status']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'w_op_number')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'appointment_status')->dropDownList([
                'Permanent' => 'Permanent',
                'Contract' => 'Contract',
                'Temporary' => 'Temporary',
            ], ['prompt' => 'Select Status']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'date_of_birth')->textInput(['type' => 'date']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'personal_email')->textInput([
                'type' => 'email',
                'maxlength' => true,
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'place_of_birth')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'permanent_address')->textarea(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'mobile_phone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'home_phone')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <img id="photograph-preview"
                 alt="Photograph preview"
                 style="display: none; max-width: 200px; max-height: 200px; margin-bottom: 10px;">

            <?= $form->field($model, 'photograph')->fileInput(array_merge(
                $imageInputOptions,
                [
                    'id' => 'photograph-input',
                    'data-preview-id' => 'photograph-preview',
                ]
            )) ?>
            <div id="photograph-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'passport_copy')->fileInput(array_merge(
                $documentInputOptions,
                ['id' => 'passport-copy-input']
            )) ?>
            <div id="passport-copy-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'driving_license_copy')->fileInput(array_merge(
                $documentInputOptions,
                ['id' => 'driving-license-copy-input']
            )) ?>
            <div id="driving-license-copy-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <?php
            $profileImageUrl = !empty($model->profile_image)
                ? Constant::$FILE_VIEW_PATH . 'officer/profile/' . rawurlencode(basename($model->profile_image))
                : null;
            ?>

            <?php if ($profileImageUrl !== null): ?>
                <?= Html::img($profileImageUrl, [
                    'id' => 'existing-profile-image',
                    'alt' => 'Current profile image',
                    'style' => 'max-width: 200px; max-height: 200px; margin-bottom: 10px;',
                ]) ?>
            <?php endif; ?>

            <img id="profile-image-preview"
                 alt="Profile image preview"
                 style="display: none; max-width: 200px; max-height: 200px; margin-bottom: 10px;">

            <?= $form->field($model, 'profile_image')->fileInput(array_merge(
                $imageInputOptions,
                [
                    'id' => 'profile-image-input',
                    'data-preview-id' => 'profile-image-preview',
                    'data-existing-image-id' => 'existing-profile-image',
                ]
            )) ?>
            <div id="profile-image-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>

        <?php if (!UserTypeUtil::hasType(Constant::FI)): ?>
            <div class="col-xl-6">
                <?php
                $signatureUrl = !empty($model->signature)
                    ? Constant::$FILE_VIEW_PATH . 'officer/signature/' . rawurlencode(basename($model->signature))
                    : null;
                ?>

                <?php if ($signatureUrl !== null): ?>
                    <?= Html::img($signatureUrl, [
                        'id' => 'existing-signature-image',
                        'alt' => 'Current signature',
                        'style' => 'max-width: 200px; max-height: 200px; margin-bottom: 10px;',
                    ]) ?>
                <?php endif; ?>

                <img id="signature-preview"
                     alt="Signature preview"
                     style="display: none; max-width: 200px; max-height: 200px; margin-bottom: 10px;">

                <?= $form->field($model, 'signature')->fileInput(array_merge(
                    $imageInputOptions,
                    [
                        'id' => 'signature-input',
                        'data-preview-id' => 'signature-preview',
                        'data-existing-image-id' => 'existing-signature-image',
                    ]
                )) ?>
                <div id="signature-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'agreement')->fileInput(array_merge(
                $documentInputOptions,
                ['id' => 'agreement-input']
            )) ?>
            <div id="agreement-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'it_result_sheet')->fileInput(array_merge(
                $documentInputOptions,
                ['id' => 'it-result-sheet-input']
            )) ?>
            <div id="it-result-sheet-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>
        <div class="col-xl-6">
            <?= $form->field($model, 'cetificate')->fileInput(array_merge(
                $documentInputOptions,
                ['id' => 'certificate-input']
            )) ?>
            <div id="certificate-input-file-error" class="text-danger small mt-1" style="display: none;"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <?= $form->field($model, 'privacy_policy')->checkbox([
                'label' => 'I confirm that I have read, consent and agree to the DFAR ' .
                    Html::a('Privacy Policy', Url::to(['site/privacy-policy']), [
                        'target' => '_blank',
                        'rel' => 'noopener noreferrer',
                    ]),
                'encodeLabel' => false,
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Create' : 'Update',
            [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'id' => 'profile-officer-submit-button',
            ]
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$fileValidationScript = <<<'JS'
(function () {
    'use strict';

    const DEFAULT_MAX_FILE_SIZE = 5 * 1024 * 1024;

    const TYPE_CONFIG = {
        image: {
            extensions: ['jpg', 'jpeg', 'png'],
            mimeTypes: ['image/jpeg', 'image/png'],
            detectedTypes: ['jpeg', 'png'],
            description: 'JPG, JPEG or PNG image'
        },
        document: {
            extensions: ['pdf', 'jpg', 'jpeg', 'png'],
            mimeTypes: ['application/pdf', 'image/jpeg', 'image/png'],
            detectedTypes: ['pdf', 'jpeg', 'png'],
            description: 'PDF, JPG, JPEG or PNG file'
        }
    };

    function getErrorBox(input) {
        return document.getElementById(input.id + '-file-error');
    }

    function getPreview(input) {
        const previewId = input.dataset.previewId;
        return previewId ? document.getElementById(previewId) : null;
    }

    function getExistingImage(input) {
        const existingImageId = input.dataset.existingImageId;
        return existingImageId ? document.getElementById(existingImageId) : null;
    }

    function clearPreview(input) {
        const preview = getPreview(input);
        const existingImage = getExistingImage(input);

        if (preview) {
            preview.removeAttribute('src');
            preview.style.display = 'none';
        }

        if (existingImage) {
            existingImage.style.display = '';
        }
    }

    function clearError(input) {
        const errorBox = getErrorBox(input);

        input.classList.remove('is-invalid');
        input.removeAttribute('aria-invalid');

        if (errorBox) {
            errorBox.textContent = '';
            errorBox.style.display = 'none';
        }
    }

    function showMessage(input, message) {
        const errorBox = getErrorBox(input);

        input.classList.add('is-invalid');
        input.setAttribute('aria-invalid', 'true');

        if (errorBox) {
            errorBox.textContent = message;
            errorBox.style.display = 'block';
        }
    }

    function rejectFile(input, message) {
        input.value = '';
        input.dataset.fileValid = '0';
        input.dataset.fileValidating = '0';
        clearPreview(input);
        showMessage(input, message);
    }

    function getExtension(fileName) {
        const lastDotIndex = fileName.lastIndexOf('.');

        if (lastDotIndex <= 0 || lastDotIndex === fileName.length - 1) {
            return '';
        }

        return fileName.substring(lastDotIndex + 1).toLowerCase();
    }

    function detectFileType(bytes) {
        const isJpeg = bytes.length >= 3 &&
            bytes[0] === 0xFF &&
            bytes[1] === 0xD8 &&
            bytes[2] === 0xFF;

        if (isJpeg) {
            return 'jpeg';
        }

        const pngSignature = [
            0x89, 0x50, 0x4E, 0x47,
            0x0D, 0x0A, 0x1A, 0x0A
        ];

        const isPng = bytes.length >= pngSignature.length &&
            pngSignature.every(function (value, index) {
                return bytes[index] === value;
            });

        if (isPng) {
            return 'png';
        }

        const isPdf = bytes.length >= 5 &&
            bytes[0] === 0x25 &&
            bytes[1] === 0x50 &&
            bytes[2] === 0x44 &&
            bytes[3] === 0x46 &&
            bytes[4] === 0x2D;

        if (isPdf) {
            return 'pdf';
        }

        return null;
    }

    function extensionMatchesDetectedType(extension, detectedType) {
        if (detectedType === 'jpeg') {
            return extension === 'jpg' || extension === 'jpeg';
        }

        return extension === detectedType;
    }

    function readHeader(file) {
        return new Promise(function (resolve, reject) {
            const reader = new FileReader();

            reader.onload = function (event) {
                resolve(new Uint8Array(event.target.result));
            };

            reader.onerror = function () {
                reject(new Error('Unable to read the selected file.'));
            };

            reader.readAsArrayBuffer(file.slice(0, 8));
        });
    }

    function renderPreview(input, file, detectedType) {
        const preview = getPreview(input);
        const existingImage = getExistingImage(input);

        if (!preview || (detectedType !== 'jpeg' && detectedType !== 'png')) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            preview.src = event.target.result;
            preview.style.display = 'block';

            if (existingImage) {
                existingImage.style.display = 'none';
            }
        };

        reader.onerror = function () {
            rejectFile(input, 'The file is valid, but its preview could not be generated.');
        };

        reader.readAsDataURL(file);
    }

    async function validateFileInput(input) {
        clearError(input);
        input.dataset.fileValid = '0';

        const file = input.files && input.files.length > 0
            ? input.files[0]
            : null;

        if (!file) {
            input.dataset.fileValidating = '0';
            clearPreview(input);
            return;
        }

        const validationType = input.dataset.fileValidation;
        const config = TYPE_CONFIG[validationType];

        if (!config) {
            rejectFile(input, 'File validation configuration is missing.');
            return;
        }

        const maxFileSize = Number(input.dataset.maxFileSize) || DEFAULT_MAX_FILE_SIZE;
        const extension = getExtension(file.name);

        if (!extension || !config.extensions.includes(extension)) {
            rejectFile(input, 'Only ' + config.description + ' types are allowed.');
            return;
        }

        if (file.size <= 0) {
            rejectFile(input, 'The selected file is empty.');
            return;
        }

        if (file.size > maxFileSize) {
            rejectFile(input, 'The selected file must not exceed 5 MB.');
            return;
        }

        if (file.type && !config.mimeTypes.includes(file.type.toLowerCase())) {
            rejectFile(input, 'The selected file type does not match the allowed file types.');
            return;
        }

        input.dataset.fileValidating = '1';

        try {
            const bytes = await readHeader(file);
            const detectedType = detectFileType(bytes);

            if (!detectedType || !config.detectedTypes.includes(detectedType)) {
                rejectFile(input, 'The selected file content is invalid or unsupported.');
                return;
            }

            if (!extensionMatchesDetectedType(extension, detectedType)) {
                rejectFile(input, 'The file extension does not match the actual file content.');
                return;
            }

            input.dataset.fileValid = '1';
            input.dataset.fileValidating = '0';
            clearError(input);
            renderPreview(input, file, detectedType);
        } catch (error) {
            rejectFile(input, error.message || 'Unable to validate the selected file.');
        }
    }

    const inputs = document.querySelectorAll(
        '#profile-officer-form input[type="file"][data-file-validation]'
    );

    inputs.forEach(function (input) {
        input.dataset.fileValid = '0';
        input.dataset.fileValidating = '0';

        input.addEventListener('change', function () {
            validateFileInput(input);
        });
    });

    const form = document.getElementById('profile-officer-form');

    if (form) {
        form.addEventListener('submit', function (event) {
            let preventSubmit = false;

            inputs.forEach(function (input) {
                const hasFile = input.files && input.files.length > 0;

                if (input.dataset.fileValidating === '1') {
                    showMessage(input, 'Please wait until file validation is complete.');
                    preventSubmit = true;
                    return;
                }

                if (hasFile && input.dataset.fileValid !== '1') {
                    showMessage(input, 'Please select a valid file before submitting.');
                    preventSubmit = true;
                }
            });

            if (preventSubmit) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);
    }
})();
JS;

$this->registerJs($fileValidationScript, yii\web\View::POS_READY);
?>