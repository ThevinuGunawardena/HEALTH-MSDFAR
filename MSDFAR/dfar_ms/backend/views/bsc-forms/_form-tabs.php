<?php

/** @var yii\web\View $this */
/** @var string $form current form: 'bsc1' or 'bsc2' */
/** @var string $action current controller action id: 'my-return' or 'district-review' */

use yii\helpers\Html;
?>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <?= Html::a('BSC-1', [$action, 'form' => 'bsc1'], [
            'class' => 'nav-link' . ($form === 'bsc1' ? ' active' : ''),
        ]) ?>
    </li>
    <li class="nav-item">
        <?= Html::a('BSC-2', [$action, 'form' => 'bsc2'], [
            'class' => 'nav-link' . ($form === 'bsc2' ? ' active' : ''),
        ]) ?>
    </li>
</ul>