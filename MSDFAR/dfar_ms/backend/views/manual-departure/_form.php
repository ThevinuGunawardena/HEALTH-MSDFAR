<?php

use backend\config\Constant;
use backend\models\FishermanRegisterdBoat;
use backend\models\Skipper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\DepartureRequests $model */
/** @var yii\widgets\ActiveForm $form */

$boatDat = FishermanRegisterdBoat::find()->andWhere(['id' => $model->boat_no])->one();
if (isset($boatDat)) {
    $boatDataS[$boatDat->id] = $boatDat->boatNumber->boat_number;

} else {
    $boatDataS = [];
}
$ownerDat = Skipper::find()->andWhere(['id' => $model->skipper])->one();
if (isset($ownerDat)) {
    $ownerData[$ownerDat->id] = "ID-" . $ownerDat->fisherman->fisherman_uid . " - NIC: " . $ownerDat->fisherman->nic;

} else {
    $ownerData = [];
}
?>
<?php $this->registerJs("
$('#btn-submit-crew').click(function(e) {
    e.preventDefault(); // stop normal submit
    
    if (!validateCrewNic()) {
        return false;
    }
    
      swal({
        title: 'Are you sure?',
        text: 'Do you want to submit this!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes, submit!',
        cancelButtonText: 'Cancel',
        closeOnConfirm: false   // ← important
    },
    function(isConfirm){
        if (isConfirm) {
            $('#w0').submit();
        }
    });
});
") ?>
<div class="departure-requests-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">

                <?= $form->field($model, 'boat_no')->textInput(['maxlength' => true, 'readOnly' => $readOnly, 'style' => 'text-transform: uppercase;'])->label(
                        '1. පිටත්වෙන්න බලාපොරොත්තුවන බෝට්ටුවේ IMUL අංකය පහතින් ටයිප් කරන්න
                           <br>  1. புறப்பட எதிர்பார்த்திருக்கும் வள்ளத்தின் IMUL எண்ணைக் குறிப்பிடுக
                           <br>  1. Please type the IMUL number of the boat that is expected to depart below'
                ) ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-danger" style="display: none" id="boatAlterts">


                        </div>
                    </div>
                </div>
                <!--    --><?php //= $form->field($model, 'boat_no')->textInput(['maxlength' => true]) ?>
                <!--                --><?php //= $form->field($model, 'boat_no')->widget(Select2::classname(), [
                //                    'data' => $boatDataS,
                //
                //                    'options' => ['placeholder' => 'Search ...'],
                //                    'pluginOptions' => [
                //                        'allowClear' => false,
                //
                //                        'minimumInputLength' => 3,
                //                        'language' => [
                //                            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                //                        ],
                //                        'ajax' => [
                //                            'url' => "../boat-registration/search-global",
                //                            'dataType' => 'json',
                //                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                //                        ],
                //                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                //                        'templateResult' => new JsExpression('function(boat_number) { return boat_number.text; }'),
                //                        'templateSelection' => new JsExpression('function (boat_number) { return boat_number.text; }'),
                //                    ],
                //                ]); ?>
                <?= $form->field($model, 'boat_name')->textInput(['maxlength' => true, 'readOnly' => $readOnly, 'style' => 'text-transform: capitalize;'])
                        ->label(
                                '1.(A). යාත්‍රාවේ නම මෙහි සදහන් කරන්න (උදා:- සුනෙත් පුතා) <br>
1.(A). படகின் பெயரை இங்கு குறிப்பிடவும் (உதா: சுனேத் புத்தா)<br>
1.(A). Please mention the name of the vessel here (eg:- Suneth Putha)') ?>
            </div>
        </div>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <?= $form->field($model, 'owner')->textInput(['maxlength' => true, "readOnly" => true, 'style' => 'text-transform: capitalize;'])->label('2. ඒ බෝට්ටුවේ අයිතිකරුගේ නම <br>
2. படகு உரிமையாளரின் பெயரைக் குறிப்பிடுக (முதலெழுத்துக்களுடன் ஆங்கிலத்தில் தட்டச்சு செய்க)<br>
2. Please type the vessel owner\'s name with initials') ?>

                <?= $form->field($model, 'contact_no')->textInput(['maxlength' => true, 'readOnly' => $readOnly])->label('3. අයිතිකරු සම්බන්ද කර ගත හැකි වැඩ කරන දුරකථන අංකයක් <br> 
3. உரிமையாளரைத் தொடர்பு கொள்ளக்கூடிய செயல்பாட்டிலிருக்கும் தொலைபேசி எண்<br>
3. Contact number of the vessel owner') ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true, "readOnly" => true])->label('4. අයිතිකරුගේ හෝ අයිතිකරු විසින් ලබාදී ඇති අයෙකුගේ ඊ මේල් ලිපිනය (මෙම පිටත්වීම් ඉල්ලීම අනුමත කර එම ඊමේල් ලිපිනයට පණිවුඩයක් එවනු ඇත) <br>
4. உரிமையாளரின் அல்லது உரிமையாளரால் கொடுக்கப்பட்டிருக்கும் நபரின் மின்னஞ்சல் முகவரி (புறப்படும் கோரிக்கைக்கு கிடைக்கும் அனுமதியானது இம்மின்னஞ்சல் முகவரிக்கு அனுப்பப்படும்)<br>
4. The owner\'s or an authorized person\'s email address (A message will be sent to this email address once this request is approved)') ?>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <?= $form->field($model, 'skipper_nic')->textInput(['maxlength' => true, 'readOnly' => $readOnly])->label('5. නියමුවාගේ ජාතික හැදුනුම්පත් අංකය 
<br> 5. பிரதானியின் தேசிய அடையாள அட்டை இலக்கம் 
<br> Skipper\'s NIC/ Passport number') ?>

                <?= $form->field($model, 'skipper_no')->textInput(['maxlength' => true, 'readOnly' => $readOnly])->label('6. නියමු අංකය 
<br> 6. பிரதானியின் உரிம எண்
<br> 6. Skipper number') ?>
                <?= $form->field($model, 'skipper')->textInput(['maxlength' => true, 'readOnly' => $readOnly])->label('7. ඔබේ නියමුවාගේ නම, 
                <br> 7. உங்களது வள்ளத்தின் பிரதானியின் (Skipper) பெயர்
                <br> 7. Your skipper\'s name') ?>

            </div>
        </div>
    </div>
    <!--    --><?php //= $form->field($model, 'district')->textInput(['maxlength' => true]) ?>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <?= $form->field($model, 'harbor')->dropDownList(Constant::$departureHarbours, ["prompt" => "Please select", 'maxlength' => true, 'disabled' => $readOnly])->label('8. බෝට්ටුව පිටත්වෙන්න බලාපොරොත්තුවන වරාය (ලිස්ට් එකෙන් එකක් තෝරන්න) 
<br> 8. வள்ளம் புறப்பட எதிர்பார்த்திருக்கும் துறைமுகம் (பின்வரும் பட்டியலிலிருந்து ஒன்றைத் தெரிவு செய்க)
<br> 8. The port from which the boat is expected to depart') ?>

                <?= $form->field($model, 'fishing_area')->dropDownList([0 => "දේශීය මුහුද / தேசிய கடற்பரப்பு /  EEZ", 2 =>
                        "අන්තර්ජාතික 
                මුහුද / சர்வதேச கடற்பரப்பு / High Seas"], ["prompt" => "Please select"])->label('9. ධීවර මෙහෙයුම අතරදී මසුන් බාන ප්‍රදේශය 
<br> 9. மீன்பிடி செயல்பாட்டின் போது மீன்கள் பிடிக்கப்படும் பகுதி
<br> 9. The fishing area during the fishing operation.') ?>

            </div>
        </div>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <h4> 10. අරං යන අම්පන්න විස්තරය <br>
                            10. கொண்டு செல்லும் வலை தொடர்பான விபரங்கள் <br>
                            10. Details of the fishing gear</h4>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'length_longline')->textInput()->label('A. මරුවැලේ දිග (මීටර්) 
                        <br> A. தூண்டில் கயிற்றின் நீளம்
                        <br> A. Length of the longline (meters)') ?>

                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'longline_hooks')->textInput()->label('කටු ගණන 
<br> தூண்டில்களின் எண்ணிக்கை
<br> Number of hooks in long line') ?>


                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'length_ringnet')->textInput()->label('B. කරමල් දැලේ දිග (කි.මී) 
                        <br> B. செவிழ் வலையின் நீளம்
                        <br> B. Length of the gill net (kilometers)') ?>

                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'mesh_gillnet')->textInput()->label('ඇස් ප්‍රමාණය (අගල්) 
<br> வலையின் கண்ணளவு
<br> Mesh size in gill net (inches') ?>

                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'length_gillnet')->textInput()->label('C. හැබිලි දැලේ දිග (මීටර්) 
                        <br> C. சுருக்கு வலையின் நீளம்
                        <br> Length of the ring net (meter)') ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'mesh_ringnet')->textInput()->label('ඇස් ප්‍රමාණය (අගල්) 
<br> வலையின் கண்ணளவு
<br> Mesh size in ring net (inches)') ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <div class="row">
                    <div id="" class="col-lg-12 ">
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <h4>11. බෝට්ටුවේ යන ගැනියන්ගේ විස්තර (යන ගැනියන්ගේ ගණනට විතරක් පුරවන්න)<br>
                                    11. வள்ளத்தில் பயணிப்போரின் விபரங்கள் (பயணிப்போரின் எண்ணிக்கைக்கு ஏற்ப மட்டும்
                                    நிரப்பவும்)<br>
                                    11. Details about the crew members. (Fill only according to the number of crew
                                    members departing.)</h4>
                            </div>
                        </div>
                    </div>
                    <div id="crewMemberContainer" class="col-lg-12 ">
                        <INPUT type="hidden" id="memberCount" value="6">
                        <?php
                        if (!isset($crews) || sizeof($crews) <= 5) {
                            $size = 5;
                        } else {
                            $size = sizeof($crews);
                        }
                        for ($i = 1; $i <= $size; $i++) { ?>
                            <div class="row mb-3 crew1">
                                <div class="col-lg-1"> <?= $i ?></div>
                                <div class="col-lg-5">
                                    <input class="form-control crewNic" id="crewnic<?= $i ?>"
                                           value="<?= $crews[$i - 1]->nic ?? '' ?>"
                                           name="crewNIC[]"
                                           placeholder="NIC">
                                </div>
                                <div class="col-lg-6">
                                    <input class="form-control" id="crewname<?= $i ?>" name="crewName[]"
                                           value="<?= $crews[$i - 1]->name ?? '' ?>" placeholder="Name">
                                </div>
                                <hr>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="col-lg-12">
                        <div class="row mb-3">
                            <div class="col-lg-8">
                            </div>
                            <div class="col-lg-4">
                                <button id="crewMemberAdd" type="button" class="btn-primary btn btn-block">Add Crew
                                    member
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <?= $form->field($model, 'national_license_no')->textInput(['maxlength' => true, 'readOnly' => $readOnly])->label('12. යාත්‍රාවේ දේශීය මෙහෙයුම් බලපත්‍ර අංකය 
<br> 12. வள்ளத்தின் தேசிய செயல்பாடுகளுக்கான உரிம எண்
<br> 12. EEZ license number of the vessel') ?>
                <?= $form->field($model, 'hs_license_no')->textInput(['maxlength' => true, 'readOnly' => $readOnly])->label('13. යාත්‍රාවේ අන්තර්ජාතික මෙහෙයුම් බලපත්‍ර අංකය(තිබේ නම් පමණි) 
<br> 13. வள்ளத்தின் சர்வதேச செயல்பாடுகளுக்கான உரிம எண் (இருந்தால் மாத்திரம் குறிப்பிடவும்)
<br> 13. High Seas license number of the vessel (if available) ') ?>
            </div>
        </div>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <?= $form->field($model, 'mcs')->dropDownList([1 => "Yes", 0 => "No"], ["prompt" => "Select"])->label('14. SSB රේඩියෝ යන්ත්‍රය <br> 14. படகிலுள்ள SSB ரேடியோ <br> 14. SSB Radio') ?>


                <?= $form->field($model, 'vms')->dropDownList([1 => "Yes", 0 => "No"], ["prompt" => "Select"])->label('15. මාගේ යාත්‍රාවේ VMS උපකරණයක් තියන අතර මාගේ දැනුමේ හැටියට එය ක්‍රියාත්මක තත්වයේ තිබේ. <br> 15. எனது வள்ளத்தில் ஒரு VMS சாதனம் பொருத்தப்பட்டுள்ளது. என் அறிவுக்கு எட்டியவரை அது செயல்படும் நிலையிலேயே உள்ளது. <br> 15. I have a VMS device on my vessel, and to my knowledge, it is in operational condition.') ?>

                <!--                --><?php //= $form->field($model, 'vms_code')->textInput(['maxlength' => true])->label('16. VMS ගෙවීම් කේතය <br> 16. VMS கொடுப்பனவு குறியீடு
                //            ') ?>

            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12  col-sm-12">
                        <label>ගමන් වාරය අනුමැත වුවුහොත් ඉහත ලබාදුන් ඊමේල් ලිපිනයට පණිවුඩයක් ලැබෙනු ඇත. එහි පිටපත්
                            දෙකක් මුද්&zwj;රණය
                            කරගෙන වරායේ ආරක්ෂක අංශයට සහ වෙරළාරක්ෂක බලකායට ලබාදෙන්න.</label><br>

                        <br><h4><b><font color="purple">"මියන්මාර අනවසර සංක්&zwj;රමණිකයින් මුහුදේදී දුටුවහොත් එම තොරතුරු
                                    ගුවන්විදුලි
                                    මධ්&zwj;යස්ථානයකට හෝ පහත අංකයට කථා කොට දැනුවත් කිරීමට නියමුවන් දැනුවත්
                                    කරන්න."</font></b></h4>
                        <h4><b><font color="purple">011-2445368/ 011-2329109</font></b></h4><br><br>

                        <h4><b><font color="purple"> ඔබ දියගො-ගර්සියා (BIOT) මුහුදු කලාපය හරහා ගමන් කිරීමට අදහස් කරන්නේ
                                    නම්
                                    අනිවාර්යයෙන් පහත උපදෙස් අනුව අනිවාර්යයෙන් කටයුතු කල යුතුය.
                                    <br>
                                    <!-- <a href="http://www.fisheriesdept.gov.lk/web/index.php?option=com_content&view=article&id=513&Itemid=323&lang=en" 	target="_blank"><b>උපදෙස් සබැදිය</b> </a></b></h4><br> -->
                                    <a href="https://www.fisheriesdept.gov.lk/innocent-passage/" target="_blank"><b>උපදෙස්
                                            සබැදිය</b> </a></font></b></h4><font color="red"><br>
                        </font></div>
                    <hr>
                    <div class="col-md-12 col-sm-2">
                        <label>கோரிக்கையானது அங்கீகரிக்கப்பட்டால், மேலே வழங்கப்பட்ட மின்னஞ்சல் முகவரிக்கு அது
                            தொடர்பான
                            செய்தியைப் பெறுவீர்கள். அதன் இரண்டு நகல்களை அச்சிட்டு துறைமுகத்தின் காவலர் பிரிவுக்கு
                            மற்றும் கடலோர
                            காவல்படையிடமும் ஒப்படைக்கவும்.</label><br>

                        <br><h4><b><font color="purple">"மியன்மார் சட்டவிரோத குடியேற்றவாசிகள் கடலில் காணப்பட்டால்,
                                    வானொலி
                                    நிலையத்திற்கோ
                                    அல்லது கீழே கொடுக்கப்பட்டுள்ள தொலைபேசி இலக்கத்திற்கோ தகவல் தெரிவிக்குமாறு
                                    படகோட்டிகளுக்கு
                                    அறியத்தரவும்."</font></b></h4>
                        <h4><b><font color="purple">011-2445368/ 011-2329109</font></b></h4><br><br>

                        <h4><b><font color="purple"> நீங்கள் டியாகோ-கார்சியா (BIOT) கடல் மார்க்கம் வழியாக பயணம்
                                    செய்யவேண்டி
                                    இருப்பின்,
                                    கீழே உள்ள அறிவுறுத்தல்களை கண்டிப்பாக பின்பற்ற வேண்டும்.

                                    <br><br>
                                    <!-- <a href="http://www.fisheriesdept.gov.lk/web/index.php?option=com_content&view=article&id=513&Itemid=323&lang=en" 	target="_blank"><b>அறிவுறுத்தலுக்கான இணைப்பு</b></a></b></h4><br> -->

                                    <a href="https://www.fisheriesdept.gov.lk/innocent-passage/" target="_blank"><b>அறிவுறுத்தலுக்கான
                                            இணைப்பு</b></a></font></b></h4><font color="red"><br>


                        </font>
                    </div>
                    <hr>
                    <div class="col-md-12 col-sm-2">
                        <label>If the departure permit is approved, you will receive a message at the email address
                            provided above. Print two copies of it and submit them to the port security unit and the
                            coast guard.</label><br>

                        <br><h4><b><font color="purple">"If you spot unauthorized Myanmar migrants at sea, inform the
                                    relevant authorities by contacting a radio station or calling the following
                                    number"</font></b></h4>
                        <h4><b><font color="purple">011-2445368/ 011-2329109</font></b></h4><br><br>

                        <h4><b><font color="purple">If you intend to travel through the Diego Garcia (BIOT) maritime
                                    zone, you must strictly follow the guidelines below.

                                    <br><br>
                                    <!-- <a href="http://www.fisheriesdept.gov.lk/web/index.php?option=com_content&view=article&id=513&Itemid=323&lang=en" 	target="_blank"><b>அறிவுறுத்தலுக்கான இணைப்பு</b></a></b></h4><br> -->

                                    <a href="https://www.fisheriesdept.gov.lk/innocent-passage/" target="_blank"><b>Instructions
                                            link</b></a></font></b></h4><font color="red"><br>


                        </font>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <?= $form->field($model, 'agree')->checkbox(['maxlength' => true])->label('17. මම මේ මුහුදු ගමනට මගේ බෝට්ටුවේ ලියාපදිංචි පොත, මෙහෙයුම් බලපත්‍ර, වලංගු රක්ෂණ සහතිකයක්, ලොග් පොත, ගිනි නිවන උපකරණ, කෝල් සයින් තියන රේඩියෝ එකක්, ජීවිතාරක්ෂක කබා, ඉලක්ක නොකරන මසුන් හසුවූ විට බේරා හරින උපකරණ යාත්‍රාවේ රැගෙන යන බවටත්, කිසිදු නීති විරෝධී පන්නයක් හෝ උපකරණයක් යාත්‍රාවේ රැගෙන නොයන බවත්, ජාතික ආරක්ෂාවට හෝ රටේ ජනතාවගේ සෞඛ්‍යට හානිකර කිසිදු ක්‍රියාකාරකමක නොදෙන බවටත් මෙයින් පොරොන්දු වෙමි.
එමෙන්ම යාත්‍රා නිරීක්ෂණ රෙගුලාසි අනුව (වැඩි විස්තර) බහුදින ධීවර යාත්‍රාවක් මුහුදේ ක්‍රියාත්මක වීමට යාත්‍රා නිරීක්ෂණ පද්ධතියක් අනිවාර්ය බවත්, මාගේ යාත්‍රාවේ සවිකර ඇති යාත්‍රා නිරීක්ෂණ උපකරණ සක්‍රියව පවත්වා ගැනීම මගේ අනිවාර්ය වගකීම බව දන්නා බවත්, යාත්‍රා නිරීක්ෂණ පද්ධති උපකරණ ගැලවීම, ඉවත් කිරීම, ක්‍රියා විරහිත කිරීම (හෝ ට්‍රිප් ස්විචය සක්‍රියව තබා නොගැනීම මගින් උපකරණය ක්‍රියාවිරහිත වීමට සැලැස්වීම), වයර් කැපීම හෝ උපකරණය සම්පුර්ණයෙන් වැසෙන සේ ආවරණය කිරීම නීතියෙන් දඩුවම් ලැබිය හැකි වරදක් බව මා දන්නා බවට ද මෙයින් සනාථ කරමි.
මාගේ යාත්‍රාවේ VMS යන්ත්‍රය සක්‍රීයව තිබෙන බවත්, එහි ආරක්ෂක පේනුව (Switch) ක්‍රියාත්මක තත්ත්වයේ පවතින බවත් එය හානි වුවහොත් ප්‍රතිස්ථාපනය කිරීමට අමතර ෆියුස් (Fuse) යාත්‍රාව සතුව ඇති බව මෙයින් සහතික කරමි. <br> <br> 
17. எனது இந்தக் கடற்பயணத்தின் போது வள்ளத்தின் பதிவுப் புத்தகம், செயல்பாட்டிற்கான அனுமதி/ உரிமம், செல்லுபடியாகும் காப்பீட்டு சான்றிதழ், பதிவேடு (லொக் புத்தகம்), தீயணைப்புக் கருவி, Call Sign இருக்கும் ரேடியோ ஒன்று, உயிர் காக்கும் மேலங்கி (Life Jacket), எதிர்பாரா மீனினங்கள் அகப்படும்போது அவற்றை விடுவிப்பதற்காக தேவையான உபகரணங்கள் வள்ளத்தில் எடுத்துச் செல்லப்படுகின்றன எனவும் எந்தவொரு சட்டவிரோதமான வலைகளோ அல்லது உபகரணங்களோ வள்ளத்தில் கொண்டுசெல்லப்படவில்லை எனவும் தேசிய பாதுகாப்புக்கும் மக்களின் ஆரோக்கியத்திற்கு தீங்கு விளைவிக்கும் எந்தவொரு செயலிலும் ஈடுபட மாட்டோம் எனவும் நான் உறுதியளிக்கிறேன்.
படகு கண்காணிப்பு விதிமுறைகளின்படி (மேலும் விபரங்கள்) கடலில் பல நாள் மீன்பிடிப் படகை இயக்குவதற்கு படகு கண்காணிப்பு அமைப்பு கட்டாயம் என்பதையும், எனது படகில் நிறுவப்பட்டுள்ள படகு கண்காணிப்பு கருவிகளைப் பராமரிப்பது எனது கட்டாயப் பொறுப்பு என்பதையும், படகு கண்காணிப்பு அமைப்பு உபகரணங்களை கழற்றுதல், அகற்றுதல், செயலிழக்கச் செய்தல் (அல்லது ட்ரிப் ஸ்விட்சை அணைப்பதன் மூலம் சாதனத்தை செயலிழக்கச் செய்தல்), வயர்களை வெட்டுதல் அல்லது சாதனத்தை முழுவதுமாக மூடுவது சட்டப்படி தண்டனைக்குரிய குற்றமாகும் என்பதையும் நான் அறிவேன் என்பதை இதன்மூலம் உறுதி செய்கிறேன்.
எனது படகில் VMS அமைப்பு செயலில் உள்ளது என்றும், அதன் பாதுகாப்பு சுவிட்ச் (Switch) செயல்படும் நிலையில் உள்ளது என்றும், அது சேதமடைந்தால் மாற்றுவதற்கு படகில் மேலதிக ஃப்யூஸ் (Fuse) உள்ளன என்றும் இதன் மூலம் சான்றளிக்கிறேன்.<br><br>
17. I hereby confirm that for this sea voyage, I will be carrying on my boat the registration book, operational license, valid insurance certificate, logbook, fire extinguishing equipment, a radio with a call sign, life jackets, and equipment for safely releasing unintendedly caught fish. I also declare that I am not carrying any illegal items or equipment on board and that I will not engage in any activities that are harmful to national security or the health of the country’s people.<br><br>') ?>
            </div>
        </div>
    </div>
    <!--    --><?php //= $form->field($model, 'req_date_time')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //= $form->field($model, 'user')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //= $form->field($model, 'action_date')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //= $form->field($model, 'approve')->textInput(['maxlength' => true]) ?>
    <?php if ($update) { ?>
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

            <div class="card shadow-sm mb-5">
                <div class="card-body">
                    <?= $form->field($model, 'remarks')->textarea(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <!--    --><?php //= $form->field($model, 'water_bot')->textInput(['maxlength' => true]) ?>


    <!--    --><?php //= $form->field($model, 'frequency')->textInput(['maxlength' => true]) ?>


    <!--    --><?php //= $form->field($model, 'manual')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //= $form->field($model, 'arrivalPort')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //= $form->field($model, 'arrivalDate')->textInput(['maxlength' => true]) ?>

    <!--    --><?php //= $form->field($model, 'arrTime')->textInput(['maxlength' => true]) ?>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <?= $form->field($model, 'req_date_time')->textInput(['maxlength' => true, "type" => "datetime-local"]) ?>


            </div>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn 
        btn-success', 'id' => 'btn-submit-crew']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
