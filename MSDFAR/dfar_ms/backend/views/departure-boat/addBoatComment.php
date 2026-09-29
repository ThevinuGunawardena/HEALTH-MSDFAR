<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var string $boatNumber */
/** @var string $token */

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Departure Boats'),
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Boat Details'),
    'url' => ['view', 'token' => $token],
];

$this->params['breadcrumbs'][] =
    Yii::t('app', 'Add Comment');
?>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">

            <?php $form = ActiveForm::begin([
                'action' => [
                    '/departure-boat/add-boat-comment',
                    'token' => $token,
                ],
                'options' => [
                    'enctype' => 'multipart/form-data',
                ],
            ]); ?>

            <div class="mb-3">
                <label class="form-label">
                    Boat Number
                </label>

                <input
                    type="text"
                    value="<?= Html::encode($boatNumber) ?>"
                    class="form-control"
                    readonly
                >
            </div>

            <div class="mb-3">
                <label class="form-label" for="boat-comment">
                    Comment
                </label>

                <textarea
                    id="boat-comment"
                    name="comment"
                    class="form-control"
                    rows="4"
                    maxlength="2000"
                    required
                    placeholder="Enter your comment..."
                ></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" for="support-doc">
                    Add Supporting Documents
                </label>

                <input
                    id="support-doc"
                    type="file"
                    name="support_doc[]"
                    class="form-control"
                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                    multiple
                >

                <small class="text-muted">
                    PDF, JPG, PNG, DOC and DOCX files. Maximum 10 MB per file.
                </small>
            </div>

            <div class="text-end">
                <?= Html::submitButton(
                    Yii::t('app', 'Submit'),
                    [
                        'class' => 'btn btn-primary px-4',
                    ]
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>