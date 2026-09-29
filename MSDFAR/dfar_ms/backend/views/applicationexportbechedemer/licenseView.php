<?php

use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var backend\models\Applicationexportbechedemer $model
 * @var backend\models\ExportBecheDemerConsignment[] $consignments
 * @var array $officer
 * @var Closure $imageSrc
 */

$officer = is_array($officer ?? null)
    ? $officer
    : [];
?>

<style>
    *,
    *::before,
    *::after {
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
                        'consignments' => $consignments,
                        'isPdf' => false,
                        'officer' => $officer,
                        'imageSrc' => $imageSrc ?? null,
                    ]
                ) ?>
            </div>
        </div>
    </div>
</div>