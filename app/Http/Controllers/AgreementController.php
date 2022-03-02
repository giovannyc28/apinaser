<?php

namespace App\Http\Controllers;
set_time_limit(360);

use App\Models\Agreement;
use App\Models\Beneficiary;
use App\Models\CreditCard;
use App\Models\Preexistance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use PhpParser\Node\Stmt\Foreach_;
use App\Http\Controllers\ParametroController;
use Artisaninweb\SoapWrapper\SoapWrapper;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;
use Mail;
use App\Mail\NotifyMail;

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
            $formAgreement = array_merge($form1, $form4);
            $formAgreement['strAgreementType'] = $form5['plan'];
            $formAgreement['strPaymentTerms'] = $opcionesTiempo[$form5['options']];
            $formAgreement['strAgreementStatus'] = "In Progress";
            $formAgreement['blnRecurringPayment'] = 'TRUE';
            $formAgreement['dtDateofpayment'] = $form7['fechaDebitoTc'];
            $formAgreement['strAgentcontactEmail'] = auth()->user()->email;
            $formAgreement['strCompanySponsor'] = auth()->user()->agentePadre;
            $formAgreement['strAgent'] = auth()->user()->user_dyn;
            $formAgreement['strCompanyName'] = $wsdlParam['strCompany'];
            $formAgreement['strObservacion'] = $form8['observacionBox'];
            $formAgreement['strECNames'] = $form4['strECFirstName'] . " " . $form4['strECLastName'];
            $form6['strCompanyName'] = $wsdlParam['strCompany'];
            $form4['strCompanyName'] = $wsdlParam['strCompany'];

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

            $respuetasServicios['createAgreementDetail'] = $responseArray;
            
            $formAgreement['strAgreement'] = $responseArray['createAgreementDetailResult']['AgreementNumber'];
    
            $newAgreement = Agreement::create($formAgreement);
            $respuetasServicios['newAgreementidBD'] = $newAgreement->id;
            
            // Consumo WS registro de Beneficiarios y de contacto de beneficiarios
            $arrBeneficiario = null;
            $arrBeneficiarios = null;
            $arrBeneficiarioContac = null;
            foreach ($form2 as $pos => $arrValue) {
                $arrBeneficiario['strAgreement'] = $formAgreement['strAgreement'];
                $arrBeneficiario['strRelationship'] = $arrValue[0];
                $arrBeneficiario['strBeneficiaryFirstName'] = $arrValue[1];
                $arrBeneficiario['strBeneficiaryLastName'] = $arrValue[2];
                $arrBeneficiario['strBeneficiaryNames'] = $arrValue[1] . " " .$arrValue[2];
                $arrBeneficiario['strBeneficiaryAddress1City'] = $arrValue[7];
                $arrBeneficiario['strBeneficiaryAddress1StateOrProvince'] = $arrValue[8];
                $arrBeneficiario['strBeneficiaryAddress1CountrOrRegion'] = $arrValue[6];
                $arrBeneficiario['strCountryofResidence'] = $arrValue[5];
                $arrBeneficiario['dtDateofbirth'] = $arrValue[3];
                $arrBeneficiario['strEdad'] = $arrValue[4];
                $arrBeneficiario['idAgreement'] = $newAgreement->id;
                $arrBeneficiario['strCompanyName'] = $wsdlParam['strCompany'];
                $arrBeneficiario['strBeneficiaryMobilePhone'] = '001';

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
                $arrBeneficiarioContac['strCompanyName'] = $wsdlParam['strCompany'];
                $arrBeneficiarioContac['strMobilePhone'] = '001';

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

                $respuetasServicios['createEmergencyContactDetails'] = $responseArrayHolder;

             //consumo WS para tarjeta de credito
            $expira = explode('-',$form7['expiraTc']);
            $arrTC['strFirstName'] = $form6['strFirstName'];
            $arrTC['strLastName'] = $form6['strLastName'];
            $arrTC['strMobilePhone'] = $form6['strMobilePhone'];
            $arrTC['strType'] = $form7['franquiciaCT'];
            $arrTC['strName'] = $form7['nombretc'];
            $arrTC['strNumber'] = $form7['numeroTc'];
            $arrTC['intExpMonth'] = $expira[0];
            $arrTC['intExpYear'] = $expira[1];
            $arrTC['strCCV'] = $form7['vvcTc'];
            $arrTC['strStreet1'] = $form6['strAddress1Street1'];
            $arrTC['strCity'] = $form6['strAddress1City'];
            $arrTC['strState'] = $form6['strAddress1StateOrProvince'];
            $arrTC['strzip'] = $form6['strAddress1ZIPOrPostalCode'];
            $arrTC['strPhoneNumber'] = $form6['strBusinessPhone'];
            $arrTC['strCountry'] = $form6['strAddress1CountrOrRegion'];
            $arrTC['strEmailAddress'] = $form6['strEmail'];
            $arrTC['strAgreement'] = $formAgreement['strAgreement'];
            $arrTC['strBillToName'] = $form7['nombretc'];

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

                $newCreditCard = CreditCard::create($arrTC);

                $respuetasServicios['createCreditCardDetails'] = $responseArrayTC;

            
            $preguntas = [];
            foreach ($form3 as $key => $value) {
                
                if (strpos($key,'[]') > 0){
                    $intQ = (int) filter_var($key, FILTER_SANITIZE_NUMBER_INT);  
                    $preguntas[$intQ] = $value; 
                }
            }

            $respuetasServicios['Preguntas'] = $preguntas;
            $arrPreexistencias= [];
            foreach ($preguntas as $idQuestion => $benIndex) {
                $arrPregunta['strAgreement'] = $formAgreement['strAgreement'];
                $arrPregunta['idQuestion'] = $idQuestion ;
                foreach ($benIndex as $key => $value) {
                    $arrPregunta['idBeneficiary'] = $respuetasServicios['newBeneficiarioIdBD'][$value];
                    $arrPreexistencias[$idQuestion][] = $form2["'$value'"][1] . " " . $form2["'$value'"][2];
                    Preexistance::create($arrPregunta);
                }
            }
            $respuetasServicios['arrPreexistencias'] = $arrPreexistencias;

            $html = file_get_contents(public_path().'/form/form_es.html'); 
            foreach ($formAgreement as $key => $value) {
                $html = str_replace(
                    "#".$key."#",
                    $value,
                    $html
                );
            }
            foreach ($arrBeneficiarios as $i => $benefi) {
                foreach ($benefi as $key => $value) {
                    $html = str_replace("#".$key."_".$i."#", $value, $html);
                }
            }
            //&#x2713

            foreach ($arrPreexistencias as $i => $nombres) {
                $html = str_replace("#quienes_".$i."#", implode(', ', $nombres), $html);
                $html = str_replace("#sq_".$i."#", "X", $html);
                $html = str_replace("#nq_".$i."#", "", $html);
            }
            $html = preg_replace("/#sq_.#/", '', $html);
            $html = preg_replace("/#nq_.#/", 'X', $html);
            $html = preg_replace("/#quienes_.#/", '', $html);
            $html = preg_replace("/#strBeneficiaryNames_.#/", '', $html);
            $html = preg_replace("/#dtDateofbirth_.#/", '', $html);
            $html = preg_replace("/#strEdad.#/", '', $html);
            $html = preg_replace("/#strCountryofResidence_.#/", '', $html);
            $html = preg_replace("/#strRelationship_.#/", '', $html);
            $html = preg_replace("/#strBeneficiaryAddress1CountrOrRegion_.#/", '', $html);
            $html = preg_replace("/#strEdad_.#/", '', $html);
            
            

            $dataMail['attachFile'] = public_path().'/form/formato_'.$formAgreement['strAgreement'].'.pdf';
            PDF::loadHtml($html)->setPaper('letter')->addInfo(['Subject' => $formAgreement['strAgreement']] )->save($dataMail['attachFile']);
            $dataMail['sendTo'] = 'giovannyc28@gmail.com';
            $dataMail['sendToName'] = $formAgreement['strAHFirstName'] . ' ' . $formAgreement['strAHLastName'];
            $dataMail['mailAgent'] = $formAgreement['strAgentcontactEmail'];
            $dataMail['mailAgentName'] = auth()->user()->name . ' '. auth()->user()->lastname;
            $sendMail = $this->sendMailAgreement($dataMail);
            //$respuetasServicios['$genPdf'] = $genPdf;
            $respuetasServicios['sendMail'] = $sendMail;
            return $respuetasServicios;
            //return $form2;


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
            return ($send->send(new NotifyMail('Envio de Cuenta Contrato',$dataMail['attachFile'])));
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
}
