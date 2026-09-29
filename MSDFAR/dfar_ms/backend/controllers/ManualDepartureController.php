<?php

namespace app\controllers;

namespace backend\controllers;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\BoatNumbers;
use backend\models\DepartureBoats;
use backend\models\DepartureRequests;
use backend\models\DepartureSkipper;
use backend\models\DeparureRequestCrew;
use backend\models\HighseasLicense;
use backend\models\NationalLicense;
use backend\models\ProfileFisherman;
use backend\models\ProfileOfficer;
use backend\models\Skipper;
use backend\models\SkipperRenew;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * DepartureController implements the CRUD actions for DepartureRequests model.
 */
class ManualDepartureController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => ['submitted', 'create', 'boatdetails', 'getskipper', 'getdepartureboatdetails'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all DepartureRequests models.
     *
     * @return string
     */


    /**
     * Creates a new DepartureRequests model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate()
    {
        if (!UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)) {
            throw new UnauthorizedHttpException(Yii::t('app', 'Unauthorized'));
        }
        $model = new DepartureRequests();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->approve = "A";
//                $model->req_date_time = date("Y-m-d H:i");
                if (!UserTypeUtil::hasType(Constant::HARBOUR_OFFICER)) {
                    throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
                }
                if ($model->harbor !=
                    Yii::$app->session->get("officer_harbour")) {
                    throw new UnauthorizedHttpException(Yii::t('app', 'Unauthorized'));
                }
                $model->action_date = $model->req_date_time;
                $officer = ProfileOfficer::findOne(Yii::$app->user->identity->profile_id);
                $model->user = $officer->first_name . ' ' . $officer->last_name . ' (' . Yii::$app->user->identity->nic . ")";
//            }


                $transaction = Yii::$app->db->beginTransaction();
                $model->validate();
                $error = false;
                $hasTakenAsSkipper = false;
                $emptyCrewName = false;
                $hasAtLeastOneCrew = false;
                $hasAtLeastOneEqu = false;
                $emptyHighseas = false;
                if ($model->fishing_area == 2 && empty($model->hs_license_no)) {
                    $error = true;
                    $emptyHighseas = true;
                }

                if (
                    (!empty($model->length_longline) && !empty($model->longline_hooks))
                    || (!empty($model->length_ringnet) && !empty($model->mesh_gillnet))
                    || (!empty($model->length_gillnet) && !empty($model->mesh_ringnet))
                ) {

                    if (!empty($model->length_longline) && empty($model->longline_hooks))
                        $hasAtLeastOneEqu = false;
                    elseif (!empty($model->length_ringnet) && empty($model->mesh_gillnet))
                        $hasAtLeastOneEqu = false;
                    elseif (!empty($model->length_gillnet) && empty($model->mesh_ringnet))
                        $hasAtLeastOneEqu = false;
                    elseif (empty($model->length_longline) && !empty($model->longline_hooks))
                        $hasAtLeastOneEqu = false;
                    elseif (empty($model->length_ringnet) && !empty($model->mesh_gillnet))
                        $hasAtLeastOneEqu = false;
                    elseif (empty($model->length_gillnet) && !empty($model->mesh_ringnet))
                        $hasAtLeastOneEqu = false;
                    else
                        $hasAtLeastOneEqu = true;
                }

                if ($hasAtLeastOneEqu && (!empty($model->national_license_no) || !empty($model->hs_license_no)) &&
                    $model->agree == 1 &&
                    $model->save()) {
                    $crewMembersNIC = $this->request->post("crewNIC");
                    $crewName = $this->request->post("crewName");

                    for ($i = 0; $i < sizeof($crewMembersNIC); $i++) {
                        if ($crewMembersNIC[$i] != "") {
                            if (!empty($crewMembersNIC[$i])) {
                                $hasAtLeastOneCrew = true;
                            }
                            $crew = new DeparureRequestCrew();
                            $crew->request_id = $model->id;
                            $crew->nic = $crewMembersNIC[$i];
                            $crew->name = $crewName[$i];
                            if (empty($crewName[$i])) {

                                $emptyCrewName = true;
                                $error = true;
                                break;

                            }

                            if ($crewMembersNIC[$i] == $model->skipper_nic) {
                                $hasTakenAsSkipper = true;
                                $error = true;
                                break;
                            }
                            if (!$crew->save()) {
                                $error = true;
                                break;
                            }
                        }
                    }
                } else {
                    $error = true;
                    if ($model->agree == 0) {
                        Yii::$app->session->setFlash('error', 'Please accept the terms and conditions.');
                    }
                    if ((empty($model->national_license_no) || empty($model->hs_license_no))) {
                        Yii::$app->session->setFlash('error', 'Please Enter the Highseas or National License number.');

                    }
                    if (!$hasAtLeastOneEqu) {
                        Yii::$app->session->setFlash('error', 'අරං යන ආම්පන්න විස්තරය හරියට පුරවා ඇතිදැයි පරීක්ෂා කරන්න. | 
எடுத்துச் செல்லும் வலை தொடர்பான விபரங்கள் சரியாக நிரப்பப்பட்டுள்ளனவா என்பதை சரிபார்க்கவும். |  Check whether the details of the fishing gears has been filled out correctly');

                    }
                }
                if ($error == false && $hasAtLeastOneCrew == false) {
                    $error = true;
                    Yii::$app->session->setFlash('error', 'අවම වශයෙන් එක් ගැනියෙකුගේ හෝ විස්තර පුරවන්න. | Please enter at least one crew member. | குறைந்தது ஒரு குழு உறுப்பினரையாவது உள்ளீடு செய்யவும்.');
                }
                if ($error == true && $hasTakenAsSkipper == true) {
                    $error = true;
                    Yii::$app->session->setFlash('error', 'නියමුවාව ගැනියෙකු ලෙස ද ඇතුළත් කළ නොහැක. | You cannot use the skipper as a crew member. | படகோட்டியை ஒரு குழு உறுப்பினராக உள்ளீடு செய்ய முடியாது.');
                }
                if ($error == true && $emptyCrewName == true) {
                    $error = true;
                    Yii::$app->session->setFlash('error', 'Please fill the all crew members names');
                }
                if ($error == true && $emptyHighseas == true) {
                    $error = true;
                    Yii::$app->session->setFlash('error', 'ඔබට High Seas පිටත්වීම සඳහා අයදුම් කිරීමට වලංගු High Seas බලපත්‍රයක් නොමැත. | You do not have a valid High Seas license to apply for High Seas departure. | High Seas புறப்பாட்டிற்கு விண்ணப்பிக்க உங்களிடம் செல்லுபடியாகும் High Seas உரிமம் இல்லை.');
                }
                if (!$error) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Departure request has been sent successfully | ගමන්වාර ඉල්ලීමේ අයදුම්පත සාර්ථකව යොමුකරන ලදී | புறப்பாடு கோரிக்கை வெற்றிகரமாக அனுப்பப்பட்டுள்ளது.');
                    return $this->redirect(['departure/view', 'id' => $model->id]);

                } else {
                    $transaction->rollback();
                }

            }
        } else {
            $model->loadDefaultValues();
        }


        return $this->render('create', [
            'model' => $model,

        ]);
    }


    public function actionBoatdetails($id)
    {
        $boatNumber = BoatNumbers::find()->where(["boat_number" => $id, 'status' => Constant::Active])->one();
        if ($boatNumber == "") {
            $returnData["status"] = "Invalid boat number, please check the boat number again | වැරදි යාත්‍රා අංකයකි, කරැණාකර නැවත පරීක්ෂා කර බලන්න. | தவறான படகு எண், தயவுசெய்து படகு எண்ணை மீண்டும் சரிபார்க்கவும்.";
            echo json_encode($returnData);
            exit();

        }

        $boatReg = DepartureBoats::find()->where(["boat_number_id" => $boatNumber->id])->one();

        if ($boatReg === null || $boatReg->status === null || $boatReg->status !== "Departure Allowed") {
            $returnData["status"] = "Departure is not allowed for this boat | මෙම යාත්‍රාවට ගමන්වාර ලබාගැනීමට අවසර නැත | இந்தப் படகுக்கு புறப்பாடு அனுமதி இல்லை.";
            echo json_encode($returnData);
            exit();
        }
        if (empty($boatReg->latestBoatRegExpire) || strtotime($boatReg->latestBoatRegExpire) < time()) {
            $returnData["status"] = "ඔබගේ යාත්‍රාවේ ලියාපදිංචිය කල් ඉකුත් වී ඇත. | You do not have a valid boat registration license. | உங்களிடம் செல்லுபடியாகும் படகு பதிவு உரிமம் இல்லை.";
            echo json_encode($returnData);
            exit();
        }
        //        if ((empty($boatReg->latestNationalExpire) && empty($boatReg->latestHighseasExpire)) || (strtotime
//                ($boatReg->latestNationalExpire) < time() && strtotime($boatReg->latestHighseasExpire) < time())) {
//            $returnData["status"] = "ඔබට සක්‍රීය Boat EEZ හෝ High Seas බලපත්‍රයක් නොමැත. | You do not have an active Boat EEZ or High Seas license. | உங்களிடம் செயலில் உள்ள Boat EEZ அல்லது High Seas உரிமம் இல்லை.";
//            echo json_encode($returnData);
//            exit();
//        }
        $returnData['alters'] = "";
        $expireDateBR = $boatReg->latestBoatRegExpire;
//
        if (!empty($expireDateBR)) {

            $daysLeft = floor((strtotime($expireDateBR) - time()) / 86400);
            $formattedDate = date('Y-m-d', strtotime($expireDateBR));

            if ($daysLeft <= 30) {
                $returnData['alters'] = "<br> Boat Registration License expiring on $formattedDate (in $daysLeft days). | බෝට්ටු ලියාපදිංචි බලපත්‍රය $formattedDate දිනෙන් (දින $daysLeft ක් තුළ) කල් ඉකුත් වේ. | படகு பதிவு உரிமம் $formattedDate அன்று (இன்னும் $daysLeft நாட்களில்) காலாவதியாகும். ";
            }
        }
        $expireDate = $boatReg->latestNationalExpire;
//
        if (!empty($expireDate)) {

            $daysLeft = floor((strtotime($expireDate) - time()) / 86400);
            $formattedDate = date('Y-m-d', strtotime($expireDate));

            if ($daysLeft <= 30) {
                $returnData['alters'] = "<br>National License (EEZ) expiring on $formattedDate (in $daysLeft days). | ජාතික බලපත්‍රය (EEZ) $formattedDate දිනෙන් (දින $daysLeft ක් තුළ) කල් ඉකුත් වේ. | தேசிய உரிமம் (EEZ) $formattedDate அன்று (இன்னும் $daysLeft நாட்களில்) காலாவதியாகும். ";
            }
        }
        $expireDateHS = $boatReg->getLatestHighseasExpire();

        if (!empty($expireDateHS)) {

            $daysLeft = floor((strtotime($expireDateHS) - time()) / 86400);
            $formattedDate = date('Y-m-d', strtotime($expireDateHS));

            if ($daysLeft <= 30) {
                $returnData['alters'] = $returnData['alters'] . "<br> High Seas License expiring on $formattedDate (in $daysLeft days). | High Seas බලපත්‍රය $formattedDate දිනෙන් (දින $daysLeft ක් තුළ) කල් ඉකුත් වේ. | High Seas உரிமம் $formattedDate அன்று (இன்னும் $daysLeft நாட்களில்) காலாவதியாகும்.";
            }
        }
        $nationalLicence = NationalLicense::find()->where(['boat_registration_id' => $boatReg->id, "status" =>
            Constant::Active])->orderBy(['expire_date' => SORT_DESC])
            ->one();
        $highseasLicence = HighseasLicense::find()->where(['boat_registration_id' => $boatReg->id, "status" => Constant::Active])->orderBy(['expire_date' => SORT_DESC])->one();
        $owner = ProfileFisherman::findOne($boatReg->fisherman_id);

        $returnData['status'] = "";
        $returnData['ownerName'] = $owner->preferred_name_for_id;
        $returnData['ownerMobile'] = $owner->mobile;
        $returnData['ownerEmail'] = $owner->email;
        $returnData['nationalLicence'] = $nationalLicence->license_number ?? "";
        $returnData['highseasLicence'] = $highseasLicence->license_number ?? "";

        echo json_encode($returnData);
        exit();

    }

    public function actionGetskipper($id)
    {
        // Normalize NIC
        $nic = $id;
        if (!preg_match('/^\d{12}$/', $nic)) { // not 12 digits
            if (stripos($nic, 'v') === false) { // no 'v' or 'V'
                $nic .= 'V';
            }
        }

        $profileFisherman = ProfileFisherman::find()->where(['nic' => $nic])->one();
        $nicList = $this->getNicVariants($id);

        $departureSkippers = DepartureSkipper::find()
            ->where(['nic' => $nicList])
            ->all();

        // Check if any record is NOT allowed
        $blocked = false;
        $departureSkipper = null;

        foreach ($departureSkippers as $ds) {
            if ($ds->status != "Departure Allowed" || $ds->status == "") {
                $blocked = true;
                break;
            }
            $departureSkipper = $ds; // keep a valid one
        }
        if ($blocked) {
            $returnData["status"] = "Departure is not allowed for this departureSkipper | මෙම නියමුවාට ගමන්වාර පිටත්වීමට අවසර නැත | இந்தப் படகோட்டிக்கு புறப்பாடு அனுமதி இல்லை.";
            echo json_encode($returnData);
            exit();
        }

        if (!empty($profileFisherman)) {
            $skipperProfile = Skipper::find()->where(['fisherman_id' => $profileFisherman->id])->one();

            if (!empty($skipperProfile)) {
                if (empty($departureSkipper)) {
                    $departureSkipper = new DepartureSkipper();
                    $departureSkipper->status = 'Departure Allowed';
                }
                if ($skipperProfile->status == Constant::Pending || $skipperProfile->status ==
                    Constant::PaymentPending) {
                    $departureSkipper->skipper_id = "SKPREQ-" . $skipperProfile->id;
                } elseif ($skipperProfile->status == Constant::Expired) {
                    $skipperProfileRenew = SkipperRenew::find()->where(['skipper_uid' =>
                        $skipperProfile->skipper_uid])->andWhere(['!=', 'status', Constant::Completed])->one();
                    if (!empty($skipperProfileRenew)) {
                        $departureSkipper->skipper_id = "SKPREQ-" . $skipperProfileRenew->id;
                    }

                } else {
                    $departureSkipper->skipper_id =
                        $profileFisherman->fisherman_uid;
                }

                $departureSkipper->skipper_name =
                    $profileFisherman->preferred_name_for_id;

                $departureSkipper->nic = $id;
            }
        }
//        $departureSkipper->save(false);

        if ($departureSkipper == "" || empty($departureSkipper)) {

            $returnData['status'] = "";
            $returnData['name'] = "";
            $returnData['nic'] = "";
            echo json_encode($returnData);
            exit();
        }


        $returnData['status'] = "";
        $returnData['id'] = $departureSkipper->skipper_id;
        $returnData['name'] = $departureSkipper->skipper_name;
        $returnData['nic'] = $departureSkipper->nic;

        echo json_encode($returnData);
        exit();

    }

    function getNicVariants($nic)
    {
        $nic = strtoupper(trim($nic));
        $list = [];

        // Always include original
        $list[] = $nic;

        // If 12-digit NIC
        if (preg_match('/^\d{12}$/', $nic)) {

            // Convert to old NIC
            $old = substr($nic, 2);
            $old = substr_replace($old, '', 5, 1); // remove 6th char

            $list[] = $old . 'V'; // with V
            $list[] = $old;       // without V
        }

        // If old NIC (with or without V)
        if (preg_match('/^\d{9}V?$/i', $nic)) {

            $base = rtrim($nic, 'V'); // remove V if exists

            // ensure both formats
            $list[] = $base;
            $list[] = $base . 'V';
            // remove V if exists
            $oldNic = strtoupper(rtrim($base, 'V'));

            if (preg_match('/^\d{9}$/', $oldNic)) {

                // insert 19 at start


                // insert 0 at 6th position (after year + part of serial)
                $new = substr($oldNic, 0, 5) . '0' . substr($oldNic, 5);
                $new = '19' . $new;
                $list[] = $new;
            }


        }

        // Remove duplicates
        return array_values(array_unique($list));
    }

    public function actionGetdepartureboatdetails($main, $role)
    {
        Constant::getSubTypesByMainAndAllowed($main, $role);

        echo json_encode(Constant::getSubTypesByMainAndAllowed($main, $role));
        exit();

    }


    protected
    function findLicenseModel($id)
    {
        if (($model = DepartureRequests::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * Finds the DepartureRequests model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return DepartureRequests the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepartureRequests::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


}
