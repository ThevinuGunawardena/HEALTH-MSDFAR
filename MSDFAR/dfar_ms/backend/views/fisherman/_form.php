<?php

use backend\models\MFishermanCategory;
use backend\services\CommonService;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use backend\config\Constant;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */
/** @var yii\widgets\ActiveForm $form */

$webURL = Yii::getAlias('@web');


$renew = $renew ?? false;

$isUpdateForm = (bool) (
    $isUpdateForm
    ?? (!$model->isNewRecord && !$renew)
);

$districtList = !empty($districtList)
    ? $districtList
    : CommonService::getFIDistrictArray();

$divisionList = is_array($divisionList ?? null)
    ? $divisionList
    : [];

$years = !empty($years)
    ? $years
    : CommonService::getYearsArray();

$districtOptions = [
    'prompt' => 'Select...',
];

/*
 * Create keeps the existing customv2.js behavior.
 * Update uses its own Yii-generated absolute AJAX URL below.
 */
if (!$isUpdateForm) {
    $districtOptions['onchange'] =
        'loadDivisionsAjaxFisherman('
        . '$(this).val(), "");';
}

$categoryList = ArrayHelper::map(
    MFishermanCategory::find()
        ->where(['status' => 1])
        ->asArray()
        ->all(),
    'id',
    'category'
);

?>

<div class="profile-fisherman-form">

<?php $form = \yii\widgets\ActiveForm::begin([
    'method' => 'post',
    'options' => [
        'enctype' => 'multipart/form-data',
    ],
]); ?>
    <?php if ($renew) { ?>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'fisherman_uid',
                'first_name',
                'last_name',
                'preferred_name_for_id',
                'nic',
                'passport',
                'dob',
                'life_isurance_no',

                [
                    'attribute' => 'gender',
                    'format' => 'text',
                    'value' => function ($model) {
                        return Yii::$app->params['gender'][$model->gender] ?? $model->gender;
                    },
                ],

                [
                    'attribute' => 'civil',
                    'format' => 'text',
                    'value' => function ($model) {
                        return Yii::$app->params['civil'][$model->civil] ?? $model->civil;
                    },
                ],

                'permanent_address:ntext',
                'current_address:ntext',

                [
                    'attribute' => 'blood_group',
                    'format' => 'text',
                    'value' => function ($model) {
                        return Yii::$app->params['blood_groups'][$model->blood_group] ?? $model->blood_group;
                    },
                ],

                'mobile',
                'fixed_line',
                'email',

                [
                    'attribute' => 'member_fisheries_society',
                    'format' => 'text',
                    'value' => function ($model) {
                        return Yii::$app->params['yesNo'][$model->member_fisheries_society]
                            ?? $model->member_fisheries_society;
                    },
                ],

                [
                    'attribute' => 'district',
                    'format' => 'text',
                    'value' => function ($model) use ($districtList) {
                        return $districtList[$model->district] ?? $model->district;
                    },
                ],

                'division',

                [
                    'attribute' => 'category',
                    'format' => 'text',
                    'value' => function ($model) use ($categoryList) {
                        return $categoryList[$model->category] ?? $model->category;
                    },
                ],

                'year_recruitment',

                [
                    'attribute' => 'management_area',
                    'format' => 'text',
                    'value' => function ($model) use ($districtList) {
                        return $districtList[$model->management_area] ?? $model->management_area;
                    },
                ],

                [
                    'attribute' => 'status',
                    'format' => 'text',
                    'value' => function ($model) {
                        return Constant::$licenseStatus[$model->status] ?? $model->status;
                    },
                ],

                [
                    'attribute' => 'approval_stage',
                    'format' => 'text',
                    'label' => 'Approval Stage',
                    'value' => function ($model) {
                        return Constant::$userTypes[$model->approval_stage]['name']
                            ?? $model->approval_stage;
                    },
                ],
            ],
        ]); ?>

    <?php } else { ?>
    <div class="alert alert-info">
                                        <strong>🚨 Notice 🚨 </strong> </br>
                                       <ul>
    <li>
        <strong>Notice:</strong> The system has been integrated with the <strong>Department for Registration of Person </strong> to improve the Fisherman Registration process.
        To enable automatic data retrieval and auto-fill, please <strong>enter the applicant's NIC number first</strong>. Once the NIC is verified,
        the system will automatically populate most of the required fields in the Fisherman Registration form.
    </li>

    <li>
        <strong>දැනුම්දීම:</strong> ධීවර ලියාපදිංචි කිරීමේ ක්‍රියාවලිය පහසු කිරීම සඳහා පද්ධතිය <strong>පුද්ගලයින් ලියාපදිංචි කිරීමේ දෙපාර්තමේන්තුව</strong> සමඟ ඒකාබද්ධ කර ඇත.
        ස්වයංක්‍රීය දත්ත ලබාගැනීම සහ ක්ෂේත්‍ර පිරවීම සඳහා, කරුණාකර <strong>පළමුව අයදුම්කරුගේ ජාතික හැඳුනුම්පත් (NIC) අංකය ඇතුළත් කරන්න</strong>.
        NIC අංකය සත්‍යාපනය වූ පසු, ධීවර ලියාපදිංචි කිරීමේ පෝරමයේ අවශ්‍ය බොහෝ ක්ෂේත්‍ර ස්වයංක්‍රීයව පිරවෙනු ඇත.
    </li>
</ul>

                                    </div>
    <div class="row<?= $isUpdateForm
        ? ' fisherman_profile_update'
        : ' fisherman_profile' ?>">
        <div class="col-xl-8 col-lg-12 col-md-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Personal Details</h6>
                            <div class="row">
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'first_name') ?>
                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'last_name') ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'preferred_name_for_id') ?>
                                </div>
                                <div class="col-xl-6">
                               <?= $form->field($model, 'nic')->textInput([
                                'id' => 'profilefisherman-nic',
                                'maxlength' => 12,
                                'autocomplete' => 'off',
                                'placeholder' => 'Enter NIC and press Enter',
                            ]) ?>

                            <div
                                id="nic-lookup-message"
                                class="small mt-1"
                                role="alert"
                            ></div>

                                <div
                                    id="nic-lookup-message"
                                    class="small mt-1"
                                    role="alert"
                                ></div>                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'passport') ?>
                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'dob')->textInput(["type" => "date", "max" => date("Y-m-d")]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'life_isurance_no') ?>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'gender')->dropDownList(yii::$app->params['gender'],
                                        ['prompt' => 'Select...']) ?>
                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'civil')->dropDownList(yii::$app->params['civil'],
                                        ['prompt' => 'Select...']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'permanent_address')->textarea() ?>
                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'current_address')->textarea() ?>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-xl-6">
                                    <?= $form->field($model, 'blood_group')->dropDownList(yii::$app->params['blood_groups'],
                                        ['prompt' => 'Select...']) ?>
                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'mobile') ?>
                                </div>

                            </div>
                            <div class="row">

                                <div class="col-xl-6">
                                    <?= $form->field($model, 'fixed_line') ?>
                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'email') ?>
                                </div>

                            </div>


                            <div class="row">
                                <div class="col-xl-12">

                                    <?= $form->field($model, 'member_fisheries_society')->dropDownList(yii::$app->params['yesNo'],
                                        ['prompt' => 'Select...']) ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Fisheries Details</h6>
                            <div class="row">

                                <div class="col-xl-6">
                                    <?= $form->field(
                                        $model,
                                        'district'
                                    )->dropDownList(
                                        $districtList,
                                        $districtOptions
                                    ) ?>
                                </div>

                                <div class="col-xl-6">
                                    <?= $form->field(
                                        $model,
                                        'division'
                                    )->dropDownList(
                                        $divisionList,
                                        [
                                            'prompt' => 'Select...',
                                        ]
                                    ) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <?= $form->field($model, 'category')->dropDownList(ArrayHelper::map(MFishermanCategory::find()->where(['status' => 1])->asArray()->all(), "id", "category"), ['prompt' => 'Select...']) ?>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-xl-6">
                                    <?= $form->field(
                                        $model,
                                        'year_recruitment'
                                    )->dropDownList(
                                        $years,
                                        [
                                            'prompt' => 'Select...',
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-xl-6">
                                    <?= $form->field(
                                        $model,
                                        'management_area'
                                    )->dropDownList(
                                        $districtList,
                                        [
                                            'prompt' => 'Select...',
                                        ]
                                    ) ?>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <?= $form->field($model, 'name_sinhala')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'address_sinhala')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'name_tamil')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'address_tamil')->hiddenInput()->label(false) ?>
            <div class="row">
                <div class="col-xl-12 col-lg-6 col-md-6 col-sm-12 col-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">

                            <div class="row">

                                <div class="col-xl-12">
                                <?= $form->field($model, 'privacy_policy')->checkbox([
                                    'label' => 'I confirm that I have read, consent and agree to DFAR <a href="privacy-policy" target="_blank">Privacy Policy</a>',
                                    'encodeLabel' => false,
                                ]) ?>
                                    <h6 class="card-subtitle mb-2 text-muted">Declaration of Applicant</h6>
                                    <label for="declaration">I declare that the above said information are
                                        true
                                        and
                                        accurate.</label>
                                    <div>
                                        <img style="max-width: 300px; max-height: 300px" src="<?=$webURL?>/uploads/fisherman/<?=$model->signature?>">

                                    <?= $form->field($model, 'signature')->fileInput([
                                            'id' => 'signature-input',
                                            'accept' => '.jpg,.jpeg,.png,image/jpeg,image/png',
                                        ]) ?>

                                        <div
                                            id="signature-error"
                                            style="
                                                display:none;
                                                margin-top:10px;
                                                color:#dc3545;
                                                font-weight:600;
                                            "
                                        ></div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>
        <div class="col-xl-4 col-lg-4 col-md-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-5 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Profile Image</h6>
                            <div style="text-align:center;">

                        <img
                            id="img-upload"
                            alt="Selected profile image"
                            style="
                                display:none;
                                width:180px;
                                height:180px;
                                object-fit:cover;
                                border-radius:8px;
                                margin:0 auto 15px;
                            "
                        >
                        <?php if (!empty($model->profile_image)): ?>
                            <div
                                class="image-card"
                                id="existing-profile-image"
                            >
                                <img
                                    src="<?= $webURL ?>/uploads/fisherman/<?= htmlspecialchars(
                                        $model->profile_image,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    alt="Current profile image"
                                    style="
                                        width:180px;
                                        height:180px;
                                        object-fit:cover;
                                        border-radius:8px;
                                    "
                                >
                            </div>
                        <?php endif; ?>

    <br>

    <div class="input-group justify-content-center">
        <span class="input-group-btn">
            <span class="btn btn-info btn-file">

                <?= $form->field(
                                $model,
                                'profile_image'
                            )->fileInput([
                                'id' => 'profile-image-input',
                                'accept' =>
                                    '.jpg,.jpeg,.png,image/jpeg,image/png',
                            ]) ?>
                <div
                    id="profile-image-error"
                    style="
                        display:none;
                        margin-top:10px;
                        color:#dc3545;
                        font-weight:600;
                    "
                ></div>

                <div
                    id="profile-image-source"
                    class="small text-muted mt-2"
                ></div>

            </span>
        </span>
    </div>

    
</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php } ?>

    <div class="row">
        <div class="col-xl-8">
            <div class="row">
                <div class="col-xl-3">
                </div>
                <div class="col-xl-3">
                </div>
                <div class="col-xl-3">
                </div>
                <div class="col-xl-3">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            <?= $model->isNewRecord ? 'Create' : 'Update' ?>
                        </button>                    
                    </div>
                </div>
            </div>
        </div>

    </div>


    <?php ActiveForm::end(); ?>

</div>

<?php

/*
 * Division loading for the fisherman update form.
 */
if ($isUpdateForm) {
    $divisionListUrl = Json::htmlEncode(
        Url::to([
            '/division/list-by-district',
        ])
    );

    $updateDivisionScript = <<<JS
(function () {
    'use strict';

    const districtDropdown = $(
        '#profilefisherman-district'
    );

    const divisionDropdown = $(
        '#profilefisherman-division'
    );

    const divisionListUrl = {$divisionListUrl};

    districtDropdown
        .removeAttr('onchange')
        .off('change');

    districtDropdown.on(
        'change.fishermanUpdateDivision',
        function () {
            const districtId = $(this).val();

            divisionDropdown
                .empty()
                .append(
                    $('<option>', {
                        value: '',
                        text: districtId
                            ? 'Loading divisions...'
                            : 'Select...'
                    })
                );

            if (!districtId) {
                divisionDropdown.prop(
                    'disabled',
                    false
                );

                return;
            }

            divisionDropdown.prop(
                'disabled',
                true
            );

            $.ajax({
                url: divisionListUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    districtId: districtId
                },
                success: function (divisions) {
                    if (typeof divisions === 'string') {
                        try {
                            divisions = JSON.parse(
                                divisions
                            );
                        } catch (error) {
                            divisions = [];
                        }
                    }

                    divisionDropdown
                        .empty()
                        .append(
                            $('<option>', {
                                value: '',
                                text: 'Select...'
                            })
                        );

                    $.each(
                        divisions,
                        function (index, division) {
                            divisionDropdown.append(
                                $('<option>', {
                                    value: division.id,
                                    text: division.name
                                })
                            );
                        }
                    );

                    divisionDropdown.prop(
                        'disabled',
                        false
                    );
                },
                error: function (
                    jqXHR,
                    textStatus,
                    errorThrown
                ) {
                    console.error(
                        'Unable to load divisions:',
                        jqXHR.status,
                        errorThrown
                    );

                    divisionDropdown
                        .empty()
                        .append(
                            $('<option>', {
                                value: '',
                                text: 'Unable to load divisions'
                            })
                        )
                        .prop(
                            'disabled',
                            true
                        );
                }
            });
        }
    );
})();
JS;

    $this->registerJs(
        $updateDivisionScript,
        \yii\web\View::POS_READY
    );
}

/*
 * Validates both:
 * - User-selected profile images
 * - DRP Base64 images converted into File objects
 */
$imageValidationScript = <<<'JS'
(function () {
    'use strict';

    const MAX_FILE_SIZE = 5 * 1024 * 1024;

    function bytesToHex(bytes) {
        return Array.from(bytes)
            .map(function (byte) {
                return byte
                    .toString(16)
                    .padStart(2, '0')
                    .toUpperCase();
            })
            .join(' ');
    }

    function isJpeg(bytes) {
        return (
            bytes.length >= 3 &&
            bytes[0] === 0xFF &&
            bytes[1] === 0xD8 &&
            bytes[2] === 0xFF
        );
    }

    function isPng(bytes) {
        const pngSignature = [
            0x89,
            0x50,
            0x4E,
            0x47,
            0x0D,
            0x0A,
            0x1A,
            0x0A
        ];

        if (bytes.length < pngSignature.length) {
            return false;
        }

        return pngSignature.every(
            function (value, index) {
                return bytes[index] === value;
            }
        );
    }

    function setupImageValidation(config) {
        const input = document.getElementById(
            config.inputId
        );

        const preview = document.getElementById(
            config.previewId
        );

        const errorBox = document.getElementById(
            config.errorId
        );

        const existingImage = document.getElementById(
            config.existingImageId
        );

        const sourceMessage = config.sourceId
            ? document.getElementById(config.sourceId)
            : null;

        if (!input) {
            return;
        }

        function clearError() {
            if (errorBox) {
                errorBox.textContent = '';
                errorBox.style.display = 'none';
            }

            input.classList.remove('is-invalid');
        }

        function clearSourceMessage() {
            if (sourceMessage) {
                sourceMessage.textContent = '';
            }
        }

        function showError(message) {
            input.value = '';
            input.classList.add('is-invalid');

            if (errorBox) {
                errorBox.textContent = message;
                errorBox.style.display = 'block';
            }

            if (preview) {
                preview.src = '';
                preview.style.display = 'none';
            }

            if (existingImage) {
                existingImage.style.display = 'block';
            }

            clearSourceMessage();
        }

        input.addEventListener(
            'change',
            function () {
                clearError();

                const file = input.files &&
                    input.files.length > 0
                    ? input.files[0]
                    : null;

                if (!file) {
                    clearSourceMessage();
                    return;
                }

                const fileNameParts = file.name.split('.');

                if (fileNameParts.length < 2) {
                    showError(
                        'The selected file does not have a valid extension.'
                    );

                    return;
                }

                const extension = fileNameParts
                    .pop()
                    .toLowerCase();

                const allowedExtensions = [
                    'jpg',
                    'jpeg',
                    'png'
                ];

                if (!allowedExtensions.includes(extension)) {
                    showError(
                        'Only JPG, JPEG and PNG images are allowed.'
                    );

                    return;
                }

                if (file.size > MAX_FILE_SIZE) {
                    showError(
                        'The image must not exceed 5 MB.'
                    );

                    return;
                }

                const allowedMimeTypes = [
                    'image/jpeg',
                    'image/png'
                ];

                if (
                    file.type &&
                    !allowedMimeTypes.includes(file.type)
                ) {
                    showError(
                        'The selected file is not a valid JPG, JPEG or PNG image.'
                    );

                    return;
                }

                const signatureReader = new FileReader();

                signatureReader.onload = function (event) {
                    const bytes = new Uint8Array(
                        event.target.result
                    );

                    const detectedAsJpeg = isJpeg(bytes);
                    const detectedAsPng = isPng(bytes);

                    console.log(
                        config.fieldName + ' hex signature:',
                        bytesToHex(bytes)
                    );

                    if (
                        !detectedAsJpeg &&
                        !detectedAsPng
                    ) {
                        showError(
                            'Invalid image content. Only genuine JPG, JPEG and PNG files are allowed.'
                        );

                        return;
                    }

                    if (
                        detectedAsJpeg &&
                        extension !== 'jpg' &&
                        extension !== 'jpeg'
                    ) {
                        showError(
                            'The file contains JPEG data, but its extension does not match.'
                        );

                        return;
                    }

                    if (
                        detectedAsPng &&
                        extension !== 'png'
                    ) {
                        showError(
                            'The file contains PNG data, but its extension does not match.'
                        );

                        return;
                    }

                    if (sourceMessage) {
                        if (
                            file.name.startsWith(
                                'drp-profile-image.'
                            )
                        ) {
                            sourceMessage.textContent =
                                'Profile image loaded from DRP.';
                        } else {
                            sourceMessage.textContent =
                                'User-selected profile image will be used.';
                        }
                    }

                    if (!preview) {
                        return;
                    }

                    const previewReader =
                        new FileReader();

                    previewReader.onload = function (
                        previewEvent
                    ) {
                        preview.src =
                            previewEvent.target.result;

                        preview.style.display = 'block';

                        if (existingImage) {
                            existingImage.style.display =
                                'none';
                        }
                    };

                    previewReader.onerror = function () {
                        showError(
                            'Unable to preview the selected image.'
                        );
                    };

                    previewReader.readAsDataURL(file);
                };

                signatureReader.onerror = function () {
                    showError(
                        'Unable to read the selected image.'
                    );
                };

                signatureReader.readAsArrayBuffer(
                    file.slice(0, 8)
                );
            }
        );
    }

    setupImageValidation({
        inputId: 'profile-image-input',
        previewId: 'img-upload',
        errorId: 'profile-image-error',
        existingImageId: 'existing-profile-image',
        sourceId: 'profile-image-source',
        fieldName: 'Profile image'
    });

    setupImageValidation({
        inputId: 'signature-input',
        previewId: 'signature-upload',
        errorId: 'signature-error',
        existingImageId: 'existing-signature-image',
        sourceId: null,
        fieldName: 'Signature'
    });
})();
JS;

$this->registerJs(
    $imageValidationScript,
    \yii\web\View::POS_READY
);

/*
 * NIC personal-details API URL.
 */
$personalDetailsUrl = Json::htmlEncode(
    Url::to([
        '/fisherman/get-personal-details',
    ])
);

$csrfParam = Json::htmlEncode(
    Yii::$app->request->csrfParam
);

$csrfToken = Json::htmlEncode(
    Yii::$app->request->getCsrfToken()
);

$personalDetailsScript = <<<JS
(function () {
    'use strict';

    const personalDetailsUrl = {$personalDetailsUrl};
    const csrfParam = {$csrfParam};
    const csrfToken = {$csrfToken};

    const MAX_FILE_SIZE = 5 * 1024 * 1024;

    const nicInput = $('#profilefisherman-nic');
    const messageBox = $('#nic-lookup-message');

    let activeRequest = null;
    let previouslyLoadedNic = '';

    function showMessage(message, type) {
        if (!messageBox.length) {
            return;
        }

        messageBox
            .removeClass(
                'text-info text-success text-danger text-warning'
            )
            .addClass('text-' + type)
            .text(message);
    }

    function normalizeNic(value) {
        return String(value || '')
            .replace(/\\s+/g, '')
            .toUpperCase();
    }

    function isValidNic(nic) {
        return /^(?:\\d{9}[VX]|\\d{12})$/.test(nic);
    }

    function setFieldValue(selector, value) {
        if (
            value === null ||
            value === undefined ||
            String(value).trim() === ''
        ) {
            return;
        }

        $(selector)
            .val(String(value).trim())
            .trigger('change');
    }

    function convertDate(dateValue) {
        if (!dateValue) {
            return '';
        }

        const value = String(dateValue).trim();

        /*
         * DRP format: DD/MM/YYYY
         * HTML date input format: YYYY-MM-DD
         */
        const match = value.match(
            /^(\\d{1,2})[\\/-](\\d{1,2})[\\/-](\\d{4})$/
        );

        if (!match) {
            return value;
        }

        const day = match[1].padStart(2, '0');
        const month = match[2].padStart(2, '0');
        const year = match[3];

        return year + '-' + month + '-' + day;
    }

    function setGender(gender) {
        if (!gender) {
            return;
        }

        const dropdown = $('#profilefisherman-gender');

        const requiredGender = String(gender)
            .trim()
            .toLowerCase();

        let matchingOption = null;

        dropdown.find('option').each(
            function () {
                const optionValue = String(
                    $(this).val()
                )
                    .trim()
                    .toLowerCase();

                const optionText = String(
                    $(this).text()
                )
                    .trim()
                    .toLowerCase();

                if (
                    optionValue === requiredGender ||
                    optionText === requiredGender
                ) {
                    matchingOption = $(this).val();
                    return false;
                }
            }
        );

        if (matchingOption !== null) {
            dropdown
                .val(matchingOption)
                .trigger('change');
        }
    }

    /*
     * Removes an optional data-URI prefix and
     * cleans whitespace from the Base64 value.
     */
    function normalizeBase64Image(base64Value) {
        if (!base64Value) {
            throw new Error(
                'The DRP profile image is empty.'
            );
        }

        let value = String(base64Value).trim();
        let declaredMimeType = '';

        const dataUriMatch = value.match(
            /^data:(image\\/(?:jpeg|jpg|png));base64,([\\s\\S]*)$/i
        );

        if (dataUriMatch) {
            declaredMimeType =
                dataUriMatch[1].toLowerCase();

            if (declaredMimeType === 'image/jpg') {
                declaredMimeType = 'image/jpeg';
            }

            value = dataUriMatch[2];
        }

        value = value.replace(/\\s+/g, '');

        if (value === '') {
            throw new Error(
                'The DRP profile image Base64 value is empty.'
            );
        }

        return {
            base64: value,
            declaredMimeType: declaredMimeType
        };
    }

    function isJpegBytes(bytes) {
        return (
            bytes.length >= 3 &&
            bytes[0] === 0xFF &&
            bytes[1] === 0xD8 &&
            bytes[2] === 0xFF
        );
    }

    function isPngBytes(bytes) {
        const pngSignature = [
            0x89,
            0x50,
            0x4E,
            0x47,
            0x0D,
            0x0A,
            0x1A,
            0x0A
        ];

        if (bytes.length < pngSignature.length) {
            return false;
        }

        return pngSignature.every(
            function (value, index) {
                return bytes[index] === value;
            }
        );
    }

    /*
     * Converts the DRP Base64 image to a normal File.
     *
     * The real MIME type is detected from the binary
     * signature rather than trusting the Base64 prefix.
     */
    function base64ToFile(base64Value) {
        const normalized = normalizeBase64Image(
            base64Value
        );

        let binaryString;

        try {
            binaryString = window.atob(
                normalized.base64
            );
        } catch (error) {
            throw new Error(
                'The DRP profile image contains invalid Base64 data.'
            );
        }

        if (binaryString.length > MAX_FILE_SIZE) {
            throw new Error(
                'The DRP profile image must not exceed 5 MB.'
            );
        }

        const bytes = new Uint8Array(
            binaryString.length
        );

        for (
            let index = 0;
            index < binaryString.length;
            index++
        ) {
            bytes[index] =
                binaryString.charCodeAt(index);
        }

        let actualMimeType;
        let extension;

        if (isJpegBytes(bytes)) {
            actualMimeType = 'image/jpeg';
            extension = 'jpg';
        } else if (isPngBytes(bytes)) {
            actualMimeType = 'image/png';
            extension = 'png';
        } else {
            throw new Error(
                'The DRP profile image is not a genuine JPEG or PNG image.'
            );
        }

        if (
            normalized.declaredMimeType &&
            normalized.declaredMimeType !== actualMimeType
        ) {
            throw new Error(
                'The DRP image type does not match its actual image content.'
            );
        }

        return new File(
            [bytes],
            'drp-profile-image.' + extension,
            {
                type: actualMimeType,
                lastModified: Date.now()
            }
        );
    }

    /*
     * Assigns the DRP image to the same profile_image
     * input used for manually selected images.
     */
    function assignDrpImageToProfileInput(base64Value) {
    const profileInput = document.getElementById(
        'profile-image-input'
    );

    const preview = document.getElementById(
        'img-upload'
    );

    const errorBox = document.getElementById(
        'profile-image-error'
    );

    const existingImage = document.getElementById(
        'existing-profile-image'
    );

    const sourceMessage = document.getElementById(
        'profile-image-source'
    );

    if (!profileInput || !base64Value) {
        return;
    }

    /*
     * Do not overwrite an image manually selected
     * by the user before the NIC lookup.
     */
    const currentFile =
        profileInput.files &&
        profileInput.files.length > 0
            ? profileInput.files[0]
            : null;

    if (
        currentFile &&
        !currentFile.name.startsWith(
            'drp-profile-image.'
        )
    ) {
        return;
    }

    try {
        if (typeof DataTransfer === 'undefined') {
            throw new Error(
                'This browser does not support assigning the DRP image to the upload field.'
            );
        }

        /*
         * base64ToFile() already validates:
         * - Base64 content
         * - Maximum size
         * - JPEG/PNG signature
         * - MIME type
         */
        const imageFile = base64ToFile(
            base64Value
        );

        const dataTransfer = new DataTransfer();

        dataTransfer.items.add(imageFile);

        /*
         * Use the same profile_image input.
         */
        profileInput.files = dataTransfer.files;

        profileInput.classList.remove(
            'is-invalid'
        );

        /*
         * Display the DRP image directly.
         * Do not trigger the change event again.
         */
        if (preview) {
            const previewUrl = URL.createObjectURL(
                imageFile
            );

            if (preview.dataset.objectUrl) {
                URL.revokeObjectURL(
                    preview.dataset.objectUrl
                );
            }

            preview.dataset.objectUrl =
                previewUrl;

            preview.src = previewUrl;
            preview.style.display = 'block';
        }

        if (existingImage) {
            existingImage.style.display = 'none';
        }

        if (errorBox) {
            errorBox.textContent = '';
            errorBox.style.display = 'none';
        }

        if (sourceMessage) {
            sourceMessage.textContent =
                'Profile image loaded from DRP.';
        }
    } catch (error) {
        console.error(
            'Unable to assign DRP profile image:',
            error
        );

        profileInput.value = '';
        profileInput.classList.add(
            'is-invalid'
        );

        if (preview) {
            if (preview.dataset.objectUrl) {
                URL.revokeObjectURL(
                    preview.dataset.objectUrl
                );

                delete preview.dataset.objectUrl;
            }

            preview.removeAttribute('src');
            preview.style.display = 'none';
        }

        if (existingImage) {
            existingImage.style.display = 'block';
        }

        if (errorBox) {
            errorBox.textContent =
                error.message ||
                'Unable to process the DRP profile image.';

            errorBox.style.display = 'block';
        }

        if (sourceMessage) {
            sourceMessage.textContent = '';
        }
    }
}
    function populateForm(data) {
        setFieldValue(
            '#profilefisherman-preferred_name_for_id',
            data.fullNameEnglish
        );

        setFieldValue(
            '#profilefisherman-dob',
            convertDate(data.dateOfBirth)
        );

        setGender(data.gender);

        setFieldValue(
            '#profilefisherman-mobile',
            data.mobileNumber
        );

        setFieldValue(
            '#profilefisherman-permanent_address',
            data.addressEnglish
        );

        setFieldValue(
            '#profilefisherman-current_address',
            data.addressEnglish
        );

        setFieldValue(
            '#profilefisherman-name_sinhala',
            data.fullNameSinhala
        );

        setFieldValue(
            '#profilefisherman-address_sinhala',
            data.addressSinhala
        );

        setFieldValue(
            '#profilefisherman-name_tamil',
            data.fullNameTamil
        );

        setFieldValue(
            '#profilefisherman-address_tamil',
            data.addressTamil
        );

        /*
         * Convert the DRP Base64 image into a File
         * and assign it to profile_image.
         */
        if (data.photoImage) {
            assignDrpImageToProfileInput(
                data.photoImage
            );
        }
    }

    function retrievePersonalDetails() {
        const nic = normalizeNic(
            nicInput.val()
        );

        nicInput.val(nic);

        if (nic === '') {
            showMessage(
                'Please enter the fisherman NIC.',
                'danger'
            );

            return;
        }

        if (!isValidNic(nic)) {
            showMessage(
                'Please enter a valid old or new NIC number.',
                'danger'
            );

            return;
        }

        if (nic === previouslyLoadedNic) {
            return;
        }

        if (activeRequest) {
            activeRequest.abort();
        }

        const requestData = {
            nic: nic
        };

        requestData[csrfParam] = csrfToken;

        showMessage(
            'Retrieving personal details...',
            'info'
        );

        nicInput.prop(
            'readonly',
            true
        );

        activeRequest = $.ajax({
            url: personalDetailsUrl,
            type: 'POST',
            dataType: 'json',
            data: requestData
        });

        activeRequest.done(
            function (response) {
                if (
                    !response ||
                    response.success !== true
                ) {
                    showMessage(
                        response &&
                        response.message
                            ? response.message
                            : 'Personal details could not be retrieved.',
                        'danger'
                    );

                    return;
                }

                populateForm(
                    response.data || {}
                );

                previouslyLoadedNic = nic;

                showMessage(
                    'Personal details loaded successfully.',
                    'success'
                );
            }
        );

        activeRequest.fail(
            function (
                xhr,
                textStatus,
                errorThrown
            ) {
                if (textStatus === 'abort') {
                    return;
                }

                let message =
                    'Unable to retrieve personal details.';

                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {
                    message =
                        xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    message =
                        'NIC lookup route was not found.';
                } else if (xhr.status === 403) {
                    message =
                        'You are not authorized to retrieve NIC details.';
                } else if (xhr.status === 400) {
                    message =
                        'The NIC lookup request was rejected.';
                } else if (xhr.status >= 500) {
                    message =
                        'A server error occurred while retrieving NIC details.';
                }

                console.error(
                    'NIC lookup error:',
                    xhr.status,
                    errorThrown,
                    xhr.responseText
                );

                showMessage(
                    message,
                    'danger'
                );
            }
        );

        activeRequest.always(
            function () {
                nicInput.prop(
                    'readonly',
                    false
                );

                activeRequest = null;
            }
        );
    }

    /*
     * Retrieve when the user leaves the NIC field.
     */
    nicInput.on(
        'change.nicLookup',
        retrievePersonalDetails
    );

    /*
     * Retrieve when the user presses Enter.
     */
    nicInput.on(
        'keydown.nicLookup',
        function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();

                retrievePersonalDetails();
            }
        }
    );
})();
JS;

$this->registerJs(
    $personalDetailsScript,
    \yii\web\View::POS_READY
);

?>