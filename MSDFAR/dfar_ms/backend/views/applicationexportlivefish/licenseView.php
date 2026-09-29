<?php

use yii\helpers\Url;

/** @var object $model */
/** @var array $qties */
/** @var array $statements */

$qties = is_array($qties ?? null)
    ? $qties
    : [];

$statements = is_array($statements ?? null)
    ? $statements
    : [];

?>

<style>
    *, *::before, *::after {
        box-sizing: unset;
    }
</style>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <a
                href="<?= Url::to([
                    'license-download',
                    'id' => $model->id,
                ]) ?>"
                class="btn btn-success"
            >
                Download
            </a>
        </div>
    </div>
</div>

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="license-view">
                <?= $this->render(
                    'license',
                    [
                        'model' => $model,
                        'qties' => $qties,
                        'statements' => $statements,
                        'isPdf' => false,
                    ]
                ) ?>
            </div>
        </div>
    </div>
</div>