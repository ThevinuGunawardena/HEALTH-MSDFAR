<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\AuthItem $model */

$this->title = Yii::t('app', 'Update Permission for role : {name}', [
    'name' => $name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Auth Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $name, 'url' => ['view', 'name' => $name]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <div class="card shadow-sm mb-5">
        <div class="card-body">


            <div class="auth-item-form">

                <?php $form = ActiveForm::begin(['options' => [
                    'class' => 'userform'
                ]]); ?>
                <?php
                $prevName = "";

                foreach ($permissionList as $key => $item) {
                    $iparr = explode("-", $item->name);
                    if ($prevName != $iparr[0]) {
                        echo ' <div class="row"><div class="col-lg-12"><h3>' . ucwords(implode(' ', preg_split('/(?=[A-Z])/', str_replace("Controller", "", $iparr[0])))) . '</h3></div>';
                    }
                    $prevName = $iparr[0];
                    ?>
                    <div class="col-lg-6">
                        <label class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" value="<?= $item->name ?>" name="permisions[]"
                                   class="custom-control-input" <?= str_contains($childString, $item->name) ? "checked" : "" ?>><span
                                    class="custom-control-label"><?= $item->name ?></span>
                        </label>
                    </div>
                    <?php
                    if ($key + 1 < sizeof($permissionList)) {
                        $iparr = explode("-", $permissionList[$key + 1]->name);
                        if ($prevName != $iparr[0]) {
                            echo ' </div><hr>';
                        }
                    } else {
                        echo ' </div>';
                    }

                }
                ?>

            </div>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
</div>


