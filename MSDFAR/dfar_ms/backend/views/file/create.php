<?php

/** @var yii\web\View $this */
/** @var backend\models\Files $model */
/** @var array $documentArray */
/** @var array $files */
/** @var string|null $process */
/** @var int|string|null $recordId */
/** @var string|null $token */
/** @var bool|null $isTokenizedProcess */

use backend\config\Constant;
use backend\config\UserTypeUtil;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t(
    'app',
    'Upload Files'
);

$this->params['breadcrumbs'][] =
    $this->title;

$request = Yii::$app->request;

$process = trim(
    (string) (
        $process
        ?? $request->getQueryParam(
            'process',
            ''
        )
    )
);

$recordId = (int) (
    $recordId
    ?? $model->process_id
    ?? $request->getQueryParam(
        'id',
        0
    )
);

$token = trim(
    (string) (
        $token
        ?? $request->getQueryParam(
            'token',
            ''
        )
    )
);

$processRoutes = [
    'BOAT_NUMBER' =>
        'boat-numbers',

    'BOAT_REGISTER' =>
        'boat-registration',

    'BOAT_REGISTER_ReNEW' =>
        'boat-registration',

    'NATIONAL_LICENSE' =>
        'national-license',

    'HIGHSEAS_LICENSE' =>
        'highseas-license',

    'HIGHSEAS_LICENSE_RENEW' =>
        'highseas-license',

    'SKIPPER_LICENCE' =>
        'skipper',

     'SKIPPER_LICENCE_RENEW' =>
        'skipper-renew',

    'BOAT_TRANSFER' =>
        'boat-transfer',

    'ExportBedchamber' =>
        'applicationexportbechedemer',

    'ExportChank' =>
        'applicationexportchank',

    'ExportLiveFish' =>
        'applicationexportlivefish',

    'ExportLobster' =>
        'applicationexportlobster',

    'ExportNakla' =>
        'applicationexportnakla',

    'TransportBechedemer' =>
        'applicationtransportbechedemer',

    'TransportChank' =>
        'applicationtransportchank',

    'TransportNakla' =>
        'applicationtransportnakla',
];

$viewUrl =
    $processRoutes[$process] ?? '';

$tokenizedProcesses = [
    'BOAT_NUMBER',
    'HIGHSEAS_LICENSE',
    'HIGHSEAS_LICENSE_RENEW',
    'NATIONAL_LICENSE',
    'BOAT_REGISTER',
    'BOAT_REGISTER_ReNEW',
    'SKIPPER_LICENCE',
    'SKIPPER_LICENCE_RENEW'
];

$isTokenizedProcess = isset($isTokenizedProcess)
    ? (bool) $isTokenizedProcess
    : in_array(
        $process,
        $tokenizedProcesses,
        true
    );

/*
 * Tokenized processes return using the encrypted token.
 */
if (
    $isTokenizedProcess
    && $token !== ''
    && $viewUrl !== ''
) {
    $backUrl = Url::to([
        '/' . $viewUrl . '/view',
        'token' => $token,
    ]);
} elseif (
    $viewUrl !== ''
    && $recordId > 0
) {
    /*
     * Existing non-tokenized modules continue using ID.
     */
    $backUrl = Url::to([
        '/' . $viewUrl . '/view',
        'id' => $recordId,
    ]);
} else {
    $backUrl = Url::to([
        '/file/index',
    ]);
}

$allowedFileAccept = implode(
    ',',
    [
        '.jpg',
        '.jpeg',
        '.png',
        '.pdf',
        '.doc',
        '.docx',
        'image/jpeg',
        'image/png',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ]
);
?>

<div
    id="secure-files-upload-page"
    class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12"
    data-allowed-file-types="<?= Html::encode($allowedFileAccept) ?>"
>
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?= Html::a(
                Yii::t('app', 'Go Back'),
                $backUrl,
                [
                    'class' => 'btn btn-outline-primary btn-block mb-4',
                ]
            ) ?>

            <div class="secure-upload-notice alert alert-info">
                <strong>Allowed file types:</strong>
                JPG, JPEG, PNG, PDF, DOC and DOCX.
            </div>

            <?= $this->render('_form', [
                'model' => $model,
                'documentArray' => $documentArray,
                'files' => $files,
                'process' => $process,
                'recordId' => $recordId,
                'token' => $token,
                'isTokenizedProcess' =>
                    $isTokenizedProcess,
            ]) ?>

            <div class="table-responsive mt-4">
                <table class="table table-sm table-bordered">
                    <thead>
                    <tr>
                        <th>File type</th>
                        <th>File Name</th>
                        <th style="width:120px;">Action</th>
                    </tr>
                    </thead>

                    <tbody>

                       <?php foreach ($files as $file): ?>

    <?php
    /*
     * Prevent stored directory paths from being used
     * in the public file URL.
     */
    $fileName = basename(
        (string) $file->file_name
    );

    $fileTypeDescription =
        (string) (
            $file->fileType->discription
            ?? Yii::t(
                'app',
                'Document'
            )
        );

    $fileViewUrl =
        rtrim(
            Constant::$FILE_VIEW_PATH,
            '/'
        )
        . '/files/'
        . rawurlencode($fileName);

    $deleteRoute = [
        '/file/delete',
        'fileId' => $file->id,
        'process' => $process,
    ];

    /*
     * Boat Registration, National License and
     * High Seas modules use their encrypted token.
     */
    if (
        $isTokenizedProcess
        && $token !== ''
    ) {
        $deleteRoute['token'] = $token;
    } else {
        $deleteRoute['id'] = $recordId;
    }
    ?>

    <tr>

        <td>
            <?= Html::encode(
                $fileTypeDescription
            ) ?>
        </td>

        <td>
            <?= Html::a(
                Html::encode($fileName),
                $fileViewUrl,
                [
                    'target' => '_blank',
                    'rel' =>
                        'noopener noreferrer',
                ]
            ) ?>
        </td>

        <td>
            <?= Html::a(
                Yii::t('app', 'Delete'),
                $deleteRoute,
                [
                    'class' =>
                        'btn btn-sm btn-danger',
                    'data' => [
                        'confirm' => Yii::t(
                            'app',
                            'Are you sure you want to delete this?'
                        ),
                        'method' => 'post',
                    ],
                ]
            ) ?>
        </td>

    </tr>

<?php endforeach; ?>

                        <?php if (empty($files)): ?>
                            <tr>
                                <td
                                    colspan="3"
                                    class="text-center text-muted"
                                >
                            <?= Html::encode(
                                Yii::t(
                                    'app',
                                    'No files have been uploaded.'
                                )
                            ) ?>
                        </td>
                            </tr>
                        <?php endif; ?>

                        </tbody>
                </table>
            </div>

            <?php
            $canSubmitExport =
                in_array(
                    $process,
                    [
                        'ExportBedchamber',
                        'ExportChank',
                        'ExportLobster',
                    ],
                    true
                ) &&
                !empty($files) &&
                UserTypeUtil::hasType(Constant::EXPORT_COMPANY);
            ?>

            <?php if ($canSubmitExport && $viewUrl !== ''): ?>

                <?= Html::a(
                    Yii::t('app', 'Submit for Approval'),
                    [
                        '/' . $viewUrl . '/submit',
                        'application' => $recordId,
                    ],
                    [
                        'class' => 'btn btn-warning',
                        'data' => [
                            'confirm' => Yii::t(
                                'app',
                                'Are you sure you want to submit this?'
                            ),
                            'method' => 'post',
                            'params' => [
                                'status-approval' => 'approve',
                                'remarks-approval' =>
                                    'submitted from company',
                            ],
                        ],
                    ]
                ) ?>

            <?php endif; ?>

        </div>
    </div>
</div>

<style>
    .secure-upload-notice {
        margin-bottom: 20px;
        font-size: 14px;
    }

    .secure-file-validation-error {
        display: block;
        width: 100%;
        margin-top: 7px;
        color: #dc3545;
        font-size: 13px;
        font-weight: 600;
    }

    #secure-files-upload-page input[type="file"].is-invalid {
        border-color: #dc3545;
    }

    #secure-files-upload-page input[type="file"].is-valid {
        border-color: #28a745;
    }

    .secure-file-validation-success {
        display: block;
        width: 100%;
        margin-top: 7px;
        color: #198754;
        font-size: 13px;
        font-weight: 600;
    }
</style>

<?php

$fileValidationScript = <<<'JS'
(function () {
    'use strict';

    const page = document.getElementById(
        'secure-files-upload-page'
    );

    if (!page) {
        return;
    }

    const ACCEPT_VALUE =
        '.jpg,.jpeg,.png,.pdf,.doc,.docx,' +
        'image/jpeg,image/png,application/pdf,' +
        'application/msword,' +
        'application/vnd.openxmlformats-officedocument.' +
        'wordprocessingml.document';

    const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'pdf',
        'doc',
        'docx'
    ];

    const ALLOWED_MIME_TYPES = {
        jpg: [
            'image/jpeg',
            'image/pjpeg'
        ],

        jpeg: [
            'image/jpeg',
            'image/pjpeg'
        ],

        png: [
            'image/png'
        ],

        pdf: [
            'application/pdf'
        ],

        doc: [
            'application/msword',
            'application/x-ole-storage',
            'application/cdfv2',
            'application/octet-stream'
        ],

        docx: [
            'application/vnd.openxmlformats-officedocument.' +
                'wordprocessingml.document',
            'application/zip',
            'application/x-zip-compressed',
            'application/octet-stream'
        ]
    };

    function getExtension(fileName) {
        const parts = String(fileName).split('.');

        if (parts.length < 2) {
            return '';
        }

        return parts.pop().toLowerCase();
    }

    function bytesStartWith(bytes, expectedBytes) {
        if (bytes.length < expectedBytes.length) {
            return false;
        }

        return expectedBytes.every(function (value, index) {
            return bytes[index] === value;
        });
    }

    function containsAscii(bytes, text) {
        const expected = Array.from(text).map(function (character) {
            return character.charCodeAt(0);
        });

        for (
            let startIndex = 0;
            startIndex <= bytes.length - expected.length;
            startIndex++
        ) {
            let matched = true;

            for (
                let textIndex = 0;
                textIndex < expected.length;
                textIndex++
            ) {
                if (
                    bytes[startIndex + textIndex] !==
                    expected[textIndex]
                ) {
                    matched = false;
                    break;
                }
            }

            if (matched) {
                return true;
            }
        }

        return false;
    }

    function readFileSlice(file, start, end) {
        return new Promise(function (resolve, reject) {
            const reader = new FileReader();

            reader.onload = function (event) {
                resolve(
                    new Uint8Array(event.target.result)
                );
            };

            reader.onerror = function () {
                reject(
                    new Error('Unable to read the selected file.')
                );
            };

            reader.readAsArrayBuffer(
                file.slice(start, end)
            );
        });
    }

    function bytesToHex(bytes, maximumLength) {
        const outputLength = Math.min(
            bytes.length,
            maximumLength || bytes.length
        );

        return Array.from(
            bytes.slice(0, outputLength)
        )
            .map(function (byte) {
                return byte
                    .toString(16)
                    .padStart(2, '0')
                    .toUpperCase();
            })
            .join(' ');
    }

    function isJpeg(bytes) {
        return bytesStartWith(
            bytes,
            [
                0xFF,
                0xD8,
                0xFF
            ]
        );
    }

    function isPng(bytes) {
        return bytesStartWith(
            bytes,
            [
                0x89,
                0x50,
                0x4E,
                0x47,
                0x0D,
                0x0A,
                0x1A,
                0x0A
            ]
        );
    }

    function isPdf(bytes) {
        return containsAscii(
            bytes,
            '%PDF-'
        );
    }

    function isLegacyWordDocument(bytes) {
        return bytesStartWith(
            bytes,
            [
                0xD0,
                0xCF,
                0x11,
                0xE0,
                0xA1,
                0xB1,
                0x1A,
                0xE1
            ]
        );
    }

    function isZipContainer(bytes) {
        return bytesStartWith(
            bytes,
            [
                0x50,
                0x4B,
                0x03,
                0x04
            ]
        );
    }

    async function isDocx(file, headerBytes) {
        if (!isZipContainer(headerBytes)) {
            return false;
        }

        /*
         * DOCX files are ZIP containers. Check for the Word
         * document entries in the beginning and end of the ZIP.
         */
        const firstChunkSize = Math.min(
            file.size,
            1024 * 1024
        );

        const lastChunkSize = Math.min(
            file.size,
            1024 * 1024
        );

        const firstChunk = await readFileSlice(
            file,
            0,
            firstChunkSize
        );

        const lastChunkStart = Math.max(
            0,
            file.size - lastChunkSize
        );

        const lastChunk = await readFileSlice(
            file,
            lastChunkStart,
            file.size
        );

        const hasContentTypes =
            containsAscii(
                firstChunk,
                '[Content_Types].xml'
            ) ||
            containsAscii(
                lastChunk,
                '[Content_Types].xml'
            );

        const hasWordDirectory =
            containsAscii(
                firstChunk,
                'word/'
            ) ||
            containsAscii(
                lastChunk,
                'word/'
            );

        return hasContentTypes && hasWordDirectory;
    }

    function getMessageBox(input) {
        const boxId =
            input.id + '-secure-validation-message';

        let box = document.getElementById(boxId);

        if (!box) {
            box = document.createElement('div');
            box.id = boxId;

            input.insertAdjacentElement(
                'afterend',
                box
            );
        }

        return box;
    }

    function clearMessage(input) {
        const box = getMessageBox(input);

        box.textContent = '';
        box.className = '';

        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
    }

    function showError(input, message) {
        const box = getMessageBox(input);

        box.textContent = message;
        box.className =
            'secure-file-validation-error';

        input.classList.remove('is-valid');
        input.classList.add('is-invalid');

        input.value = '';
        input.dataset.secureFileValid = 'false';
    }

    function showSuccess(input) {
        const box = getMessageBox(input);

        box.textContent =
            'The selected file type is valid.';

        box.className =
            'secure-file-validation-success';

        input.classList.remove('is-invalid');
        input.classList.add('is-valid');

        input.dataset.secureFileValid = 'true';
    }

    async function validateFile(input, file) {
        const extension = getExtension(file.name);

        if (!ALLOWED_EXTENSIONS.includes(extension)) {
            throw new Error(
                'Only JPG, JPEG, PNG, PDF, DOC and DOCX files are allowed.'
            );
        }

        const allowedMimeTypes =
            ALLOWED_MIME_TYPES[extension] || [];

        if (
            file.type &&
            !allowedMimeTypes.includes(
                file.type.toLowerCase()
            )
        ) {
            throw new Error(
                'The file MIME type does not match the selected file extension.'
            );
        }

        /*
         * Read enough bytes for JPG, PNG, PDF, DOC and ZIP
         * signature validation.
         */
        const headerBytes = await readFileSlice(
            file,
            0,
            Math.min(file.size, 1024)
        );

        console.log(
            file.name + ' hex signature:',
            bytesToHex(headerBytes, 16)
        );

        let validContent = false;

        switch (extension) {
            case 'jpg':
            case 'jpeg':
                validContent = isJpeg(headerBytes);
                break;

            case 'png':
                validContent = isPng(headerBytes);
                break;

            case 'pdf':
                validContent = isPdf(headerBytes);
                break;

            case 'doc':
                validContent =
                    isLegacyWordDocument(headerBytes);
                break;

            case 'docx':
                validContent = await isDocx(
                    file,
                    headerBytes
                );
                break;
        }

        if (!validContent) {
            throw new Error(
                'The actual file content does not match its extension.'
            );
        }

        return true;
    }

    const fileInputs = page.querySelectorAll(
        'input[type="file"]'
    );

    fileInputs.forEach(function (input) {
        /*
         * Restrict the operating-system file chooser.
         */
        input.setAttribute(
            'accept',
            ACCEPT_VALUE
        );

        input.addEventListener(
            'change',
            async function () {
                clearMessage(input);
                input.dataset.secureFileValid = 'false';

                const selectedFiles = Array.from(
                    input.files || []
                );

                if (selectedFiles.length === 0) {
                    return;
                }

                try {
                    for (const file of selectedFiles) {
                        await validateFile(input, file);
                    }

                    showSuccess(input);
                } catch (error) {
                    showError(
                        input,
                        error.message ||
                        'The selected file is invalid.'
                    );
                }
            }
        );
    });
})();
JS;

$this->registerJs(
    $fileValidationScript,
    \yii\web\View::POS_READY
);
?>