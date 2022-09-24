<?php

namespace App\Http\Controllers;
set_time_limit(600);

use App\Models\Agreement;
use App\Models\Beneficiary;
use App\Models\CreditCard;
use App\Models\Preexistance;
use App\Models\Tarifas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use PhpParser\Node\Stmt\Foreach_;
use App\Http\Controllers\ParametroController;
use Artisaninweb\SoapWrapper\SoapWrapper;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;
//use Mail;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyMail;
use PhpParser\Node\Stmt\TryCatch;

class AgreementController extends Controller
{

    private $url;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function storeData($data)
    {
        

        try {
            $opcionesTiempo = ['yearly_fee'=>'yearly', 'halfyear_fee'=>'halfyear','quarterly_fee'=>'quarterly','monthly_fee'=>'monthly_fee',];
            $parametrosWsdl = new ParametroController();
            $respuetasServicios = [];
            $wsdlParam = $parametrosWsdl->getParametroFilter('WSDLDyn');
            $form1 = json_encode($data['form1']);        
            $form1 = json_decode($form1,true);
            $form2 = json_encode($data['form2']);        
            $form2 = json_decode($form2,true);
            $form3 = json_encode($data['form3']);        
            $form3 = json_decode($form3,true);
            $form4 = json_encode($data['form4']);        
            $form4 = json_decode($form4,true);
            $form5 = json_encode($data['form5']);        
            $form5 = json_decode($form5,true);
            $form6 = json_encode($data['form6']);        
            $form6 = json_decode($form6,true);
            $form7 = json_encode($data['form7']);        
            $form7 = json_decode($form7,true);
            $form8 = json_encode($data['form8']);        
            $form8 = json_decode($form8,true);
            $form9 = json_encode($data['form9']);        
            $form9 = json_decode($form9,true);

            $language = json_encode($data['language']);        
            $language = json_decode($language,true);

            $tarifasConsulta = Tarifas::find($form5['plan']);
            $formAgreement = array_merge($form1, $form4);

            $formAgreement['strAgreementType'] = $tarifasConsulta['plan_type'];
            $formAgreement['strAgreementTypeId'] = $form5['plan'];
            $formAgreement['strPaymentTerms'] = $form5['planPeriodo'];
            $formAgreement['strAgreementStatus'] = "In Progress";
            $formAgreement['blnRecurringPayment'] = 'TRUE';
            
            if(isset($form7['medioPago']) && $form7['medioPago'] == 'TC')
                $formAgreement['dtDateofpayment'] =  $form7['fechaDebitoTc'];
            if(isset($form7['medioPago']) && $form7['medioPago'] == 'BC')
                $formAgreement['dtDateofpayment'] =  $form7['fechaDebitoBC'];
            if(isset($form7['medioPago']) && $form7['medioPago'] == 'TB')
                $formAgreement['dtDateofpayment'] =  $form7['fechaDebitoTB'];
            
            //$formAgreement['dtDateofpayment'] = $form7['fechaDebitoTc'];
            $formAgreement['strAgentcontactEmail'] = auth()->user()->email;
            $formAgreement['strCompanySponsor'] = ''; //auth()->user()->agentePadre; 
            $formAgreement['strAgent'] = auth()->user()->user_dyn;
            $formAgreement['strAgentName'] = auth()->user()->name ." ".auth()->user()->lastname;
            $formAgreement['strCompanyName'] = $wsdlParam['strCompany'];
            $formAgreement['strObservacion'] = $form8['observacionBox']; 
            $formAgreement['strECNames'] = $form4['strECFirstName'] . " " . $form4['strECLastName'];
            $formAgreement['valorTc'] = $form7['valorTc'];
            $formAgreement['planTime'] = $form5['planPeriodo'];
            $formAgreement['tresPagos'] =  isset($form5['planPeriodo']) && $form5['planPeriodo'] == 'Annual 3 payments' ?  'on' : ''; 
                        
            $formAgreement['strTypeTC'] = isset($form7['franquiciaCT']) ? $form7['franquiciaCT'] : ''; 
            $formAgreement['strNumberTC'] = isset($form7['numeroTc']) ? $form7['numeroTc'] : '';
            $formAgreement['ExpTC'] = isset($form7['expiraTc']) ? $form7['expiraTc'] : '';
            $formAgreement['strCCVTC'] = isset($form7['vvcTc']) ? $form7['vvcTc'] : '';
            $formAgreement['nombreFirma'] = $form1['strAHFirstName'].' '.$form1['strAHLastName']; 
            
            $formAgreement['strStreet1TC'] = $form6['strAddress1Street1'];  
            $formAgreement['strNameEstadoCuenta'] = $form6['strFirstName'] . " " . $form6['strLastName'];
            $formAgreement['strCityTC'] = $form6['strAddress1City'];
            $formAgreement['strStateTC'] = $form6['strAddress1StateOrProvince'];
            $formAgreement['strzipTC'] = $form6['strAddress1ZIPOrPostalCode'];
            //--$formAgreement['strPhoneNumberTC'] = $form6['strBusinessPhone'];
            //--$formAgreement['strMobilePhoneTC'] = $form6['strMobilePhone'];
            $formAgreement['strCountryTC'] = $form6['strAddress1CountrOrRegionValue'];
            //--$formAgreement['strEmailAddressTC'] = $form6['strEmail'];
            $formAgreement['strBillToNameTC'] = isset($form7['nombretc']) ? $form7['nombretc'] : '';
            $formAgreement['dtDateofpaymentTC'] = isset($form7['fechaDebitoTc']) ? $form7['fechaDebitoTc'] : '';
            $formAgreement['medioPago'] = isset($form7['medioPago']) ? $form7['medioPago'] : '';
            $formAgreement['tipoTransferencia'] = isset($form7['tipoTransferencia']) ? $form7['tipoTransferencia'] : '';
            
            $formAgreement['pagorecurrente'] = isset($form5['planValorHide']) ? number_format($form5['planValorHide'], 2, '.', ',') : '';
            $formAgreement['valorCargo'] = isset($form5['planValorCargoHide']) ? number_format($form5['planValorCargoHide'], 2, '.', ',' ) : '';
            $formAgreement['primerpago'] = isset($form5['planValorCargoHide']) ? number_format($form5['planValorCargoHide'] + $form5['planValorHide'], 2, '.', ',' ) : '';
            // DATOS de Cheque
            $formAgreement['tipoCta'] = isset($form7['tipoCta']) ? $form7['tipoCta'] : '';
            $formAgreement['bancoCheque'] = isset($form7['bancoCheque']) ? $form7['bancoCheque'] : '';
            $formAgreement['numeroRutaCheque'] = isset($form7['numeroRutaCheque']) ? $form7['numeroRutaCheque'] : '';
            $formAgreement['numeroCtaCheque'] = isset($form7['numeroCtaCheque']) ? $form7['numeroCtaCheque'] : ''; 
            // DATOS de Transferencia
            $formAgreement['transferenciaNombres'] = isset($form7['nombreTransferencia']) ? $form7['nombreTransferencia'] : ''; 
            $formAgreement['transferenciaReferencia'] = isset($form7['numeroReferencia']) ? $form7['numeroReferencia'] : ''; 
            $formAgreement['transferenciaconfirmacion'] = isset($form7['numeroConfirmacion']) ? $form7['numeroConfirmacion'] : ''; 

            $formAgreement['user_id'] = auth()->user()->id;
            $formAgreement['datosIgualContratante'] = isset($form6['infoPregunta1']) ? $form6['infoPregunta1'] : '';
            $formAgreement['dateNow'] = date('Y-m-d');
            $formAgreement['strGuidBusinessUnit'] = $wsdlParam['bunit'];
            $formAgreement['strPlaceofbirth'] = $form1['strAHAddress1CountrOrRegion'];
            $formAgreement['estadoCivilValue'] = $form1['estadoCivilValue'];
            $form6['strCompanyName'] = $wsdlParam['strCompany'];
            $form4['strCompanyName'] = $wsdlParam['strCompany'];
            
            if(isset($form7['medioPago']) && $form7['medioPago'] == 'TC')
                $formAgreement['strPrimaryPayment'] =  'Credit Card';
            if(isset($form7['medioPago']) && $form7['medioPago'] == 'BC')
                $formAgreement['strPrimaryPayment'] = 'ACH Check';
            if(isset($form7['medioPago']) && $form7['medioPago'] == 'TB')
                $formAgreement['strPrimaryPayment'] = 'Other';
            

            // consumo de WS registro de Contrato
            $this->url = $wsdlParam['url'];

            $soapWrapper = new SoapWrapper;
            $soapWrapper->add('createAgreementDetail', function ($service) {
                $service->wsdl($this->url)
                ->trace(true);
              });
              
              $response = $soapWrapper->call('createAgreementDetail.createAgreementDetail', [
                'body' => $formAgreement
              ]);
            $json = json_encode($response);
            $responseArray = json_decode($json,true);

            $respuetasServicios['createAgreementDetailData'] = $formAgreement;
            $respuetasServicios['createAgreementDetail'] = $responseArray;
            
            $formAgreement['strAgreement'] = $responseArray['createAgreementDetailResult']['AgreementNumber'];

            //$formAgreement['strAgreement'] = rand(1000, 10000);
            $newAgreement = Agreement::create($formAgreement);
            $respuetasServicios['newAgreementidBD'] = $newAgreement->id;
            
            //Preguntas Preexistencias
            $preguntas = [];
            $arrBeneficiarioPreExiste = [];

            foreach ($form3 as $key => $value) {
                
                if (strpos($key,'[]') > 0){
                    $intQ = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);  
                    $preguntas[$intQ] = $value; 
                }
            }
            $respuetasServicios['Preguntas'] = $preguntas;
            foreach ($preguntas as $idQuestion => $benIndex) {
                foreach ($benIndex as $key => $value) {
                    $arrBeneficiarioPreExiste[] = $value;
                }
            }
            $arrBeneficiarioPreExiste = array_unique($arrBeneficiarioPreExiste);
            $respuetasServicios['arrBeneficiarioPreExiste'] = $arrBeneficiarioPreExiste;
            
            // Consumo WS registro de Beneficiarios y de contacto de beneficiarios
            $arrBeneficiario = null;
            $arrBeneficiarios = null;
            $arrBeneficiarioContac = null;
            foreach ($form2 as $pos => $arrValue) {
                $arrBeneficiario['strAgreement'] = $formAgreement['strAgreement'];
                $arrBeneficiario['strRelationship'] = $arrValue[0];
                $arrBeneficiario['strRelationshipValue'] = $language == 'en' ? $arrValue[0]: $arrValue[10];
                $arrBeneficiario['strBeneficiaryFirstName'] = $arrValue[1];
                $arrBeneficiario['strBeneficiaryLastName'] = $arrValue[2];
                $arrBeneficiario['strBeneficiaryNames'] = $arrValue[1] . " " .$arrValue[2];
                $arrBeneficiario['strBeneficiaryAddress1City'] = $arrValue[7];
                $arrBeneficiario['strBeneficiaryAddress1StateOrProvince'] = $arrValue[8];
                $arrBeneficiario['strBeneficiaryAddress1CountrOrRegion'] = $arrValue[6];
                $arrBeneficiario['strBeneficiaryAddress1CountrOrRegionValue'] = $language == 'en' ? $arrValue[6] : $arrValue[12];
                $arrBeneficiario['strCountryofResidence'] = $arrValue[5];
                $arrBeneficiario['strCountryofResidenceValue'] = $language == 'en' ? $arrValue[5] : $arrValue[11];
                $arrBeneficiario['dtDateofbirth'] = $arrValue[3];
                $arrBeneficiario['strEdad'] = $arrValue[4];
                $arrBeneficiario['strBeneficiaryEmail'] = $arrValue[9];
                $arrBeneficiario['idAgreement'] = $newAgreement->id;
                $arrBeneficiario['strCompanyName'] = $wsdlParam['strCompany'];
                $arrBeneficiario['strBeneficiaryMobilePhone'] = '001';
                $arrBeneficiario['blnPreExistence'] = in_array(str_replace("'", "", $pos), $arrBeneficiarioPreExiste) ? true : false;
                $arrBeneficiario['strGuidBusinessUnit'] = $wsdlParam['bunit'];
                
                $respuetasServicios['dataCreateBeneficiary'][] = $arrBeneficiario;
                $soapWrapperBen = new SoapWrapper;
                $soapWrapperBen->add('createBeneficiaryDetails', function ($service) {
                $service->wsdl($this->url)
                    ->trace(true);
                });
              
                $responseBen = $soapWrapperBen->call('createBeneficiaryDetails.createBeneficiaryDetails', [
                    'body' => $arrBeneficiario
                ]);
                $jsonBen = json_encode($responseBen);
                $responseArrayBen = json_decode($jsonBen,true);
                $respuetasServicios['createBeneficiaryDetails'][] = $responseArrayBen;
                
                $newBeneficiario = Beneficiary::create($arrBeneficiario);
                $respuetasServicios['newBeneficiarioIdBD'][] = $newBeneficiario->id;
                $arrBeneficiarios[] = $arrBeneficiario;

                $arrBeneficiarioContac['strFirstName'] = $arrValue[1];
                $arrBeneficiarioContac['strLastName'] = $arrValue[2];
                $arrBeneficiarioContac['strAddress1City'] = $arrValue[7];
                $arrBeneficiarioContac['strAddress1StateOrProvince'] = $arrValue[8];
                $arrBeneficiarioContac['strAddress1CountrOrRegion'] = $arrValue[6];
                $arrBeneficiarioContac['strEmail'] = $arrValue[9];
                $arrBeneficiarioContac['strCompanyName'] = $wsdlParam['strCompany'];
                $arrBeneficiarioContac['strMobilePhone'] = '001';
                $arrBeneficiarioContac['strGuidBusinessUnit'] = $wsdlParam['bunit'];

                $soapWrapperBenCon = new SoapWrapper;
                $soapWrapperBenCon->add('createBeneficiaryContactDetails', function ($service) {
                $service->wsdl($this->url)
                    ->trace(true);
                });
              
                $responseBenCon = $soapWrapperBenCon->call('createBeneficiaryContactDetails.createBeneficiaryContactDetails', [
                    'body' => $arrBeneficiarioContac
                ]);
                $jsonBenCon = json_encode($responseBenCon);
                $responseArrayBenCon = json_decode($jsonBenCon,true);

                $respuetasServicios['createBeneficiaryContactDetails'][] = $responseArrayBenCon;
               
            }
            // Consumo WS registro de Titular
            $arrHolder['strFirstName'] = $form1['strAHFirstName'];
            $arrHolder['strLastName'] = $form1['strAHLastName'];
            $arrHolder['strBusinessPhone'] = $form1['strAHBusinessPhone'];
            $arrHolder['strMobilePhone'] = $form1['strAHMobilePhone'];
            $arrHolder['strEmail'] = $form1['strAHEmail'];
            $arrHolder['strAddress1Street1'] = $form1['strAHAddress1Street1'];
            $arrHolder['strAddress1City'] = $form1['strAHAddress1City'];
            $arrHolder['strAddress1StateOrProvince'] = $form1['strAHAddress1StateOrProvince'];
            $arrHolder['strAddress1ZIPOrPostalCode'] = $form1['strAHAddress1ZIPOrPostalCode'];
            $arrHolder['strAddress1CountrOrRegion'] = $form1['strAHAddress1CountrOrRegion'];
            $arrHolder['strCompanyName'] = $wsdlParam['strCompany'];
            $arrHolder['strGuidBusinessUnit'] = $wsdlParam['bunit'];

                $soapWrapperHolder = new SoapWrapper;
                $soapWrapperHolder->add('createAgreementHolderDetails', function ($service) {
                $service->wsdl($this->url)
                    ->trace(true);
                });
              
                $responseHolder = $soapWrapperHolder->call('createAgreementHolderDetails.createAgreementHolderDetails', [
                    'body' => $arrHolder
                ]);
                $jsonHolder = json_encode($responseHolder);
                $responseArrayHolder = json_decode($jsonHolder,true);

                $respuetasServicios['createAgreementHolderDetails'] = $responseArrayHolder;
              
            //consumo WS para registro de Contacto de Emergencia
            $arrEC['strFirstName'] = $form4['strECFirstName'];
            $arrEC['strLastName'] = $form4['strECLastName'];
            $arrEC['strBusinessPhone'] = $form4['strECBusinessPhone'];
            $arrEC['strMobilePhone'] = $form4['strECMobilePhone'];
            $arrEC['strEmail'] = $form4['strECEmail'];
            $arrEC['strAddress1Street1'] = $form4['strECAddress1Street1'];
            $arrEC['strAddress1City'] = $form4['strECAddress1City'];
            $arrEC['strAddress1StateOrProvince'] = $form4['strECAddress1StateOrProvince'];
            $arrEC['strAddress1ZIPOrPostalCode'] = $form4['strECAddress1ZIPOrPostalCode'];
            $arrEC['strAddress1CountrOrRegion'] = $form4['strECAddress1CountrOrRegion'];
            $arrEC['strCompanyName'] = $form4['strCompanyName'];
            $arrEC['strGuidBusinessUnit'] = $wsdlParam['bunit'];

                $soapWrapperEC = new SoapWrapper;
                $soapWrapperEC->add('createEmergencyContactDetails', function ($service) {
                $service->wsdl($this->url)
                    ->trace(true);
                });
              
                $responseEC = $soapWrapperEC->call('createEmergencyContactDetails.createEmergencyContactDetails', [
                    'body' => $arrEC
                ]);
                $jsonEC = json_encode($responseEC);
                $responseArrayEC = json_decode($jsonEC,true);

                $respuetasServicios['createEmergencyContactDetails'] = $responseArrayEC;

             //consumo WS para Tarjeta de Credito
             
             if ((isset($form7['medioPago']) && $form7['medioPago'] == 'TC')) {
                $expira = explode('-',$form7['expiraTc']);
                $arrTC['strFirstName'] = $form6['strFirstName'];
                $arrTC['strLastName'] = $form6['strLastName'];
                //$arrTC['strMobilePhone'] = $form6['strMobilePhone'];
                $arrTC['strType'] = $form7['franquiciaCT'];
                $arrTC['strName'] = $form7['nombretc'];
                $arrTC['strNumber'] = str_replace(' ', '', $form7['numeroTc']);
                $arrTC['intExpMonth'] = $expira[0];
                $arrTC['intExpYear'] = $expira[1];
                $arrTC['strCCV'] = $form7['vvcTc'];
                $arrTC['strStreet1'] = $form6['strAddress1Street1'];
                $arrTC['strCity'] = $form6['strAddress1City'];
                $arrTC['strState'] = $form6['strAddress1StateOrProvince'];
                $arrTC['strzip'] = $form6['strAddress1ZIPOrPostalCode'];
                //$arrTC['strPhoneNumber'] = $form6['strBusinessPhone'];
                $arrTC['strCountry'] = $form6['strAddress1CountrOrRegion'];
                //$arrTC['strEmailAddress'] = $form6['strEmail'];
                $arrTC['strAgreement'] = $formAgreement['strAgreement'];
                $arrTC['strBillToName'] = $form7['nombretc'];
                $arrTC['blnPrimaryCard'] = true;
                $arrTC['strGuidBusinessUnit'] = $wsdlParam['bunit'];

            //Crypt::encryptString($request->secret),
            //Crypt::decryptString($encryptedValue);


                $soapWrapperTC = new SoapWrapper;
                $soapWrapperTC->add('createCreditCardDetails', function ($service) {
                $service->wsdl($this->url)
                    ->trace(true);
                });
              
                $responseTC = $soapWrapperTC->call('createCreditCardDetails.createCreditCardDetails', [
                    'body' => $arrTC
                ]);
                $jsonTC = json_encode($responseTC);
                $responseArrayTC = json_decode($jsonTC,true);

                $newCreditCard = CreditCard::create($responseArrayTC);

                $respuetasServicios['createCreditCardDetails'] = $newCreditCard;

             }
             //consumo WS para Cheque
             
            if ((isset($form7['medioPago']) && $form7['medioPago'] == 'BC')) {
                
                $arrACH['strName'] = $form7['bancoCheque'] .' '.$formAgreement['strAgreement'];
                $arrACH['strRoutingNumber'] = $form7['numeroRutaCheque'];
                $arrACH['strAccountNumber'] = $form7['numeroCtaCheque'];
                $arrACH['strType'] =  $form7['tipoCta'];
                $arrACH['strAgreement'] = $formAgreement['strAgreement'];
                $arrACH['strPaymentGateway'] = 'Authorize.Net';
                $arrACH['strBankName'] = $form7['bancoCheque'];
                $arrACH['strFirstName'] = $formAgreement['strAHFirstName'];
                $arrACH['strLastName'] = $formAgreement['strAHLastName'];
                $arrACH['strAccount'] = null;
                $arrACH['strContact'] = null;
                $arrACH['blnPrimaryAccount'] = 'ACH Check';//true;
                $arrACH['strGuidBusinessUnit'] = $wsdlParam['bunit'];

                $soapWrapperACH = new SoapWrapper;
                $soapWrapperACH->add('createACHAccount', function ($service) {
                $service->wsdl($this->url)
                    ->trace(true);
                });
              
                $responseACH = $soapWrapperACH->call('createACHAccount.createACHAccount', [
                    'body' => $arrACH
                ]);
                $jsonACH = json_encode($responseACH);
                $responseArrayACH = json_decode($jsonACH,true);

                //$newCreditCard = CreditCard::create($arrTC);

                $respuetasServicios['createACHAccount'] = $responseArrayACH;
                $respuetasServicios['soapWrapperACH'] = $soapWrapperACH;
                $respuetasServicios['wsdl'] = $arrACH;

             }

             //consumo WS para Transferencia Bancaria
             
            if ((isset($form7['medioPago']) && $form7['medioPago'] == 'TB')) {
                
                $arrTB['strTitle'] = 'NOTE';
                $arrTB['strDescription'] = 'Entity: '. $form7['tipoTransferencia'].' <br>Fullname: '.$form7['nombreTransferencia']. ' <br>Reference Number: '. $form7['numeroReferencia'].' <br>Confirmation Number: '. $form7['numeroConfirmacion']. '<br>Debit Date: '. date('m/d/Y ',strtotime($formAgreement['dtDateofpayment']));
                $arrTB['strAgreement'] = $formAgreement['strAgreement'];
                $arrTB['strGuidBusinessUnit'] = $wsdlParam['bunit'];

                $soapWrapperTB = new SoapWrapper;
                $soapWrapperTB->add('createAgreementNotes', function ($service) {
                $service->wsdl($this->url)
                    ->trace(true);
                });
              
                $responseTB = $soapWrapperTB->call('createAgreementNotes.createAgreementNotes', [
                    'body' => $arrTB
                ]);
                $jsonTB = json_encode($responseTB);
                $responseArrayTB = json_decode($jsonTB,true);

                $respuetasServicios['createAgreementNotesTB'] = $responseArrayTB;

             }

             //Registro de nota
             if ((isset($form8['observacionBox']) && strlen($form8['observacionBox']) > 0)) {
                $noteAgreement['strTitle'] = 'NOTE';
                $noteAgreement['strDescription'] = $form8['observacionBox'];
                $noteAgreement['strAgreement'] = $formAgreement['strAgreement'];
                $noteAgreement['strGuidBusinessUnit'] = $wsdlParam['bunit'];

                $soapWrapperNote = new SoapWrapper;
                $soapWrapperNote->add('createAgreementNotes', function ($service) {
                $service->wsdl($this->url)
                    ->trace(true);
                });
            
                $responseNote = $soapWrapperNote->call('createAgreementNotes.createAgreementNotes', [
                    'body' => $noteAgreement
                ]);
                $jsonNote = json_encode($responseNote);
                $responseArrayNote = json_decode($jsonNote,true);
                $respuetasServicios['createAgreementNote'] = $responseArrayNote;
                
            }
            $respuetasServicios['Preguntas'] = $preguntas;
            $arrPreexistencias= [];
            //Preexistencias Beneficiarios
            foreach ($preguntas as $idQuestion => $benIndex) {
                $arrPregunta['strAgreement'] = $formAgreement['strAgreement'];
                $arrPregunta['idQuestion'] = $idQuestion ;
                foreach ($benIndex as $key => $value) {
                    $indexQst = array_search($value, $benIndex);
                    $arrPregunta['idBeneficiary'] = $respuetasServicios['newBeneficiarioIdBD'][$indexQst];
                    $arrPreexistencias[$idQuestion][] = $form2["'$value'"][1] . " " . $form2["'$value'"][2];
                    Preexistance::create($arrPregunta);
                }
            }
            $respuetasServicios['arrPreexistencias'] = $arrPreexistencias;
            
            $html = file_get_contents(public_path().'/form/form_'.$language.'.html'); 
            
            $arrConversionFechas = ['dtDateofbirth', 'dtDateofpaymentTC', 'dateNow'];
            foreach ($arrConversionFechas as $campo => $valor) {
                if ($formAgreement[$valor] != ''){
                $fecha = strtotime($formAgreement[$valor]);
                $formAgreement[$valor] =date('m/d/Y ',$fecha);
                }
            }
            if ($formAgreement['strBillToNameTC'] == '')
                $html = preg_replace("/#strBillToNameTC#/", $formAgreement['strNameEstadoCuenta'], $html);
            
            foreach ($formAgreement as $key => $value) {
                $html = str_replace(
                    "#".$key."#",
                    $value,
                    $html
                );
            }
            foreach ($arrBeneficiarios as $i => $benefi) {
                foreach ($benefi as $key => $value) {
                    if(strtotime($value)){
                        $html = str_replace("#".$key."_".$i."#", date('m/d/Y ',strtotime($value)), $html);
                    } else {
                        $html = str_replace("#".$key."_".$i."#", $value, $html);
                    }
                }
            }
            //&#x2713
            $arrPlanTime = ['Annual'=>'#an#',
                    'Semester'=>'#sm#',
                    'Quartely'=>'#tm#',
                    'Monthly'=>'#mm#'
                ];
            foreach ($arrPlanTime as $keyName => $planOption) {
                    if($keyName == $formAgreement['planTime'])
                        $html = preg_replace("/".$planOption."/", 'X', $html);
                    else
                        $html = preg_replace("/".$planOption."/", '', $html);
            }
            
            if (isset($formAgreement['tresPagos']) && $formAgreement['tresPagos'] == 'on')
                $html = preg_replace("/#3py#/", 'X', $html);
            else
                $html = preg_replace("/#3py#/", '', $html);
            
            if ($formAgreement['datosIgualContratante'] == 'on')
                $html = preg_replace("/#cdeq#/", 'X', $html);
            else
                $html = preg_replace("/#cdeq#/", '', $html);

            if ($formAgreement['medioPago'] == 'BC'){
                $html = str_replace("#ach#", "X", $html);
            } else {
                $html = str_replace("#ach#", "", $html);
            }
            if ($formAgreement['medioPago'] == 'TB' && $formAgreement['tipoTransferencia'] == 'zelle'){
                $html = str_replace("#zele#", "X", $html);
            } else {
                $html = str_replace("#zele#", "", $html);
            }
            if ($formAgreement['medioPago'] == 'TB' && $formAgreement['tipoTransferencia'] == 'wireTransfer'){
                $html = str_replace("#wire#", "X", $html);
            } else {
                $html = str_replace("#wire#", "", $html);
            }
            
            foreach ($arrPreexistencias as $i => $nombres) {
                $html = str_replace("#quienes_".$i."#", implode(', ', $nombres), $html);
                $html = str_replace("#sq_".$i."#", "X", $html);
                $html = str_replace("#nq_".$i."#", "", $html);
            }

            $arrFranquicia = [ 
                    'Master Card' => '#mc#',
                    'Visa' => '#vs#',
                    'American Express' => '#am#',
                    'Discover' => '#ds#'
            ];

            foreach ($arrFranquicia as $keyFranquicia => $FranquiciaOption) {
                if($keyFranquicia == $formAgreement['strTypeTC'])
                    $html = preg_replace("/".$FranquiciaOption."/", 'X', $html);
                else
                    $html = preg_replace("/".$FranquiciaOption."/", '', $html);
            }
            
            $html = preg_replace("/#sq_.#/", '', $html);
            $html = preg_replace("/#nq_.#/", 'X', $html);
            $html = preg_replace("/#quienes_.#/", '', $html);
            $html = preg_replace("/#strBeneficiaryNames_.#/", '', $html);
            $html = preg_replace("/#dtDateofbirth_.#/", '', $html);
            $html = preg_replace("/#strEdad.#/", '', $html);
            $html = preg_replace("/#strCountryofResidenceValue_.#/", '', $html);
            $html = preg_replace("/#strRelationshipValue_.#/", '', $html);
            $html = preg_replace("/#strBeneficiaryAddress1CountrOrRegionValue_.#/", '', $html);
            $html = preg_replace("/#strBeneficiaryEmail_.#/", '', $html);
            $html = preg_replace("/#strBeneficiaryAddress1City_.#/", '', $html);
            $html = preg_replace("/#strEdad_.#/", '', $html);
            
            $dataRecord = base64_decode(str_replace('data:audio/webm;codecs=opus;base64,', '', $form9));
            $myRecord = fopen(public_path().'/form/formato_'.$formAgreement['strAgreement'].'.webm', "w");
            fwrite($myRecord, $dataRecord);
            fclose($myRecord);
            $dataMail['attachFile'][0] = public_path().'/form/formato_'.$formAgreement['strAgreement'].'.webm';
            $dataMail['attachFile'][1] = public_path().'/form/formato_'.$formAgreement['strAgreement'].'.pdf';
            PDF::loadHtml($html)->setPaper('letter')->addInfo(['Subject' => $formAgreement['strAgreement']] )->save($dataMail['attachFile'][1]);
            $dataMail['sendTo'] = $formAgreement['strAHEmail'];
            $dataMail['sendToName'] = $formAgreement['strAHFirstName'] . ' ' . $formAgreement['strAHLastName'];
            $dataMail['mailAgent'] = $formAgreement['strAgentcontactEmail'];
            $dataMail['mailAgentName'] = auth()->user()->name . ' '. auth()->user()->lastname;
            $dataMail['template'] = 'emails.messageAgreement_'.$language;
            $dataMail['subject'] = $language == 'es' ? 'Afiliación Plan Funerario Internacional': 'International Funeral Plan Agreement'; 
            $sendMail = $this->sendMailAgreement($dataMail);

            
            $respuetasServicios['email'] =  $sendMail;
            //return $respuetasServicios;
            //return $formAgreement;
            return $form9;


        } catch (\Throwable $th) {
            $json = json_encode($th);
            $responseArray = json_decode($json,true);
            return $responseArray;
        }
            
    }

    public function sendMailAgreement($dataMail)
    {
        try {
            $send =  Mail::to($dataMail['sendTo'], $dataMail['sendToName'] )
                            ->bcc($dataMail['mailAgent'],$dataMail['mailAgentName']);                            
            return ($send->send(new NotifyMail($dataMail['subject'],$dataMail['attachFile'][1], $dataMail['template']) ));
        } catch (\Throwable $th) {
            return ($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agreement  $agreement
     * @return \Illuminate\Http\Response
     */
    public function show(Agreement $agreement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Agreement  $agreement
     * @return \Illuminate\Http\Response
     */
    public function edit(Agreement $agreement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Agreement  $agreement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agreement $agreement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Agreement  $agreement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agreement $agreement)
    {
        //
    }


    public function getResponseMethodWS ($metodo, $body)
    {
        try {
            $parametrosWsdl = new ParametroController();
            $wsdlParam = $parametrosWsdl->getParametroFilter('WSDLDyn');
            $this->url = $wsdlParam['url'];
            $body['strGuidBusinessUnit'] = $wsdlParam['bunit'];
            $soapWrapperHolder = new SoapWrapper;
            $soapWrapperHolder->add($metodo, function ($service) {
            $service->wsdl($this->url)
                ->trace(true);
            });
        
            $response = $soapWrapperHolder->call($metodo.'.'.$metodo, [
                'body' => $body
            ]);
            $json = json_encode($response);
            $responseArray = json_decode($json,true);

            return $responseArray;
        } catch (\Throwable $th) {
            return ($th);
        }
        
    }

}
