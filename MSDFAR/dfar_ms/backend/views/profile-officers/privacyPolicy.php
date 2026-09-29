<?php

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\services\CommonService;
use backend\services\Util;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\ProfileFisherman $model */

// $this->title = "Fisherman : " . $model->fisherman_uid . " " . $model->first_name . " " . $model->last_name;
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Fishermen'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$webURL = Yii::getAlias('@web');

?>
 <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        h1, h2 {
            color: #2c3e50;
        }
        ul {
            margin-left: 20px;
        }
    </style>
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

    <body>

<h1>Privacy Policy - Department of Fisheries and Aquatic Resources</h1>

<h2>Commitment to Privacy</h2>
<p>
The Department of Fisheries and Aquatic Resources (DFAR) is dedicated to safeguarding your privacy. 
This privacy policy applies to the DFAR website and its services, detailing how personal information is collected, used, and shared.
</p>

<h2>Collection of Personal Information</h2>
<p>DFAR collects personal information in several ways:</p>

<ol>
    <li><strong>Data You Provide:</strong>
        <ul>
            <li>Information such as your name, contact details, and identification numbers when creating accounts or making transactions.</li>
            <li>Data related to purchases, shared content, and any additional details provided during interactions.</li>
        </ul>
    </li>
    <li><strong>Automatic Data Collection:</strong>
        <ul>
            <li>Transaction details and interactions with services.</li>
            <li>Location data from devices and cookies, including usage statistics and device-related information.</li>
        </ul>
    </li>
    <li><strong>Social Network Data:</strong>
        <ul>
            <li>Information from social networks if you connect your account to them.</li>
        </ul>
    </li>
</ol>

<h2>Use of Personal Information</h2>
<ul>
    <li>Processing orders and delivering products.</li>
    <li>Informing you about services and conducting surveys.</li>
    <li>Ensuring confidentiality and not selling or leasing your data to third parties.</li>
</ul>

<h2>Legal Compliance</h2>
<p>
Personal information may be accessed or disclosed if required by law or to protect the rights and safety of DFAR or its users.
</p>

<h2>Data Processing Purposes and Legal Bases</h2>
<ul>
    <li>To fulfill contracts and provide services.</li>
    <li>To comply with legal obligations and prevent illegal activities.</li>
    <li>To protect vital interests of individuals.</li>
</ul>

<h2>Data Storage and Retention</h2>
<p>
Personal data is stored as long as necessary for processing purposes and in compliance with legal obligations.
</p>

<h2>Control of Personal Information</h2>
<ul>
    <li>Users can control how their data is collected, used, and shared via email at <a href="mailto:elog.dfar@gmail.com">elog.dfar@gmail.com</a>.</li>
    <li>Users may unsubscribe from promotional emails and request copies of their personal data (subject to a small fee under the Data Protection Act 1998).</li>
    <li>Users can request corrections to inaccurate or incomplete information.</li>
    <li>Rights include access, rectification, erasure, restriction of processing, and data portability.</li>
</ul>

<h2>Request for Erasure</h2>
<ul>
    <li>Users may request deletion of their personal data unless exceptions apply (e.g., legal obligations, fraud prevention).</li>
    <li>Requests can be made via email or mail to the DFAR office.</li>
</ul>

<h2>Use of Cookies</h2>
<ul>
    <li>Cookies enhance user experience and analyze website traffic.</li>
    <li>Users can accept or decline cookies via browser settings.</li>
    <li>Cookies may be used for Google Analytics and remarketing without identifying individuals.</li>
</ul>

<h2>Security of Personal Information</h2>
<ul>
    <li>DFAR uses encryption, firewalls, and other measures to protect personal data.</li>
    <li>Sensitive data is transmitted securely and stored in controlled facilities.</li>
</ul>

<h2>User Responsibilities</h2>
<ul>
    <li>Users should be aware that shared data may be visible to others.</li>
    <li>Users must comply with data protection laws when handling others' information.</li>
</ul>

<h2>Children's Privacy</h2>
<p>
DFAR services are not intended for children, and no personal data is knowingly collected from children under national laws.
</p>

<h2>Session Management</h2>
<ul>
    <li>Users may stay signed in but should avoid doing so on shared or public computers.</li>
    <li>Signing out and clearing cookies after use is recommended.</li>
</ul>

<h2>Changes to the Privacy Statement</h2>
<p>
DFAR may update this privacy policy periodically. Material changes will be posted prominently on the website before implementation. 
Users are encouraged to review the policy regularly.
</p>

</body>
</div>



