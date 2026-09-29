<?php

use yii\helpers\Url;

/** @var object $model */
/** @var array $storePlaces */

$storePlaces = is_array($storePlaces ?? null)
    ? $storePlaces
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
                        'storePlaces' => $storePlaces,
                        'isPdf' => false,
                    ]
                ) ?>
            </div>
        </div>
    </div>
</div>