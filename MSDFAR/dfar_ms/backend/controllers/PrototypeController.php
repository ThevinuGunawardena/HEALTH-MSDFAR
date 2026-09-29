<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\components\Controller;

use yii\web\Response;

/**
 * PrototypeController
 */
class PrototypeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'language'],
                        'allow' => true,
                    ],
                    [
                        'actions' => [
                            'logout',
                            'fm-index',
                            'fi-index',
                            'fm-profile',
                            'fm-register',
                            'fm-register2',
                            'fm-register3',
                            'fm-registerrenew',
                            'fm-register2renew',
                            'fm-register3renew',
                            'bo-operationhs',
                            'bo-operationez',
                            'fm-skipperlicense',
                            'fm-skipperlicenserenew',
                            'bo-register',
                            'bo-renew',
                            'bo-operationezrenew',
                            'bo-operationhsrenew',
                            'bo-registration',
                            'yo-yaregistration',
                            'fi-fishermanregreqs',
                            'fi-boatregreqs',
                            'fi-yardregreqs',
                            'fi-yardrenewreqs',
                            'admin-boatdesignreqs',
                            'fi-boatdesignreqs',
                            'fi-boatdesignreqsview',
                            'bo-renewal',
                            'bo-reserveboatno',
                            'admin-boatdesign',
                            'bo-transfer',
                            'yo-yarenewal',
                            'fi-fishermanregreqsview',
                            'fi-boatregreqsview',
                            'fi-reserveboatnoview',
                            'ya-registration',
                            'fi-yardregview',
                            'fi-yardrenewview',
                            'fi-boatrenewalview',
                            'fi-boatrenewalreqs',
                            'fi-operation',
                            'fi-operationview',
                            'fi-skipperlicense',
                            'fi-skipperlicenserenew',
                            'fi-skipperlicenserenewview',
                            'fi-skipperlicenseview',
                            'fm-bocancelation',
                            'fi-bocancelation',
                            'fi-bocancelationview',
                            'fi-addnewgeartype',
                            'ad-index',
                            'ad-fishermanregreqs',
                            'fi-boatdesignview',
                            'fi-boatdetails',
                            'ad-boatregreqs',
                            'ad-fishermanregreqsview',
                            'ad-boatregreqsview',
                            'ad-boatdesignreqs',
                            'ad-boatdesignreqsview',
                            'ad-yardregreqs',
                            'ad-yardregview',
                            'ad-yardrenewreqs',
                            'ad-yardrenewview',
                            'ad-skipperlicense',
                            'ad-skipperlicenseview',
                            'ad-operation',
                            'ad-operationview',
                            'ad-reserveboatnoview',
                            'ad-reserveboatno',
                            'ad-bocancelation',
                            'ad-bocancelationview',
                            'ad-boatrenew',
                            'ad-boatrenewview',
                            'ad-boattransfer',
                            'ad-boattransferview',
                            'fi-operationrenew',
                            'fi-operationrenewview',
                            'ad-operationrenew',
                            'ad-operationrenewview',
                            'ad-skipperlicenserenew',
                            'ad-skipperlicenserenewview',
                            'ad-bocancelationview',
                            'ad-reserveboatno',
                            'fi-boatdesignview',
                            'fi-boattransreqs',
                            'fi-boattransferview',
                            'fi-boatnoreservereqs',
                            'fi-geartypelist',
                            'fi-boatdelupdate',
                            'dg-fishermanregreqs',
                            'dg-operation',
                            'dg-skipperlicense',
                            'dg-bocancelation',
                            'dg-boatnoreservereqs',
                            'dg-boatregreqs',
                            'dg-yardregreqs',
                            'dg-boattransreqs',
                            'fi-scientific',
                            'fi-scientificdatareqs',
                            'fi-addsamplecraft',
                            'fi-addoperationcost',
                            'fi-addlengthweight',
                            'dg-boatdesignreqs',
                            'dg-boatdesignview',
                            'admin-boatdesignview',
                            'admin-yodetails',
                            'admin-yardregview',
                            'ad-yardregview',
                            'ad-yardregreqs',
                            'ad-yardrenewview',
                            'ad-yardrenewreqs',
                            'mea-yardreg',
                            'mea-yardregreqs',
                            'mea-boatregreqs',
                            'mea-boatreg',
                            'mea-boatregview',
                            'sdo-scientificdatareqs',
                            'sdo-scientific',
                            'sdo-addsamplecraft',
                            'sdo-addlengthweight',
                            'sdo-addoperationcost',
                            'nara-scientificdatareqs',
                            'nara-scientific',
                            'nara-addsamplecraft',
                            'nara-addlengthweight',
                            'nara-addoperationcost',
                            'dg-boattransreqs',
                            'dg-boatdesignreqs',
                            'dg-boatdesignview',
                            'dg-operationview',
                            'do-skipperlicense',
                            'do-boatregistrationupdate',
                            'do-boatregistrationlist',
                            'do-boatnumberview',
                            'fi-boatregistrationlist',
                            'fi-boatregistrationupdate',
                            'do-boatnumberreservelist',
                            'yo-index',
                            'admin-index',
                            'admin-addnewuser',
                            'admin-addnewemployee',
                            'admin-addnewskipperins',
                            'admin-addnewlandingsite',
                            'admin-addnewoccupation',
                            'admin-addnewpronature',
                            'admin-addnewdsdivision',
                            'admin-addnewpayment',
                            'admin-addnewfidivision',
                            'admin-addnewgndivision',
                            'admin-addnewexportcountry',
                            'admin-addnewinsurancecompany',
                            'admin-addnewdesignation',
                            'admin-addnewskipperlicprogram',
                            'admin-addnewuserrole',
                            'admin-addwidgetprevillage',
                            'admin-adduserprevillage',
                            'admin-addnewhscode',
                            'admin-addnewequipment',
                            'admin-addnewdistress',
                            'admin-addnewproductform',
                            'admin-addnewyardsafetytype',
                            'admin-changeboatstatus',
                            'admin-addnewfidistrict',
                            'admin-addnewinstituteprogram',
                            'admin-addnewfishspecies',
                            'admin-updatelicenseprice',
                            'fi-addlength_weight',
                            'fi-addlengthweightview',
                            'fi-scientificview',
                            'fi-addsamplecraftview',
                            'fi-addoperationcostview',
                            'sdo-addlengthweightview',
                            'sdo-scientificreports',
                            'sdo-scientificreportuser',
                            'mea-boatregrenew',
                            'mea-boatrenewreqs',
                            'mea-boatrenew_view',  
                            'skipper-cancelation',
                            'ex-license',
                            'im-license',
                            'po-license',
                            'reex-license',
                            'ex-licenseassignrequest',
                            'ex-approvesamplelicenserequest',
                            'ex-registration',
                            'ex-assigninspector',
                            'ex-application',
                            'ex-inspectionreport',
                            'ex-dmrecommendation',
                            'ex-dgrecommendation',
                            'ex-samplelicense',
                            'ex-adrecommendation',
                            'im-approvesamplelicenserequest',
                            'im-registration',
                            'im-application',
                            'im-assigninspector',
                            'im-inspectionreport',
                            'im-samplelicense',
                            'im-adrecommendation',
                            'im-dmrecommendation',
                            'im-dgrecommendation',
                            'po-application',
                            'po-approvesamplelicenserequest',
                            'po-assigninspector',
                            'po-inspectionreport',
                            'po-dmrecommendation',
                            'po-dgrecommendation',
                            'po-adrecommendation',
                            'po-samplelicense',
                            're-approvesamplelicenserequest',
                            're-application',
                            're-assigninspector',
                            're-inspectionreport',
                            're-dmrecommendation',
                            're-dgrecommendation',
                            're-adrecommendation',
                            're-samplelicense',
                            'ad-exapprove',
                            'ad-exapplication',
                            'ad-exassigninspector',
                            'ad-exinspectionreport',
                            'ad-exsamplelicense',
                            'ad-exdmrecommendation',
                            'ad-exdgrecommendation',
                            'ad-exadrecommendation',
                            'ad-exassign',
                            'ad-exassignapprove',
                            'ad-exassignapplication',
                            'ad-exassigninspectornew',
                            'ad-exapprovesamplelicenserequest',
                            'ad-exapprovelicense',
                            'ad-exassigninspectorapprove',
                            'ad-exinspectionreportnew',
                            'ad-exsamplelicensenew',
                            'ad-exadrecommendationnew',
                            'ad-imapprove',
                            'ad-imapplication',
                            'ad-imassigninspector',
                            'ad-iminspectionreport',
                            'ad-imsamplelicense',
                            'ad-imdmrecommendation',
                            'ad-imdgrecommendation',
                            'ad-imadrecommendation',
                            'ad-imassignapprove',
                            'ad-imassignapplication',
                            'ad-imassigninspectornew',
                            'ad-imapprovesamplelicenserequest',
                            'ad-imapprovelicense',
                            'ad-imassigninspectorapprove',





                        ],

                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionAdIndex()
    {
        return $this->render('adIndex');
    }
    public function actionAdFishermanregreqs()
    {
        return $this->render('adFishermanregreqs');
    }
    public function actionAdFishermanregreqsview()
    {
        return $this->render('adFishermanregreqsview');
    }
    public function actionAdBoatregreqs()
    {
        return $this->render('adBoatregreqs');
    }
    public function actionAdBoatrenew()
    {
        return $this->render('adBoatrenew');
    }
    public function actionAdBoattransfer()
    {
        return $this->render('adBoattransfer');
    }
    public function actionAdBoattransferview()
    {
        return $this->render('adBoattransferview');
    }
    public function actionAdBoatrenewview()
    {
        return $this->render('adBoatrenewview');
    }

    public function actionAdBoatregreqsview()
    {
        return $this->render('adBoatregreqsview');
    }

    public function actionAdBoatdesignreqs()
    {
        return $this->render('AdBoatdesignreqs');
    }
    public function actionAdBoatdesignreqsview()
    {
        return $this->render('AdBoatdesignreqsview');
    }

    public function actionAdYardrenewview()
    {
        return $this->render('adYardrenewview');
    }
    public function actionAdYardregview()
    {
        return $this->render('adYardregview');
    }
    public function actionAdYardrenewreqs()
    {
        return $this->render('AdYardrenewreqs');
    }
    public function actionAdSkipperlicense()
    {
        return $this->render('adSkipperlicense');
    }
    public function actionAdSkipperlicenseview()
    {
        return $this->render('adSkipperlicenseview');
    }
    public function actionAdOperation()
    {
        return $this->render('adOperation');
    }
    public function actionAdOperationview()
    {
        return $this->render('adOperationview');
    }
    public function actionAdOperationrenew()
    {
        return $this->render('adOperationrenew');
    }
    public function actionAdOperationrenewview()
    {
        return $this->render('adOperationrenewview');
    }
    public function actionAdReserveboatnoview()
    {
        return $this->render('adReserveboatnoview');
    }
    public function actionAdReserveboatno()
    {
        return $this->render('adReserveboatno');
    }
    public function actionAdBocancelation()
    {
        return $this->render('adBocancelation');
    }
    public function actionAdBocancelationview()
    {
        return $this->render('adBocancelationview');
    }

    public function actionAdSkipperlicenserenew()
    {
        return $this->render('adSkipperlicenserenew');
    }
    public function actionAdSkipperlicenserenewview()
    {
        return $this->render('adSkipperlicenserenewview');
    }
    public function actionDoBoatregistrationupdate()
    {
        return $this->render('doBoatregistrationupdate');
    }
    public function actionDoBoatregistrationlist()
    {
        return $this->render('doBoatregistrationlist');
    }
    public function actionDoSkipperlicense()
    {
        return $this->render('doSkipperlicense');
    }
    public function actionDoBoatnumberreservelist()
    {
        return $this->render('doBoatnumberreservelist');
    }
    public function actionDoBoatnumberview()
    {
        return $this->render('doBoatnumberview');
    }
    public function actionAdminIndex()
    {
        return $this->render('adminIndex');
    }
    public function actionAdminAddnewuser()
    {
        return $this->render('adminAddnewuser');
    }
    public function actionAdminAddnewemployee()
    {
        return $this->render('adminAddnewemployee');
    }
    public function actionAdminAddnewskipperins()
    {
        return $this->render('adminAddnewskipperins');
    }
    public function actionAdminAddnewlandingsite()
    {
        return $this->render('adminAddnewlandingsite');
    }
    public function actionAdminAddnewoccupation()
    {
        return $this->render('adminAddnewoccupation');
    }
    public function actionAdminAddnewpronature()
    {
        return $this->render('adminAddnewpronature');
    }
    public function actionAdminAddnewdsdivision()
    {
        return $this->render('adminAddnewdsdivision');
    }
    public function actionAdminAddnewpayment()
    {
        return $this->render('adminAddnewpayment');
    }
    public function actionAdminAddnewfidivision()
    {
        return $this->render('adminAddnewfidivision');
    }
    public function actionAdminAddnewgndivision()
    {
        return $this->render('adminAddnewgndivision');
    }
    public function actionAdminAddnewexportcountry()
    {
        return $this->render('adminAddnewexportcountry');
    }
    public function actionAdminAddnewinsurancecompany()
    {
        return $this->render('adminAddnewinsurancecompany');
    }
    public function actionAdminAddnewdesignation()
    {
        return $this->render('adminAddnewdesignation');
    }
    public function actionAdminAddnewskipperlicprogram()
    {
        return $this->render('adminAddnewskipperlicprogram');
    }
    public function actionAdminAddnewuserrole()
    {
        return $this->render('adminAddnewuserrole');
    }
    public function actionAdminAddwidgetprevillage()
    {
        return $this->render('adminAddwidgetprevillage');
    }
    public function actionAdminAdduserprevillage()
    {
        return $this->render('adminAdduserprevillage');
    }
    public function actionAdminAddnewhscode()
    {
        return $this->render('adminAddnewhscode');
    }
    public function actionAdminAddnewequipment()
    {
        return $this->render('adminAddnewequipment');
    }
    public function actionAdminAddnewdistress()
    {
        return $this->render('adminAddnewdistress');
    }
    public function actionAdminAddnewproductform()
    {
        return $this->render('adminAddnewproductform');
    }
    public function actionAdminAddnewyardsafetytype()
    {
        return $this->render('adminAddnewyardsafetytype');
    }
    public function actionAdminChangeboatstatus()
    {
        return $this->render('adminChangeboatstatus');
    }
    public function actionAdminAddnewfidistrict()
    {
        return $this->render('adminAddnewfidistrict');
    }
    public function actionAdminAddnewinstituteprogram()
    {
        return $this->render('adminAddnewinstituteprogram');
    }
    public function actionAdminAddnewfishspecies()
    {
        return $this->render('adminAddnewfishspecies');
    }
    public function actionAdminUpdatelicenseprice()
    {
        return $this->render('adminUpdatelicenseprice');
    }

    public function actionFmIndex()
    {
        return $this->render('fmIndex');
    }
    public function actionYoIndex()
    {
        return $this->render('yoIndex');
    }
    public function actionFiIndex()
    {
        return $this->render('fiIndex');
    }
    public function actionFiGeartypelist()
    {
        return $this->render('FiGeartypelist');
    }
    public function actionFiFishermanregreqs()
    {
        return $this->render('FiFishermanregreqs');
    }
    public function actionFiFishermanregreqsview()
    {
        return $this->render('FiFishermanregreqsview');
    }
    public function actionFiBoatregreqs()
    {
        return $this->render('FiBoatregreqs');
    }
    public function actionFiBoatregreqsview()
    {
        return $this->render('FiBoatregreqsview');
    }
    public function actionFiBoatdetails()
    {
        return $this->render('FiBoatdetails');
    }
    public function actionFiBoatdelupdate()
    {
        return $this->render('FiBoatdelupdate');
    }
    public function actionFiYardregreqs()
    {
        return $this->render('fiYardregreqs');
    }
    public function actionFiYardrenewreqs()
    {
        return $this->render('FiYardrenewreqs');
    }
    public function actionAdminBoatdesignreqs()
    {
        return $this->render('adminBoatdesignreqs');
    }
    public function actionFiBoatdesignreqsview()
    {
        return $this->render('FiBoatdesignreqsview');
    }
    public function actionFiOperation()
    {
        return $this->render('fiOperation');
    }
    public function actionFiOperationview()
    {
        return $this->render('fiOperationview');
    }
    public function actionFiOperationrenew()
    {
        return $this->render('fiOperationrenew');
    }
    public function actionFiOperationrenewview()
    {
        return $this->render('fiOperationrenewview');
    }
    public function actionFiSkipperlicense()
    {
        return $this->render('fiSkipperlicense');
    }
    public function actionFiSkipperlicenseview()
    {
        return $this->render('fiSkipperlicenseview');
    }
    public function actionFiSkipperlicenserenew()
    {
        return $this->render('fiSkipperlicenserenew');
    }
    public function actionFiSkipperlicenserenewview()
    {
        return $this->render('fiSkipperlicenserenewview');
    }
    public function actionFiBocancelation()
    {
        return $this->render('fiBocancelation');
    }
    public function actionFiAddnewgeartype()
    {
        return $this->render('fiAddnewgeartype');
    }
    public function actionFiBocancelationview()
    {
        return $this->render('fiBocancelationview');
    }
    public function actionFiBoatregistrationupdate()
    {
        return $this->render('fiBoatregistrationupdate');
    }
    public function actionFiBoatregistrationlist()
    {
        return $this->render('fiBoatregistrationlist');
    }
    public function actionFmProfile()
    {
        return $this->render('fmProfile');
    }
    public function actionFmRegisterrenew()
    {
        return $this->render('fmRegisterrenew');
    }


    public function actionFmRegister2renew()
    {
        return $this->render('fmRegister2renew');
    }

    public function actionFmRegister3renew()
    {
        return $this->render('fmRegister3renew');
    }

    public function actionFmRegister()
    {
        return $this->render('fmRegister');
    }

    public function actionFmRegister2()
    {
        return $this->render('fmRegister2');
    }

    public function actionFmRegister3()
    {
        return $this->render('fmRegister3');
    }
    public function actionFmBocancelation()
    {
        return $this->render('fmBocancelation');
    }

    public function actionDgBoatdesignreqs()
    {
        return $this->render('DgBoatdesignreqs');
    }

    public function actionDgBoatdesignview()
    {
        return $this->render('dgBoatdesignview');
    }
    public function actionBoOperationhs()
    {
        return $this->render('boOperationhs');
    }
    public function actionBoOperationez()
    {
        return $this->render('boOperationez');
    }
    public function actionFmSkipperlicense()
    {
        return $this->render('fmSkipperlicense');
    }
    public function actionFmSkipperlicenserenew()
    {
        return $this->render('fmSkipperlicenserenew');
    }
    public function actionBoRegister()
    {
        return $this->render('boRegister');
    }
    public function actionBoRenew()
    {
        return $this->render('BoRenew');
    }
    public function actionBoOperationezrenew()
    {
        return $this->render('BoOperationezrenew');
    }

    public function actionBoOperationhsrenew()
    {
        return $this->render('BoOperationhsrenew');
    }
    public function actionBoRegistration()
    {
        return $this->render('boRegistration');
    }

    public function actionYoYaregistration()
    {
        return $this->render('yoYaregistration');
    }
    public function actionYoYarenewal()
    {
        return $this->render('yoYarenewal');
    }
    public function actionBoRenewal()
    {
        return $this->render('boRenewal');
    }
    public function actionBoReserveboatno()
    {
        return $this->render('boReserveboatno');
    }
    public function actionAdminBoatdesign()
    {
        return $this->render('adminBoatdesign');
    }
    public function actionBoTransfer()
    {
        return $this->render('boTransfer');
    }
    public function actionFiReserveboatnoview()
    {
        return $this->render('fiReserveboatnoview');
    }

    public function actionFiYardregview()
    {
        return $this->render('fiYardregview');
    }
    public function actionFiYardrenewview()
    {
        return $this->render('fiYardrenewview');
    }
    public function actionFiBoatrenewalview()
    {
        return $this->render('fiBoatrenewalview');
    }
    public function actionFiBoatrenewalreqs()
    {
        return $this->render('fiBoatrenewalreqs');
    }
    public function actionFiBoatdesignview()
    {
        return $this->render('fiBoatdesignview');
    }
    public function actionFiBoattransreqs()
    {
        return $this->render('fiBoattransreqs');
    }
    public function actionFiBoattransferview()
    {
        return $this->render('fiBoattransferview');
    }
    public function actionFiBoatnoreservereqs()
    {
        return $this->render('fiBoatnoreservereqs');
    }
    public function actionDgFishermanregreqs()
    {
        return $this->render('dgFishermanregreqs');
    }
    public function actionDgOperation()
    {
        return $this->render('dgOperation');
    }
    public function actionDgSkipperlicense()
    {
        return $this->render('dgSkipperlicense');
    }
    public function actionDgBocancelation()
    {
        return $this->render('dgBocancelation');
    }
    public function actionDgBoatnoreservereqs()
    {
        return $this->render('dgBoatnoreservereqs');
    }
    public function actionDgBoatregreqs()
    {
        return $this->render('dgBoatregreqs');
    }
    public function actionDgYardregreqs()
    {
        return $this->render('dgYardregreqs');
    }
    public function actionDgBoattransreqs()
    {
        return $this->render('dgBoattransreqs');
    }
    public function actionFiScientific()
    {

        return $this->render('fiScientific');
    }

    public function actionFiScientificdatareqs()
    {
        return $this->render('fiScientificdatareqs');
    }
    public function actionFiAddsamplecraft()
    {
        return $this->render('fiAddsamplecraft');
    }
    public function actionFiAddoperationcost()
    {
        return $this->render('fiAddoperationcost');
    }
    public function actionFiAddlengthweight()
    {
        return $this->render('fiAddlengthweight');
    }
    public function actionAdminBoatdesignview()
    {
        return $this->render('adminBoatdesignview');
    }
    public function actionAdminYodetails()
    {
        return $this->render('adminYodetails');
    }
    public function actionAdminYardregview()
    {
        return $this->render('adminYardregview');
    }

    public function actionAdYardregreqs()
    {
        return $this->render('adYardregreqs');
    }

    public function actionMeaYardreg()
    {
        return $this->render('meaYardreg');
    }
    public function actionMeaYardregreqs()
    {
        return $this->render('meaYardregreqs');
    }
    public function actionMeaBoatregreqs()
    {
        return $this->render('meaBoatregreqs');
    }
    public function actionMeaBoatreg()
    {
        return $this->render('meaBoatreg');
    }
    public function actionMeaBoatregview()
    {
        return $this->render('meaBoatregview');
    }
    public function actionSdoScientificdatareqs()
    {
        return $this->render('sdoScientificdatareqs');
    }
    public function actionSdoScientific()
    {
        return $this->render('sdoScientific');
    }
    public function actionSdoAddsamplecraft()
    {
        return $this->render('sdoAddsamplecraft');
    }
    public function actionSdoAddlengthweight()
    {
        return $this->render('sdoAddlengthweight');
    }
    public function actionSdoAddoperationcost()
    {
        return $this->render('sdoAddoperationcost');
    }



    public function actionNaraScientificdatareqs()
    {
        return $this->render('naraScientificdatareqs');
    }
    public function actionNaraScientific()
    {
        return $this->render('naraScientific');
    }
    public function actionNaraAddsamplecraft()
    {
        return $this->render('naraAddsamplecraft');
    }
    public function actionNaraAddlengthweight()
    {
        return $this->render('naraAddlengthweight');
    }
    public function actionNaraAddoperationcost()
    {
        return $this->render('naraAddoperationcost');
    }

    public function actionDgOperationview()
    {
        return $this->render('dgOperationview');
    }

    public function actionFiAddlength_weight()
    {
        return $this->render('fiAddlength_weight');
    }

    public function actionFiAddlengthweightview()
    {
        return $this->render('fiAddlengthweightview');
    }

    public function actionFiScientificview()
    {
        return $this->render('fiScientificview');
    }

    public function actionFiAddsamplecraftview()
    {
        return $this->render('fiAddsamplecraftview');
    }

    public function actionFiAddoperationcostview()
    {
        return $this->render('fiAddoperationcostview');
    }

    public function actionSdoAddlengthweightview()
    {
        return $this->render('sdoAddlengthweightview');
    }

    public function actionSdoScientificreports()
    {
        return $this->render('sdoScientificreports');
    }

    public function actionSdoScientificreportuser()
    {
        return $this->render('sdoScientificreportuser');
    }


    public function actionMeaBoatregrenew()
    {
        return $this->render('meaBoatregrenew');
    }

    public function actionMeaBoatrenewreqs()
    {
        return $this->render('meaBoatrenewreqs');
    }


    public function actionMeaBoatrenew_view()
    {
        return $this->render('meaBoatrenew_view');
    }

    public function actionSkipperCancelation()
    {
        return $this->render('skipperCancelation');
    }

    public function actionExLicense()
    {
        return $this->render('exLicense');
    }
    public function actionImLicense()
    {
        return $this->render('imLicense');
    }
    public function actionPoLicense()
    {
        return $this->render('poLicense');
    }
    public function actionReexLicense()
    {
        return $this->render('reexLicense');
    }
    public function actionExLicenseassignrequest()
    {
        return $this->render('exLicenseassignrequest');
    }
    public function actionExApprovesamplelicenserequest()
    {
        return $this->render('exApprovesamplelicenserequest');
    }
    public function actionExRegistration()
    {
        return $this->render('exRegistration');
    }
    public function actionExAssigninspector()
    {
        return $this->render('exAssigninspector');
    }
    public function actionExApplication()
    {
        return $this->render('exApplication');
    }
    public function actionExInspectionreport()
    {
        return $this->render('exInspectionreport');
    }
    public function actionExDmrecommendation()
    {
        return $this->render('exDmrecommendation');
    }
    public function actionExDgrecommendation()
    {
        return $this->render('exDgrecommendation');
    }
    public function actionExSamplelicense()
    {
        return $this->render('exSamplelicense');
    }
    public function actionExAdrecommendation()
    {
        return $this->render('exAdrecommendation');
    }
    public function actionImApprovesamplelicenserequest()
    {
        return $this->render('imApprovesamplelicenserequest');
    }
    public function actionImRegistration()
    {
        return $this->render('imRegistration');
    }
    public function actionImApplication()
    {
        return $this->render('imApplication');
    }
    public function actionImAssigninspector()
    {
        return $this->render('imAssigninspector');
    }
    public function actionImInspectionreport()
    {
        return $this->render('imInspectionreport');
    }
    public function actionImSamplelicense()
    {
        return $this->render('imSamplelicense');
    }
    public function actionImAdrecommendation()
    {
        return $this->render('imAdrecommendation');
    }
    public function actionImDmrecommendation()
    {
        return $this->render('imDmrecommendation');
    }
    public function actionImDgrecommendation()
    {
        return $this->render('imDgrecommendation');
    }
    public function actionPoApplication()
    {
        return $this->render('poApplication');
    }
    public function actionPoApprovesamplelicenserequest()
    {
        return $this->render('poApprovesamplelicenserequest');
    }
    public function actionPoAssigninspector()
    {
        return $this->render('poAssigninspector');
    }
    public function actionPoInspectionreport()
    {
        return $this->render('poInspectionreport');
    }
    public function actionPoAdrecommendation()
    {
        return $this->render('poAdrecommendation');
    }
    public function actionPoDmrecommendation()
    {
        return $this->render('poDmrecommendation');
    }
    public function actionPoDgrecommendation()
    {
        return $this->render('poDgrecommendation');
    }
    public function actionPoSamplelicense()
    {
        return $this->render('poSamplelicense');
    }
    public function actionReApprovesamplelicenserequest()
    {
        return $this->render('reApprovesamplelicenserequest');
    }
    public function actionReApplication()
    {
        return $this->render('reApplication');
    }
    public function actionReAssigninspector()
    {
        return $this->render('reAssigninspector');
    }
    public function actionReInspectionreport()
    {
        return $this->render('reInspectionreport');
    }
    public function actionReDmrecommendation()
    {
        return $this->render('reDmrecommendation');
    }
    public function actionReDgrecommendation()
    {
        return $this->render('reDgrecommendation');
    }
    public function actionReSamplelicense()
    {
        return $this->render('reSamplelicense');
    }
    public function actionAdExapprove()
    {
        return $this->render('adExapprove');
    }
    public function actionAdExapplication()
    {
        return $this->render('adExapplication');
    }
    public function actionAdExassigninspector()
    {
        return $this->render('adExassigninspector');
    }
    public function actionAdExinspectionreport()
    {
        return $this->render('adExinspectionreport');
    }
    public function actionAdExsamplelicense()
    {
        return $this->render('adExsamplelicense');
    }
    public function actionAdExdmrecommendation()
    {
        return $this->render('adExdmrecommendation');
    }
    public function actionAdExdgrecommendation()
    {
        return $this->render('adExdgrecommendation');
    }
    public function actionAdExadrecommendation()
    {
        return $this->render('adExadrecommendation');
    }
    public function actionAdExassign()
    {
        return $this->render('adExassign');
    }
    public function actionAdExassignapprove()
    {
        return $this->render('adExassignapprove');
    }
    public function actionAdExassignapplication()
    {
        return $this->render('adExassignapplication');
    }
    public function actionAdExassigninspectornew()
    {
        return $this->render('adExassigninspectornew');
    }
    public function actionAdExapprovesamplelicenserequest()
    {
        return $this->render('adExapprovesamplelicenserequest');
    }
    public function actionAdExapprovelicense()
    {
        return $this->render('adExapprovelicense');
    }
    public function actionAdExassigninspectorapprove()
    {
        return $this->render('adExassigninspectorapprove');
    }
    public function actionAdExinspectionreportnew()
    {
        return $this->render('adExinspectionreportnew');
    }
    public function actionAdExadrecommendationnew()
    {
        return $this->render('adExadrecommendationnew');
    }
    public function actionAdExsamplelicensenew()
    {
        return $this->render('adExsamplelicensenew');
    }
    public function actionAdImapprove()
    {
        return $this->render('adImapprove');
    }
    public function actionAdImapplication()
    {
        return $this->render('adImapplication');
    }
    public function actionAdImassigninspector()
    {
        return $this->render('adImassigninspector');
    }
    public function actionAdIminspectionreport()
    {
        return $this->render('adIminspectionreport');
    }
    public function actionAdImsamplelicense()
    {
        return $this->render('adImsamplelicense');
    }
    public function actionAdImdmrecommendation()
    {
        return $this->render('adImdmrecommendation');
    }
    public function actionAdImdgrecommendation()
    {
        return $this->render('adImdgrecommendation');
    }
    public function actionAdImadrecommendation()
    {
        return $this->render('adImadrecommendation');
    }
    public function actionAdImassignapprove()
    {
        return $this->render('adImassignapprove');
    }
    public function actionAdImassignapplication()
    {
        return $this->render('adImassignapplication');
    }
    public function actionAdImassigninspectornew()
    {
        return $this->render('adImassigninspectornew');
    }
    public function actionAdImapprovesamplelicenserequest()
    {
        return $this->render('adImapprovesamplelicenserequest');
    }
    public function actionAdImapprovelicense()
    {
        return $this->render('adImapprovelicense');
    }
    public function actionAdImassigninspectorapprove()
    {
        return $this->render('adImassigninspectorapprove');
    }

    /**
     * Renders the DG Analytics Dashboard
     */
    public function actionDgDashboard()
    {
        // TODO: Replace with real data queries for DG context
        $categoryData = [
            ['name' => 'Category A', 'y' => 45],
            ['name' => 'Category B', 'y' => 26],
            ['name' => 'Category C', 'y' => 12],
            ['name' => 'Category D', 'y' => 17],
        ];
        $districtData = [
            ['name' => 'District 1', 'y' => 120],
            ['name' => 'District 2', 'y' => 80],
            ['name' => 'District 3', 'y' => 60],
            ['name' => 'District 4', 'y' => 40],
        ];
        $metrics = [
            'resolutionRate' => 87.5,
            'avgResponseTime' => 4.2,
        ];
        $monthlyData = [
            'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            'inquiries' => [100, 120, 90, 110, 130],
            'resolved' => [80, 100, 70, 90, 120],
        ];
        return $this->render('//analytics/dg-dashboard', [
            'categoryData' => $categoryData,
            'districtData' => $districtData,
            'metrics' => $metrics,
            'monthlyData' => $monthlyData,
        ]);
    }
}


