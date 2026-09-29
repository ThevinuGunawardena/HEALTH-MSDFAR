<?php

use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/** @var yii\web\View $this */

$profileId = (int) (Yii::$app->user->identity->profile_id ?? 0);

$scientificJsConfig = [
    'storageKey' => 'sss' . $profileId,
    'profileId' => $profileId,
    'csrfParam' => Yii::$app->request->csrfParam,
    'csrfToken' => Yii::$app->request->getCsrfToken(),
    'routes' => [
        'index' => Url::to(['/scientific/index']),
        'create' => Url::to(['/scientific/create']),
        'addSampleCraft' => Url::to(['/scientific/addsamplecraft']),
        'catchData' => Url::to(['/scientific/catchdata']),
        'addLengthWeight' => Url::to(['/scientific/addlengthweight']),
        'addOperationCost' => Url::to(['/scientific/addoperationcost']),
        'gearTypeList' => Url::to(['/gear-type/list']),
        'fishTypeList' => Url::to(['/fish-types/list']),
        'districtList' => Url::to(['/fi-district/list']),
        'selectedEnumerationData' => Url::to(['/enumeration/selected-data']),
        'divisionByDistrict' => Url::to(['/division/list-by-district']),
        'landingSiteByDivision' => Url::to(['/landing-site/list-by-division']),
        'boatTypeList' => Url::to(['/boat-types/list']),
        'boatCategoryList' => Url::to(['/boat-category/list']),
        'boatSubCategoryList' => Url::to(['/boat-sub-category/list']),
        'gearExtraDataList' => Url::to(['/gear-type-extra-data/list-by-gear']),
        'fisheryTypeList' => Url::to(['/fishery-types/list']),
        'districtCodeList' => Url::to(['/fi-district/list-code']),
    ],
];

$this->registerJs(
    'window.scientificConfig = '
        . Json::htmlEncode($scientificJsConfig)
        . ';',
    View::POS_HEAD
);
