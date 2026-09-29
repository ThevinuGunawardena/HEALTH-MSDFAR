<?php

use backend\services\CommonService;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\config\Constant;

/** @var yii\web\View $this */
/** @var backend\models\SkipperSearch $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="skipper-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => [$action],
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">

        <div class="col-xl-6">
            <div class="form-group field-reportSearch-from">
                <label class="control-label" for="reportSearch-from">From</label>
                <input type="date" id="reportSearch-from" class="form-control" name="from" value="<?= $from ?>">
            </div>
        </div>
        <div class="col-xl-6">
            <div class="form-group field-reportSearch-to">
                <label class="control-label" for="reportSearch-to">To</label>
                <input type="date" id="reportSearch-to" class="form-control" name="to" value="<?= $to ?>">
            </div>
        </div>
        <div class="col-xl-6">
            <?php if ($district != "NA") { ?>
            <div class="form-group">
                <label>District</label>
                <select name="district" class="form-control">
                    <option value="All">All</option>
                    <?php foreach (CommonService::getFIDistrictArray() as $index => $param) {
                        $selected = $index == $district ? "selected=selected" : " ";
                        echo '<option ' . $selected . ' value="' . $index . '">' . $param . '</option>';
                    } ?>
                </select>
            </div>
            <?php } ?>
        </div>
        <?php if ($action === 'boat-registration') { ?>
            <div class="col-xl-6">
                <div class="form-group">
                    <label for="boat-type">Boat Type</label>

                    <select name="boat_type" id="boat-type" class="form-control">
                        <option value="">All</option>

                        <?php foreach (CommonService::getBoatCategoriesArray() as $index => $param) {
                            $selected = $index == $boat_type ? "selected=selected" : "";
                            echo '<option ' . $selected . ' value="' . $index . '">' . $param . '</option>';
                        } ?>
                    </select>
                </div>
            </div>
        <?php } ?>

        <?php if ($action === 'boat-registration') { ?>
    <div class="col-xl-6">
        <div class="form-group">
            <label for="reg_type">First Registration / Renew</label>

            <select name="reg_type" id="reg_type" class="form-control">
                <option value="">All</option>
                <option value="0" <?= $reg_type === '0' || $reg_type === 0 ? 'selected' : '' ?>>
                    First Registration
                </option>
                <option value="1" <?= $reg_type === '1' || $reg_type === 1 ? 'selected' : '' ?>>
                    Renew
                </option>
            </select>
        </div>
    </div>
<?php } ?>
        <?php if ($action === 'boat-registration' || $action === 'fishermen-registration-printed') { ?>

<div class="col-xl-6">
    <div class="form-group">
        <label class="control-label" for="license-status">Status</label>

        <select name="status" id="license-status" class="form-control">
            <option value="">All</option>

            <?php foreach (Constant::$licenseStatus as $index => $param) { ?>
                <option value="<?= $index ?>" <?= (string)$index === (string)$status ? 'selected' : '' ?>>
                    <?= $param ?>
                </option>
            <?php } ?>
        </select>
    </div>
</div>
       <?php } ?>
 
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-primary']) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
