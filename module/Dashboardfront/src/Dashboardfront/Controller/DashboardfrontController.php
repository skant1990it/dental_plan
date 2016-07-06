<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Dashboardfront\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Dashboardfront\Model\Dashboardfront;    
use Dashboardfront\Model\DashboardfrontTable;
use Zend\Session\Container;
use Zend\Mail\Message;
use Zend\Mime;
use Zend\Mail\Transport\Sendmail ; 
use Zend\dental\Service\AES;

use ZF2AuthAcl\Utility\UserPassword;

use Zend\Mvc\MvcEvent;
class DashboardfrontController extends AbstractActionController
{
  public $inputKey = "2345432AD12H";
  public  $blockSize = 256;
  public $publishable_key = "pk_test_NLgzhGanpB897rKprLEh2dGq";
  public $stripesecret_key = 'sk_test_O02d4yUy6mq251y3wRD5lG6r';
  public function __construct(){
   $stripe['secret_key'] = ('sk_test_O02d4yUy6mq251y3wRD5lG6r');
   $stripe['publishable_key'] = ('pk_test_NLgzhGanpB897rKprLEh2dGq');
  \Stripe\Stripe::setApiKey($stripe['secret_key']);
   $inputKey = "2345432AD12H";
   $blockSize = 256;

}

  public function indexAction()
	{ 
	 return new ViewModel();
	}
  public function aboutusAction()
	{    
    return new ViewModel();
	}
   public function riskreferralsAction()
  {    
    return new ViewModel();
  }
     public function workwithusAction()
  {    
    return new ViewModel();
  }
    public function featuresAction()
	{    
    return new ViewModel();
	}
    public function doctordashboardAction()
  {    
    return new ViewModel();
  }
    public function pricingAction()
	{    
		
     return new ViewModel();
	}
      public function termsconditionAction()
  {    
    
     return new ViewModel();
  }
      public function privacypolicyAction()
  {    
    
     return new ViewModel();
  }
  public function importzipAction(){
	echo $target_dir = $_SERVER['DOCUMENT_ROOT']."/upload/zip_code_database.csv";
	
		/*
		 * csv read and insert in db
		 * 
		 */
		$file = fopen($target_dir, "r");
		//$sql_data = "SELECT * FROM prod_list_1 ";
		
		$session = new Container('User');
		$docid  =$session->offsetGet('userId');
		
		
		$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteFeeSchedule($docid);
		
		$count = 0;                                         // add this line
		while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
		{
			//print_r($emapData);
			//exit();
			$count++;                                      // add this line

			if($count>1){
				$data=array('zip'=>$emapData[0],'city'=>$emapData[2],'state'=>$emapData[4]);		
				$doctordetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->zipInsert($data);
			}                                              // add this line
		}
		echo '1';
	exit;
  }
  public function feeScheduleAction(){
	$session = new Container('User');
	$docid  =$session->offsetGet('userId');
	
	$target_dir = $_SERVER['DOCUMENT_ROOT']."/upload/";
	//$name = $_POST['name'];
	//print_r($_FILES);
	$fname = $docid.'_'.rand(). basename($_FILES["file"]["name"]);
	$target_file = $target_dir.$fname;
	$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
	if(in_array($_FILES['file']['type'],$mimes)){
		move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);

		echo $fname;
	}else
		echo '0';
		
	exit;
  }
  public function feemapAction(){
		$data= urldecode($_GET['name']);
		//$post_date = file_get_contents("php://input");
		//$data = json_decode($post_date,true);			
		$session = new Container('User');
		$docid  =$session->offsetGet('userId');
		$target_dir = $_SERVER['DOCUMENT_ROOT']."/upload/".$data;
		$file = fopen($target_dir, "r");
		//$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteFeeSchedule($docid);
		$line = fgetcsv($file);
		//print_r($line);
		echo json_encode((object) $line);
		exit;
		
		/*
		$char = array("'");
		$rep = array("");
		$count = 0;                                         // add this line
		while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
		{
			//print_r($emapData);
			//exit();
			$count++;                                      // add this line
			
			if($count>1){
				$data=array('doc_id'=>$docid,'ada_code'=>str_replace($char,$rep,$emapData[0]),'desc'=>str_replace($char,$rep,$emapData[1]),'user_friendly_desc'=>str_replace($char,$rep,$emapData[2]),'category'=>$emapData[3]);	
				$doctordetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->feeInsert($data);
			}                                              // add this line
		}*/
  }
   public function feemapsubmitAction(){
  
		
		$post_date = file_get_contents("php://input");
		$data = json_decode($post_date,true);  
	
		$session = new Container('User');
		$docid  =$session->offsetGet('userId');
		$target_dir = $_SERVER['DOCUMENT_ROOT']."/upload/".$data['file'];
		$file = fopen($target_dir, "r");
		$char = array("'");
		$rep = array("");
		$count = 0;
		$ada=$data['ada'];
		$desc=$data['description'];
		$pricing=$data['pricing'];
		if($ada==$desc || $desc==$pricing || $pricing==$ada){
			echo '0';
			exit;
		}
		if($ada=='' || $desc=='' || $pricing==''){
			echo '0';
			exit;
		}
		
		$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteFeeSchedule($docid);
		
		while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
		{
			//print_r($emapData);
			//exit();
			$count++;                                      // add this line
			
			if($count>1){
				$data=array('doc_id'=>$docid,'ada_code'=>str_replace($char,$rep,$emapData[$ada]),'desc'=>str_replace($char,$rep,$emapData[$desc]),'price'=>$emapData[$pricing]);	
				//print_r($data);
				$doctordetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->feeInsert($data);
			}                                              // add this line
		}
		echo $count;
		exit;
  }
  public function bankingdoctorAction()
	{    
		$doctorid= $this->params()->fromRoute('id',null); 
		if(isset($doctorid)){
		   $doctordetails=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->doctorDetailsFromId($doctorid);
		}
		$request = $this->getRequest() ;    // getting current request object
		$success_msg='';
		
		
        if ($request->isPost())
        {   // echo 'submitted'; exit;
			
			$session = new Container('User');
			$userdatails = $session->offsetGet('userdetail');
			
			$doctorid = $userdatails['doc_id'];
			$post_date = file_get_contents("php://input");
			$data = json_decode($post_date,true);
			

			$doc_data['deposit_account_no']= $data['account_number'];
			$doc_data['deposit_routing_no']= $data['routing_number'];
			$doc_data['bank_account_type']= '';
			$doc_data['deposit_acc_holder_name']= $data['name'];		
			$doc_data['doc_id'] = $doctorid;
			
			$stripeToken = $request->getPost('stripeToken');  
			/*$doc_data = array();  
			$doc_data['bank_routing_number']= $request->getPost('bank_routing_number');
			$doc_data['bank_account_number']= $request->getPost('bank_account_number');
			$doc_data['bank_account_type']= $request->getPost('bank_account_type');
			$doc_data['legal_name']= $request->getPost('legal_name');		
			$doc_data['doc_id']= $request->getPost('doc_id');   
			* */       
			$savedoctor=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->savedoctorbank($doc_data);			
			$doctordetails=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->doctorDetailsFromId($doctorid);
			//echo '<pre>'; print_r($doctordetails);
          
				//close for dev
				/*\Stripe\Stripe::setApiKey($this->stripesecret_key);
				$account = \Stripe\Account::create(array(
				"managed" => false,
				"country" => "US",
				"email" => $doctordetails[0]['doc_email']
				));
				
				$aes = new \MyModule\AES($account->id, $this->inputKey, $this->blockSize);
				$enc_acid = $aes->encrypt();
				*///close for dev
								
				//$enc_acid = $account->id;	
							
				//echo '<pre>';
				//print_r($account);
				/*$cu = \Stripe\Account::retrieve($account->id);
				$bankdetails = $cu->external_accounts->create(array("external_account" => $stripeToken));
				
				$aes = new \MyModule\AES($bankdetails->id, $this->inputKey, $this->blockSize);
				$enc_acid = $aes->encrypt();*/
				
				//$aes->setData($enc);
				//$subs_id=$enc ;
				
				
				
				$doc_ac_data = array();
				//$doc_ac_data['account_id'] = $enc_acid;//close for dev
				$doc_ac_data['account_id'] = '';
				$doc_ac_data['bank_acc_id'] = '';
				$doc_ac_data['doc_id'] = $doc_data['doc_id'];
				
				$savedoctorac=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->saveDoctorBankAc($doc_ac_data);
				$success_msg='Bank account saved successfully.';
				/*$update_qry = "update doctor_details set account_id='".mysqli_real_escape_string($cn,myencrypt($account->id))."' where doc_id=".mysqli_real_escape_string($cn,$doc_id);
				mysqli_query($cn,$update_qry);*/
				
				/*$update_qry = "update doctor_details set bank_acc_id='".mysqli_real_escape_string($cn,myencrypt($bankdetails->id))."' where doc_id=".mysqli_real_escape_string($cn,$doc_id);
				mysqli_query($cn,$update_qry);*/
				echo 1;exit;
				
				//  print_r($request->getPost());die ;
        
        }
        $doctordetails =array();
        //print_r($doctordetails);
     return new ViewModel(array('success_msg'=>$success_msg,'doctorid'=>$doctorid,'doctordetails'=>$doctordetails,'publishable_key'=>$this->publishable_key));
  }
  public function planchoiceAction()
  {  
	 $session = new Container('User');
	 $doc_id  =$session->offsetGet('userId');	  
     //$listplans= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlansListing();      
     //$listplanservice= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listplanservice(); 
		//echo '<pre>';print_r($listplans);
		
	 $listplans= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->DoctorPlansService($doc_id);
     return new ViewModel(array('listplans'=>$listplans,'listplanservice'=>$listplanservice));
  }
   public function planselectionAction()
  {  $planservicetid = $this->params()->fromRoute('id',null); 
     $listplanservice= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlanDetails($planservicetid); 
     $planservicedetails  = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlanserviceDetails($planservicetid); 
     return new ViewModel(array('listplanservice'=>$listplanservice,'planservicedetails'=>$planservicedetails));
  }

  public function patienteditAction()
  {  
	  
    $planid = $this->params()->fromRoute('id',null);
    //$parentid = $this->params()->fromRoute('idd',null);  
	//$planid =6;
    $session = new Container('User');
	$docid  =$session->offsetGet('userId');
	$request= $this->getRequest();
	if($request->isPost()){
	
		$post_date = file_get_contents("php://input");
		$data = json_decode($post_date,true);
		//print_r($data);exit;
		$returndata = array();
		$parent_id = 0;
		$fp = 0;
		foreach($data['first_name'] as $key=>$val){	
					
				//echo $data['last_name'][$key];exit;
				
			// print_r($request->getPost());die;
			$plan_id=$data['planid'];
			// echo "fdf";
			//echo $plan_id;die;
			$plan_cycle='';
			$patient_salutation='';
			$patient_firstname=$val;
			$patient_lastname=$data['last_name'][$key];
			$patient_dob=$data['dob'][$key];
			$patient_ssn='';
			$patient_sex=$data['sex'][$key];
			$patient_city=$data['city'][$key];
			$patient_state=$data['state'][$key];

			$patient_address=$data['address'][$key];
			$patient_zip=$data['zip'][$key];
			$patient_phone=$data['phone'][$key];
			$patient_mobile='';
			$patient_email=$data['email'][$key];
			$patient_password='';
			$pat_id='';
			$addon='';
			
			$patient_status='on';
			$patient_consent='off';
			if($patient_status=='on')
			$pstatus = 1;
			else
			$pstatus = 0;

			if($patient_consent=='on')
			$pconsent = 1;
			else
			$pconsent = 0;
                   
             
 
		  $returnid=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertPatient($plan_id,$plan_cycle,$patient_salutation,$patient_firstname,$patient_lastname,$patient_dob,$patient_ssn,$patient_sex,$patient_address,$patient_zip,$patient_phone,$patient_mobile,$patient_email,$patient_password,$pat_id,$addon,$parent_id,$docid,$pstatus,$pconsent,$patient_city,$patient_state); 
		 // echo  $pat_id ;die;
		 
			if($fp == 0){
				
				$fp=1;
				$parent_id=$returnid;
			}
		}
		
		$returndata['success'] = "1";
       //get parent
       $patientdetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetailsById($parent_id);  
       
       
       if($patientdetails[0]['parent_id']==0){
           $plandetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlanDetails($planid);
				$plan_name =$plandetails['0']['plan_name'] ;
				$session = new Container('User');
				$docid  =$session->offsetGet('userId');
				$get_doc_details= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($docid);
				$doc_name = $get_doc_details[0]['doc_firstname'].' '.$get_doc_details[0]['doc_lastname'];
				$doc_phone = $get_doc_details[0]['doc_phone'];
				$doc_address = $get_doc_details[0]['doc_address'].','.$get_doc_details[0]['doc_zip'];

				$ragisteremail ='admin@dentalplansoftware.com' ;
				// $email ='skant1990it@gmail.com' ;
				$maillink='<table cellpadding="0" cellspacing="0" border="0" width="950" align="center">
				<tr>
				<td valign="top" colspan="3" height="30"></td>
				</tr>
				<tr>
				<td valign="top" width="30px;"></td>
				<td valign="top">
				<table cellpadding="0" cellspacing="0" border="0" width="890" align="center">
				<tr>
				  <td valign="top" colspan="2"><img src="http://dentalplansoftware.com/front_img/logo3.png" width="889" height="136" alt="" title="" /></td>
				</tr>
				<tr>
					<td valign="top" colspan="2" height="30"></td>
				</tr>
				<tr>
				  <td valign="top" width="445"></td>
					<td valign="top" width="445">
					  <table cellpadding="0" cellspacing="0" border="0" width="445">
							<tr>
								<td valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:34px; color:#626363">Patient Name:&nbsp;<strong style="color:#2aa7e2;">'.$patientdetails[0]["patient_firstname"].' '.$patientdetails[0]["patient_lastname"].'</strong></td>
							</tr>
							<tr>
								<td valign="top" colspan="2" height="20"></td>
							</tr>
							<tr>
								<td valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:29px; color:#626363">Patient ID:&nbsp;<strong style="color:#2aa7e2;">'.$patientdetails[0]["patient_email"].'</strong></td>
							</tr>
							<tr>
								<td valign="top" colspan="2" height="20"></td>
							</tr>
							<tr>
								<td valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:29px; color:#626363">Plan Type:&nbsp;<strong style="color:#2aa7e2;">'.$plan_name.'</strong></td>
							</tr>
							<tr>
								<td valign="top" colspan="2" height="35"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td valign="top" colspan="2" height="30"></td>
				</tr>
				<tr>
				  <td valign="top" width="445" bgcolor="#2aa7e2"></td>
					<td valign="top" width="445" bgcolor="#2aa7e2">
					  <table cellpadding="0" cellspacing="0" border="0" width="445">
						  <tr>
								<td valign="top" colspan="2" height="30"></td>
							</tr>
							<tr>
							  <td valign="top" width="50"><img src="http://dentalplansoftware.com/front_img/front_07.png" width="39" height="39" /></td>
								<td valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:29px; color:#ffffff">'.$doc_name.'</td>
							</tr>
							<tr>
								<td valign="top" colspan="2" height="20"></td>
							</tr>
							<tr>
							  <td valign="top"><img src="http://dentalplansoftware.com/front_img/front_07-03.png" width="39" height="39" /></td>
								<td valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:29px; color:#ffffff">'.$doc_phone.'</td>
							</tr>
							<tr>
								<td valign="top" colspan="2" height="20"></td>
							</tr>
							<tr>
							  <td valign="top"><img src="http://dentalplansoftware.com/front_img/front_07-04.png" width="39" height="39" /></td>
								<td valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:29px; color:#ffffff">'.$doc_address.'</td>
							</tr>
							<tr>
								<td valign="top" colspan="2" height="35"></td>
							</tr>
						</table>
					</td>
				</tr>
				</table>
				</td>
				<td valign="top" width="30px;"></td>
				</tr>
				<tr>
				<td valign="top" colspan="3" height="30"></td>
				</tr>
				</table>';    
					/* code for mail start   */

					 
				$config = $this->getServiceLocator()->get('config');
				 /* code for mail start   */
					/*$footer = "The Admin Team";
					$html = new Mime\Part($maillink);
					$html->type = Mime\Mime::TYPE_HTML;
					$text = new Mime\Part($footer);
					$text->type = Mime\Mime::TYPE_TEXT;
					$maillink = new Mime\Message();
					$maillink->setParts(array($html, $text));
					$message = new \Zend\Mail\Message();
					$message->setBody($maillink);
					$message->setFrom($config['email_sender']['email']);
					$message->setSubject("Welcome to dentalplansoftware.com");
					$message->addTo($patient_email) ;
					$SmtpOptions =  new \Zend\Mail\Transport\SmtpOptions();
					$SmtpOptions->setHost($config['smtp_settings']['host'])->setPort($config['smtp_settings']['port'])
						  ->setConnectionClass($config['smtp_settings']['connection_class'])
						  ->setName('smtp.gmail.com')
						  ->setConnectionConfig(array(
						   'username'=>$config['smtp_settings']['username'],
						   'password'=>$config['smtp_settings']['password'],
						   'ssl'     =>$config['smtp_settings']['ssl']

					));
					$transport  =  new \Zend\Mail\Transport\Smtp($SmtpOptions) ;
					$transport->send($message);*/
				  /* code for mail end   */
       }      //    patient detail (parent id==0) if close here .
       
       $returndata['url'] = "/dashboardfront/payccurl/$parent_id";  
						/*if($addon==1){
						return $this->redirect()->toUrl("/dashboardfront/patientedit/$planid/$parentid");
						}else{
						return $this->redirect()->toUrl("/dashboardfront/payccurl/$parentid");
						}*/
						// else part of $pat_id==0 close here ..
						/* $chkpid= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->getMainPatient($patient_id);
						if($chkpid!=$patient_id){ 
						return $this->redirect()->toUrl("/dashboardfront/payccurl/$chkpid");
						}else { 
						return $this->redirect()->toUrl("/dashboardfront/payccurl/$patient_id");
						}*/
						//$patientplan= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientPlanDetails($patientid,$docid);
						// $this->redirect()->toUrl("/dashboardfront/payccurl/");  
   
   echo json_encode($returndata);exit;
   }    // post bracket close here ...
     return new ViewModel(array('planid'=>$planid));
     //return new ViewModel(array('planid'=>$planid,'patientid'=>$patientid,'parentid'=>$parentid,'addon'=>$addon));
}
 public function managepatienteditAction()
  {    
     $planid = $this->params()->fromRoute('id',null);
     $parentid = $this->params()->fromRoute('idd',null);  
     $patientid = $this->params()->fromRoute('iddd',null); 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
       $patientdetail= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetails($patientid,$docid); 
     $request= $this->getRequest();
     if($request->isPost()){
      $plan_id=$request->getPost('plan_id');
     // echo "fdf";
      //echo $plan_id;die;
      $plan_cycle=$request->getPost('plan_cycle');
      $patient_salutation=$request->getPost('patient_salutation');
      $patient_firstname=$request->getPost('patient_firstname');
      $patient_lastname=$request->getPost('patient_lastname');
      $patient_dob=$request->getPost('patient_dob');
      $patient_ssn=$request->getPost('patient_ssn');
      $patient_sex=$request->getPost('patient_sex');
      $patient_address=$request->getPost('patient_address');
      $patient_zip=$request->getPost('patient_zip');
      $patient_phone=$request->getPost('patient_phone');
      $patient_mobile=$request->getPost('patient_mobile');
      $patient_email=$request->getPost('patient_email');
      $patient_password=$request->getPost('patient_password');
      $patientid=$request->getPost('pat_id');
      $addon=$request->getPost('addon');
      $parentid=$request->getPost('parent_id');
      $patient_status=$request->getPost('patient_status');
      


      $patient_consent=$request->getPost('patient_consent');
      $parentid1=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertPatient($planid,$plan_cycle,$patient_salutation,$patient_firstname,$patient_lastname,$patient_dob,$patient_ssn,$patient_sex,$patient_address,$patient_zip,$patient_phone,$patient_mobile,$patient_email,$patient_password,$patientid,$addon,$parentid,$docid,$patient_status,$patient_consent); 
       //get parent
       $patientdetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetailsById($parentid);  
       $parent_id  =$patientdetails[0]['parent_id'] ;
       if($parent_id!=0)
        $parentid = $parent_id;
       if($addon==1){
       return $this->redirect()->toUrl("/dashboardfront/patientedit/$parentid");
     }
     $patientplan= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientPlanDetails($patientid,$docid); 
     $this->redirect()->toUrl("/dashboardfront/payccurl/$parentid");
     }
     return new ViewModel(array('planid'=>$planid,'patientid'=>$patientid,'patientplan'=>$patientdetail,'parentid'=>$parentid,'addon'=>$addon));
  }

  public function viewpatientAction()
  {    
     $patid = $this->params()->fromRoute('id',null); 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $patient= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetails($patid,$docid);  
     $patientonly= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatientsId();   
     return new ViewModel(array('patient'=>$patient,'patientonly'=>$patientonly,'patid'=>$patid));
  }
  public function patientpaymentsAction()
  {    
     $patid = $this->params()->fromRoute('id',null); 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     
     //code not ready
    /* 
	$pat_pays_qry = mysqli_query($cn,"select * from patients_payments where patient_id=".mysqli_real_escape_string($cn,$_GET['pat_id'])." order by pay_date desc");

	$request= $this->getRequest();
     if($request->isPost()){
		extract($_POST);
		if($pat_id) {
	$get_details_qry = mysqli_query($cn,"select pa.patient_name,p.plan_id,p.plan_price from patients pa, plans p where pa.plan_id = p.plan_id and pa.patient_id=".mysqli_real_escape_string($cn,$_GET['pat_id']));
	$get_detail_r = mysqli_fetch_object($get_details_qry);

		$insert_qry = "insert into patients_payments(`patient_id`, `plan_id`, `amount`, `payer_email`, `pay_cc`, `pay_expiry`, `pay_cvv`, `pay_date`, `pay_status`) values('".mysqli_real_escape_string($cn,$pat_id)."','".mysqli_real_escape_string($cn,$get_detail_r->plan_id)."','".mysqli_real_escape_string($cn,$get_detail_r->plan_price)."','".mysqli_real_escape_string($cn,$payer_email)."','".mysqli_real_escape_string($cn,$cc_number)."','".mysqli_real_escape_string($cn,$cc_expiry)."','".mysqli_real_escape_string($cn,$cc_cvv)."',now(),'1')";
		mysqli_query($cn,$insert_qry);
		header('location:patients.php');
		} 
	}
	$patient= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetails($patid,$docid);  
    $patientonly= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatientsId(); 
    */  
    return new ViewModel(array('pat_pays_qry'=>array(),'patid'=>$patid));

  }
  public function patientidcardAction(){
	 $patid = $this->params()->fromRoute('id',null); 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     return new ViewModel(array('pat_r'=>array(),'patid'=>$patid,'doc_details'=>array(),'plan_name'=>array()));
  }
  public function payccurlAction()
  {    
    
    $patid = $this->params()->fromRoute('id',null); 
    $session = new Container('User');
    $docid  =$session->offsetGet('userId');
    $patient= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetails($patid,$docid);
    $patientonlydetail= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetailsById($patid);    // only patient table detail come here .
  
    $patientonly= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatientsId();
    $request= $this->getRequest();
     if($request->isPost())
      {
		  $this->redirect()->toUrl("/dashboardfront/patient");        
                                        // post bracket open here
       $number= $request->getPost('number');
       $exp_month= $request->getPost('exp_month');
       $exp_year= $request->getPost('exp_year');
       $patid= $request->getPost('pat_id');
       $stripeToken =  $request->getPost('stripeToken');
       $encriptnumber = $this->myownenc(substr($number,-4));
      if($patid) {       
         // check if plan exists, otherwise make a plan
          $patientonlydetail= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetailsById($patid);    // only patient table detail come here .
          $getmainpatientid = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetailsById($patid);  //   this table only get patient_id and parent_id .
         if($getmainpatientid[0]['parent_id']>0)
         $ptype = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->getPatientAddons($getmainpatientid[0]['parent_id']);
         if($ptype>0) {
          $p_type = 'Add'.$ptype;
          } else {
         $p_type = 'Ind1';
         }

       // plan cycle start
       $plan_bill_cycle[1] = 'Month';
       $plan_bill_cycle[2] = 'Year';
       // plan cycle end
      $cycle = $plan_bill_cycle[$patientonlydetail[0]['plan_cycle']];
      $plandetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlanDetails($patientonlydetail[0]['plan_id']);
      $doctordetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($plandetails[0]['doc_id']);   
      $plancode_name = $plandetails[0]['plan_name'].'_'.$doctordetails[0]['doc_firstname'].'_'.$doctordetails[0]['doc_lastname'].'_'.$plandetails['plan_id'].'_'.$p_type.'_'.$cycle;   // here get plan code name of stripe_plan
      $stripeplandetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->chkStripePlanExists($plancode_name);
        if(!$stripeplandetails[0]['plan_code']) {     // stripe plan detail open here
        $plan_price = 0;
        $get_addons = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->getPatientAddons($patient_id);
        $get_pat_details= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetailsById($patid); 
        $plan_qry= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlanDetails($get_pat_details[0]['plan_id']); 
          if($plan_qry[0]['plan_id']) {     // plan_query  bracket open here
            if($get_pat_details[0]['plan_cycle']==1) {
             $indvidual_monthly_price = $plan_qry[0]['plan_monthly_price'];
             if($get_addons>0)
              $addon_monthly_price = $get_addons*$plan_qry[0]['addon_monthly_price'];
             $plan_price = $indvidual_monthly_price+$addon_monthly_price;
             } else if($get_pat_details[0]['plan_cycle']==2) {
             $indvidual_yearly_price = $plan_qry[0]['plan_yearly_price'];
             if($get_addons>0)
             $addon_yearly_price = $get_addons*$plan_qry[0]['addon_yearly_price'];
             $plan_price = $indvidual_yearly_price+$addon_yearly_price;
             }
            }       // plan_query  bracket close here
       $plan_price_incents = $plan_price*100;
       $plancyclename = strtolower($plan_bill_cycle[$get_pat_details[0]['plan_cycle']]);
       $normalplanname=str_replace('_',' ',$plancode_name);
      $mkplan = \Stripe\Plan::create(array(
       "amount" => $plan_price_incents,
       "interval" => $plancyclename,
       "name" => $normalplanname,
       "currency" => "usd",
       "id" => $plancode_name)
      );
    $stripePlanInsert= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->stripePlanInsert($plancode_name,$plan_price); 
}     // stripe plan detail close here
$cust = \Stripe\Customer::create(array(
  "email" => $email,
  "description" => "Customer - ".$email.' of doctor - '.$session->offsetGet('userId'),
  "source" => $stripeToken
));
$cu = \Stripe\Customer::retrieve($cust->id);
$subs = $cu->subscriptions->create(array("plan" => $plancode_name));
$encryptcustid=$this->myownenc($cust->id);
$encryptsubsid=$this->myownenc($subs->id);
$patientUpdate= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->patientUpdate($encryptcustid,$encryptsubsid,$patid); 
if($subs->id) {    // subs_id bracket open here
$updatePatientdetail= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updatePatientDetails($patid,$encriptnumber,$exp_month,$exp_year); 

$doctordetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($plandetails[0]['doc_id']);
 $this->redirect()->toUrl("/dashboardfront/patient");
     }   // subs_id bracket close here
   }     // pat id bracket close here 
 }    // post bracket close here 
    // print_r($patientonlydetail);die;
    $cc_number=$patientonlydetail[0]['cc_number'] ;
   // print_r($cc_number);
    if($cc_number){
     $inputText=$cc_number;
     $aes = new \MyModule\AES($inputText,$this->inputKey,$this->blockSize);
     $cc_numberAfterDecription=$aes->decrypt();
      }
     return new ViewModel(array('patient'=>$patientonlydetail,'cc_numberAfterDecription'=>$cc_numberAfterDecription,'patientonly'=>$patientonly,'patid'=>$patid,'publishablekey'=>$this->publishable_key));
}

public function termsconditionpatientAction()
{
	$request= $this->getRequest();
	$pat_id = $this->params()->fromRoute('id',null);
	$session = new Container('User');
    $docid  =$session->offsetGet('userId');
     
	$patient= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetails($pat_id,$docid);
	//print_r($patient);exit;
	$plan = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlanDetails($patient[0]['plan_id']);
	$planservice = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlanserviceDetails($patient[0]['plan_id']);
	if($request->isPost()){
	
		$post_date = file_get_contents("php://input");
		$data = json_decode($post_date,true);
		$data['doc_id']= $docid;
		$save = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertTermsAndCondPat($data);
		
		echo '1'; 
		exit;
	}
	
	
	//print_r($patient);
	return new ViewModel(array('plan_id'=>$patient[0]['plan_id'],'plan'=>$plan,'planservice'=>$planservice,'patient'=>$patient,'pat_id'=>$pat_id));  
}
public function myownenc($data){
   $inputKey= $this->inputKey;
   $blockSize =$this->blockSize;
  if(trim($data)) {
    $inputText = $data;
    $aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
    $enc = $aes->encrypt();
    $aes->setData($enc);
    return $enc;
    //echo $enc ;
  } 
}
public function myencrypt($data)
{
   $inputKey= $this->inputKey;
   $blockSize =$this->blockSize;
  if(trim($data)) {
    $inputText = $data;
    $aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
    $enc = $aes->encrypt();
    $aes->setData($enc);
  //  return $enc;
    echo $enc ;
  } 
}
public function docccpayAction()
  {    
     $action = $this->params()->fromRoute('id',null); 
     $request= $this->getRequest();
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $inputKey = "2345432AD12H";
     $blockSize = 256;
     $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($docid);
      $doctor_custid= $doctorDetails[0]['cust_id'] ;
     if($action==8){                    /*   for cancel subscription   */
	$inputText =$doctor_custid;
    //$aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
    //$dec=$aes->decrypt();
     
    \Stripe\Stripe::setApiKey($this->stripesecret_key);
    $cu = \Stripe\Customer::retrieve($doctor_custid);
    
    $inputText = $doctorDetails[0]['subs_id'] ;
    $aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
    $dec_subid=$aes->decrypt();
    
    $cu->subscriptions->retrieve($dec_subid)->cancel();
    $updateDoctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->cancelDoctorSubscription($docid);

     }else if($action==3){

       
      /*
    $inputText =$custid;
    \Stripe\Stripe::setApiKey($stripe['secret_key']);
    $aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
    $dec=$aes->decrypt(); 
      echo  $stripeToken ;
      $cu = \Stripe\Customer::retrieve($dec);
      $cu->source = $stripeToken; // obtained with Stripe.js
      $cu->save();
       */
     }
     if($request->isPost()){
   
		$number = $request->getPost('number');
		$cvc       = $request->getPost('cvc');
		$exp_month  = $request->getPost('exp_month');
		$exp_year = $request->getPost('exp_year');
		$stripeToken = $request->getPost('stripeToken');
		$data  = substr($number,-4);
		/**create cc_number*/
		$inputText = $data;
		//$vendor = new \MyModule\AES();
		$aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
		$enc = $aes->encrypt();
		$aes->setData($enc);
		$number_aes =$enc;
		
       if($action==3){               // for change subscription 
       $doctor_custid= $doctorDetails[0]['cust_id'] ;
     
      $inputText = $doctor_custid;
      
    // $vendor = new \MyModule\AES();
       $aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
       $dec = $aes->decrypt(); 
      // $dec = 'cus_85lvrrlNI8RjNl';
       $cu = \Stripe\Customer::retrieve($dec);
      $cu->source = $stripeToken; // obtained with Stripe.js
      $cu->save();
      }else{
       $email =$doctorDetails[0]['doc_email'];
       $cust = \Stripe\Customer::create(array(
         "description" => "Customer - ".$email,
         "source" => $stripeToken,
         ));
      
       $cu = \Stripe\Customer::retrieve($cust->id);
     
       $subs = $cu->subscriptions->create(array("plan" => "docplan"));
    /*  code of myencript function    start    */
     if(trim($subs->id)) {
      $inputText = $subs->id;
      $aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
      $enc = $aes->encrypt();
      $aes->setData($enc);
      $subs_id=$enc ;
  } 

      
        
        /*  code of myencript function    end     */
     
       $DoctorSubscriptionInNotChange= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDoctorSubscriptionInNotChange($subs_id,$cust->id,$docid);   
       //echo '#1';
       if($subs->id){
		
            $DoctorSubscriptionInNotChange= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertDoctorSubscriptionInNotChange($docid,($subs->plan['amount']/100));
         //   echo '#2';exit;
         }
      }
      
  $updateDoctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDoctorDetails($docid,$number_aes,$cvc,$exp_month,$exp_year); 
  
  $this->redirect()->toUrl('/dashboardfront/docccpay');
  }
     $cc_number=$doctorDetails[0]['cc_number'] ;
     if(!empty($cc_number)){   // check if $inputText value exist .
     $inputText = $cc_number;
     $aes = new \MyModule\AES($inputText,$inputKey,$blockSize);
     $cc_numberAfterDecription=$aes->decrypt();
     }
     return new ViewModel(array('docUserID'=>$docid,'doctorDetails'=>$doctorDetails,'cc_numberDecription'=>$cc_numberAfterDecription,'action'=>$action,'publishable_key'=>$this->publishable_key));
  }
 
 public function doctorccpayAction()
  {   
     $docurlid = $this->params()->fromRoute('id',null); 
     $request= $this->getRequest();
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $inputKey = "2345432AD12H";
     $blockSize = 256;
     $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($docid);
     $custid=$doctorDetails[0]['cust_id']; 
     if($request->isPost()){
      $number = $request->getPost('number');
      $cvc       = $request->getPost('cvc');
      $exp_month  = $request->getPost('exp_month');
      $exp_year = $request->getPost('exp_year');
      $data  = substr($number,-4);
        if($docurlid) { 
        $email =$doctorDetails[0]['doc_email'];
        $cust = \Stripe\Customer::create(array(
         "description" => "Customer - ".$email,
         "source" => $stripeToken
         ));
        $cu = \Stripe\Customer::retrieve($cust->id);
        if($doctorDetails[0]['doc_speciality']>3)
          $subs = $cu->subscriptions->create(array("plan" => "splplan"));
        else
          $subs = $cu->subscriptions->create(array("plan" => "docplan"));
           /*  code of myencript function    start    */
         if(trim($subs->id)) {
         $inputText = $subs->id;
         $aes = new \MyModule\AES($inputText, $this->inputKey, $this->blockSize);
         $enc = $aes->encrypt();
         $aes->setData($enc);
         $subs_id=$enc ;
         } 
         /*  code of myencript function    end     */
         $encriptCust_id=myencript($cust->id) ;

         $DoctorSubscriptionInNotChange= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDoctorSubscriptionInNotChange($subs_id,$encriptCust_id,$docurlid);   
       if($subs->id){
            $DoctorSubscriptionInNotChange= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertDoctorSubscriptionInNotChange($docurlid,($subs->plan['amount']/100));
         }
          $inputText = $data;
    //  $vendor = new \MyModule\AES();
        $aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
        $enc = $aes->encrypt();
        $aes->setData($enc);
        $number_aes =$enc; 
        $updateDoctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDoctorDetails($docurlid,$number_aes,$cvc,$exp_month,$exp_year);
        $this->redirect()->toUrl('/register/1000') ;     
  } 
}
     return new ViewModel(array('docUserID'=>$docurlid,'doctorDetails'=>$doctorDetails,'action'=>$action,'publishable_key'=>$this->publishable_key));
  }
public function patientreferAction(){
    $patid= $this->params()->fromRoute('id',null);  
    $doclinkid= $this->params()->fromRoute('idd',null);   
    $session = new Container('User');
    $docid  =$session->offsetGet('userId');
    $doctorSpacilityDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailFromDocSpacility();
    $specialServicesDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->SpecialServicesList($doclinkid);
    if($doclinkid){
    $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($doclinkid);
    }
    $request= $this->getRequest();
      if ($request->isPost())
          { 
            $pat_id=$request->getPost('pat_id');
            $comments=$request->getPost('comments');
            $doclinkid=$request->getPost('doc_id');
         if($doclinkid){
          $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertReferPatient($doclinkid,
            $pat_id,$docid,$comments);
         }
         $this->redirect()->toRoute('dashboardfront',array('action'=>'doctorreferrals','id'=>$doclinkid)); 
        
         }
     return new ViewModel(array('doc_r'=>$doctorDetails,'pat_id'=>$patid,'doclinkid'=>$doclinkid,'spl_r'=>$doctorSpacilityDetails,'doclinkid'=>$doclinkid,'msg'=>$msg,'specialServicesDetails'=>$specialServicesDetails));
}
public function doctorreferralsAction()
  {  
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $referedtodoctorid =$this->params()->fromRoute('id',null); 
     $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetails();
     $DoctorReferralsListing= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->DoctorReferralsListing($docid);
     return new ViewModel(array('DoctorReferralsListing'=>$DoctorReferralsListing,'doctorDetails'=>$doctorDetails));
}

public function thanksAction()
	{    
		 return new ViewModel();
	}
   public function suscribeAction()
	{ 
	 $request= $this->getRequest();
	 if($request->isPost()){
	    $ragisteremail=$request->getPost('register-email');
	    $email ='Support@DentalPlanSoftware.com' ;
	   // $email ='skant1990it@gmail.com' ;
        $maillink='Name: '.$ragisteremail;    
                /* code for mail start   */
				$footer = "The Admin Team";
				$html = new Mime\Part($maillink);
				$html->type = Mime\Mime::TYPE_HTML;
				$text = new Mime\Part($footer);
				$text->type = Mime\Mime::TYPE_TEXT;
				$maillink = new Mime\Message();
				$maillink->setParts(array($html, $text));
				$message = new \Zend\Mail\Message();
				$message->setBody($maillink);
				$message->setFrom($config['email_sender']['email']);
				$message->setSubject("New Subscription entry");
				$message->addTo($email) ;
				$SmtpOptions =  new \Zend\Mail\Transport\SmtpOptions();
				$SmtpOptions->setHost($config['smtp_settings']['host']) 
					  ->setPort($config['smtp_settings']['port'])
					  ->setConnectionClass($config['smtp_settings']['connection_class'])
					  ->setName('smtp.gmail.com')
					  ->setConnectionConfig(array(
					   'username'=>$config['smtp_settings']['username'],
					   'password'=>$config['smtp_settings']['password'],
					   'ssl'     =>$config['smtp_settings']['ssl']

				));
				$transport  =  new \Zend\Mail\Transport\Smtp($SmtpOptions) ;
				$transport->send($message);
					/* code for mail start   */
      return $this->redirect()->toUrl('/dashboardfront/thanks');
      }  
     return new ViewModel();
	}

	 public function contactAction()
	{    	
	 $request= $this->getRequest();
	 if($request->isPost()){
	    $contactfname=$request->getPost('contact-fname');
      $contactlname=$request->getPost('contact-lname');
	    $contactsub=$request->getPost('contact-message');
	    $contactphone=$request->getPost('contact-phone');
      $contactname= $contactfname." ".$contactlname;
	    $email ='Support@DentalPlanSoftware.com' ;
      $maillink='Name: '.$contactname.'<br>Subject: '.$contactsub.'<br>Phone: '.$contactphone; 
                         /* code for mail start   */
                        $footer = "The Admin Team";
                        $html = new Mime\Part($maillink);
                        $html->type = Mime\Mime::TYPE_HTML;
                        $text = new Mime\Part($footer);
                        $text->type = Mime\Mime::TYPE_TEXT;
                        $maillink = new Mime\Message();
                        $maillink->setParts(array($html, $text));
                        $message = new \Zend\Mail\Message();
                        $message->setBody($maillink);
                        $message->setFrom('support@DentalPlanSoftware.com');
                        $message->setSubject("New contact us Entry");
                        $message->addTo($email) ;
                        $SmtpOptions =  new \Zend\Mail\Transport\SmtpOptions();
                        $SmtpOptions->setHost('smtp.gmail.com') 
                              ->setConnectionClass('login')
                              ->setName('smtp.gmail.com')
                              ->setConnectionConfig(array(
                               'username'=>'reporting@healthrise.com',
                               'password'=>'healthrise',
                               'ssl'     =>'tls',
                        ));
                        $transport  =  new \Zend\Mail\Transport\Smtp($SmtpOptions) ;
                        $transport->send($message);
                         /* code for mail start   */
      $this->flashMessenger()->addMessage(array(
                    'msg' => 'Thanks to submit the form. We will contact you soon'));
      return $this->redirect()->toUrl('/dashboardfront/thanks');
      }
    return new ViewModel();
	}
  public function welcomeAction()
  {
   $session = new Container('User');
   $doc_id  =$session->offsetGet('userId'); 
   $doctordashboardfrontdata= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalDoctorPatientCount($doc_id);
   $doc_fee =300;
   $total_pat =$doctordashboardfrontdata[0]['totalPatients'] ;
   $total_income =$doctordashboardfrontdata[0]['total_income'] ;
   $total_ref =$doctordashboardfrontdata[0]['totalref'] ;
   $refer_accepted =$doctordashboardfrontdata[0]['refer_accepted'] ;
   $refer_rejected =$doctordashboardfrontdata[0]['refer_rejected'] ;
   if($total_income >($doc_fee*10)) {
    $net_income = $total_income-(($total_income/100)*10)-$doc_fee;
   } else {
    $net_income = $total_income-$doc_fee;
  }
  return new viewModel(array('total_pat'=>$total_pat,'total_income'=>$total_income,'net_income'=>$net_income,'total_ref'=>$total_ref,'refer_accepted'=>$refer_accepted,'refer_rejected'=>$refer_rejected)) ;  
  }
  public function doctorsupportAction(){
     $request= $this->getRequest();
     if($request->isPost()){
       $first_name =$request->getPost('first_name');
       $last_name =$request->getPost('last_name');
       $email_id =$request->getPost('email_id'); 
       $address =$request->getPost('address');
       $phone =$request->getPost('phone');
       $message =$request->getPost('message');
       $pid =$request->getPost('pid');
       $session = new Container('User');
       $doc_id  =$session->offsetGet('userId');
       $tabledetail = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->savesupports($doc_id,$first_name,$last_name,$email_id,$address,$phone,$message); 
       $success= 1;
      }
   return new viewModel(array('success'=>$success)) ;
    
  } 
 public function splservicesAction()
  {  $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $splserviceDetails = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->splserviceFromDocId($docid);   
     return new ViewModel(array('splserviceDetails'=>$splserviceDetails));
  }
    public function reportsAction(){
      $reportid = $this->params()->fromRoute('id',null); 
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
      if($reportid=='1') {
       $reports = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->viewReports($reportid,$docid) ;
       } else if($reportid=='2') {
      $reports = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->viewReports($reportid,$docid) ;
       } else if($reportid=='3') {
       $reports = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->viewReports($reportid,$docid) ;
       } else {
       $reports = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->viewReports($reportid,$docid) ;
       }
       return new ViewModel(array('reports' =>$reports,'reportid'=>$reportid));
    }
     public function viewplandetailAction()
     {    
     $planid = $this->params()->fromRoute('id',null); 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $listplanservice= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listplanservice();
     $plandetail= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlanDetails($planid,$docid);  
     return new ViewModel(array('plandetail'=>$plandetail,'listplanservice'=>$listplanservice));
  }

public function splservicesaddAction()
  {    
    $spl_serviceid= $this->params()->fromRoute('id',null); 
    if(isset($spl_serviceid)){
      $spl_servicedetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->servicesDetailsFromId($spl_serviceid);
    }
     $request = $this->getRequest() ;    // getting current request object
        if ($request->isPost())
        {
          $service_type= $request->getPost('service_type');
          $service_name1= $request->getPost('service_name1');
          $service_name2= $request->getPost('service_name2');
          $service_name3= $request->getPost('service_name3');
          $service_name4= $request->getPost('service_name4');
          $service_name5= $request->getPost('service_name5');
          $service_name6= $request->getPost('service_name6');
          $service_name7= $request->getPost('service_name7');
          $service_name8= $request->getPost('service_name8');
          $service_name9= $request->getPost('service_name9');
          $service_name10= $request->getPost('service_name10');
          $price= $request->getPost('price');
          $session = new Container('User');
          $doc_id  =$session->offsetGet('userId');
          $savedoctor=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->saveSplServices($service_type,$service_name1,$service_name2,$service_name3,$service_name4,$service_name5,$service_name6,$service_name7,$service_name8,$service_name9,$service_name10,$price,$doc_id,$spl_serviceid);
        }
     return new ViewModel(array('service_id'=>$spl_serviceid,'spl_servicedetails'=>$spl_servicedetails,'exists'=>$exists));
  }
 public function splservicesviewdetailsAction()
  { 
    $serviceid=$this->params()->fromRoute('id',null);  
    $spl_servicedetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->servicesDetailsFromId($serviceid); 
    return new ViewModel(array('spl_servicedetails'=>$spl_servicedetails));
  }
  public function  delservicesAction(){
    $id = (int) $this->params()->fromRoute('id',null);
         $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteservices($id);
         $this->redirect()->toRoute('dashboardfront',array('action'=>'splservices'));

  }
   public function  delpatientAction(){
    $id = (int) $this->params()->fromRoute('id',null);
         $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->delpatient($id);
         $this->redirect()->toRoute('dashboardfront',array('action'=>'patient'));

   }
  public function planViewAction(){
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
     $planid= $this->params()->fromRoute('id',null);
     $plan_list = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectPlans($planid) ;
     $plan_service = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectPlanServices($planid) ;
return new ViewModel(array('plans'=>$plan_list,'plan_id'=>$planid,'plan_services'=>$plan_service,'doc_id'=>$docid));
  }
 
  public function planeditAction(){
         $planid= $this->params()->fromRoute('id',null);
         $doc_id= $this->params()->fromRoute('idd',null);
     if(!empty($planid)){
     $request = $this->getRequest() ;    // For Edit Plan
      // print_r($request);exit;
        if ($request->isPost())
          {  
       $doc_id = $request->getPost('doc_id');
       $plan_name = $request->getPost('plan_name');
       $heading_line = $request->getPost('heading_line');
       $plan_monthly_price = $request->getPost('plan_monthly_price');
       $plan_yearly_price = $request->getPost('plan_yearly_price');
       $addon_monthly_price = $request->getPost('addon_monthly_price');
       $addon_yearly_price = $request->getPost('addon_yearly_price'); 
       $planid = $request->getPost('pid');
      $saveplan=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->editPlans($planid,$doc_id,$plan_name,$heading_line,$plan_monthly_price,$plan_yearly_price,$addon_monthly_price,$addon_yearly_price);

   $this->redirect()->toRoute('dashboardfront',array('action'=>'plan')); 
      }   
    $plan_list = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectPlans($planid) ;
       $plan_service = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectPlanServices($planid) ;
return new ViewModel(array('plans'=>$plan_list,'plan_id'=>$planid,'plan_services'=>$plan_service,'doc_id'=>$doc_id));  
       
      }else{

       $session = new Container('User');
       $doc_id  =$session->offsetGet('userId'); 
       $request = $this->getRequest() ;    // For Insert Plan
        if ($request->isPost())
          {  
     //  $doc_id = $request->getPost('doc_id');
       $plan_name = $request->getPost('plan_name');
       $heading_line = $request->getPost('heading_line');
       $plan_monthly_price = $request->getPost('plan_monthly_price');
       $plan_yearly_price = $request->getPost('plan_yearly_price');
       $addon_monthly_price = $request->getPost('addon_monthly_price');
       $addon_yearly_price = $request->getPost('addon_yearly_price'); 
       $lastplanid=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlans($doc_id,$plan_name,$heading_line,$plan_monthly_price,$plan_yearly_price,$addon_monthly_price,$addon_yearly_price);


   $this->redirect()->toRoute('dashboardfront',array('action'=>'plansservice','id'=>$lastplanid));      
    }        
return new ViewModel(array('plan_id'=>$planid,'plan_services'=>$plan_service,'doc_id'=>$doc_id));       
      }
   }
  
  public function plansserviceAction(){
      $planid= $this->params()->fromRoute('id',null);
      $service_id= $this->params()->fromRoute('idd',null); 
      $session = new Container('User');
      $doc_id  =$session->offsetGet('userId');      
     if($service_id !=0){

     $request = $this->getRequest() ;    // For Edit Plan Services    
        if ($request->isPost())          { 
       $service_type  = $request->getPost('service_type');
       $service_name  = $request->getPost('service_name');
       $service_name1 = $service_name[0];
       $service_name2 = $service_name[1];
       $service_name3 = $service_name[2];
       $service_name4 = $service_name[3];
       $service_name5 = $service_name[4];
       $service_name6 = $service_name[5];
       $service_name7 = $service_name[6];
       $service_name8 = $service_name[7];
       $service_name9 = $service_name[8];
       $service_name10 = $service_name[9];
       $service_notes = $request->getPost('service_notes'); 
      $addnext = $request->getPost('addnext');
//  print_r($service_name);exit;
$saveplan= $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->editPlanServices($service_id,$service_type,$service_name1,$service_name2,$service_name3,$service_name4,$service_name5,$service_name6,$service_name7,$service_name8,$service_name9,$service_name10,$service_notes);
  if($addnext){
  $this->redirect()->toRoute('dashboardfront',array('action'=>'plansservicenext','id'=>$planid));
  }else{
  $this->redirect()->toRoute('dashboardfront',array('action'=>'planedit','id'=>$planid,'idd'=>$doc_id)); 
   } 
 }  
}else{
    
       $request = $this->getRequest() ;    // For Edit Plan Services   
        if ($request->isPost()) { 
     
       $service_type  = $request->getPost('service_type');
       $service_name  = $request->getPost('service_name');
       $service_name1 = $service_name[0];
       $service_name2 = $service_name[1];
       $service_name3 = $service_name[2];
       $service_name4 = $service_name[3];
       $service_name5 = $service_name[4];
       $service_name6 = $service_name[5];
       $service_name7 = $service_name[6];
       $service_name8 = $service_name[7];
       $service_name9 = $service_name[8];
       $service_name10 = $service_name[9];
       $service_notes = $request->getPost('service_notes'); 
       $addnext = $request->getPost('addnext');
$saveplan= $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlanServices($service_type,$service_name1,$service_name2,$service_name3,$service_name4,$service_name5,$service_name6,$service_name7,$service_name8,$service_name9,$service_name10,$service_notes,$planid);
  if($addnext){
  $this->redirect()->toRoute('dashboardfront',array('action'=>'plansservicenext','id'=>$planid));
  }else{
   $this->redirect()->toRoute('dashboardfront',array('action'=>'planedit','id'=>$planid,'idd'=>$doc_id));  
      }   
     }
   }
   if($service_id){
   $services = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectSingleServices($service_id) ;
   }
   return new ViewModel(array('services'=>$services,'plan_id'=>$planid,'service_id'=>$service_id,'doc_id'=>$doc_id));
   }  

 public function plansservicenextAction()
 {
   $planid= $this->params()->fromRoute('id',null);
   $service_id= $this->params()->fromRoute('idd',null); 
    $session = new Container('User');
    $doc_id  =$session->offsetGet('userId');  
   if(empty($service_id)){
      $request = $this->getRequest() ;    // For Edit Plan Services    
        if ($request->isPost())         
         { 
       $service_type  = $request->getPost('service_type');
       $service_name  = $request->getPost('service_name');
       $service_name1 = $service_name[0];
       $service_name2 = $service_name[1];
       $service_name3 = $service_name[2];
       $service_name4 = $service_name[3];
       $service_name5 = $service_name[4];
       $service_name6 = $service_name[5];
       $service_name7 = $service_name[6];
       $service_name8 = $service_name[7];
       $service_name9 = $service_name[8];
       $service_name10 = $service_name[9];
       $service_notes = $request->getPost('service_notes'); 
       $addnext = $request->getPost('addnext');
       $saveplan= $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlanServices($service_type,$service_name1,$service_name2,$service_name3,$service_name4,$service_name5,$service_name6,$service_name7,$service_name8,$service_name9,$service_name10,$service_notes,$planid);
      $this->redirect()->toRoute('dashboardfront',array('action'=>'planedit','id'=>$planid,'idd'=>$doc_id)); 
    }
  }

}
 public function doctoreditAction()
  { 
    $msg= $this->params()->fromRoute('id',null);   
    $session = new Container('User');
    $docid  =$session->offsetGet('userId');
    $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($docid);
     $request= $this->getRequest();
      if ($request->isPost())
          { 
          $doc_username= $request->getPost('doc_username');
          $doc_firstname= $request->getPost('doc_firstname');
          $doc_lastname= $request->getPost('doc_lastname');
          $doc_email= $request->getPost('doc_email');
          $doc_phone= $request->getPost('doc_phone');
          $doc_zip= $request->getPost('doc_zip');
          $doc_address= $request->getPost('doc_address');
          $doc_phone2= $request->getPost('doc_phone2');
          $doc_sex= $request->getPost('doc_sex');
          $doc_speciality= $request->getPost('doc_speciality');
          $doc_license_no= $request->getPost('doc_license_no');
          $doc_contact_person= $request->getPost('doc_contact_person');
          $doc_status= $request->getPost('doc_status');
          $doc_id= $request->getPost('doc_id');
          $savedoctor=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->savedoctor($doc_id,$doc_firstname,$doc_lastname,$doc_email,$doc_phone,$doc_zip,$doc_address,$doc_phone2,$doc_sex,$doc_speciality,$doc_license_no,$doc_contact_person,$doc_status,$doc_username);
          $msg =1;
         $this->redirect()->toRoute('dashboardfront',array('action'=>'doctoredit','id'=>$msg)); 
         }
     return new ViewModel(array('doc_id'=>$docid,'doc_r'=>$doctorDetails,'msg'=>$msg));
  }
   public function changepasswordAction()
  {    
      $request = $this->getRequest() ;    // getting current request object
        if ($request->isPost())
          { 
             $admin_pass = $request->getPost('admin_pass');
             $admin_cpass = $request->getPost('admin_cpass');
             if(strlen($admin_pass)<6) {
            $errmsg = 'Password length should be 6 characters';
             $err = 1;
             } else if($admin_pass!=$admin_cpass) {
            $errmsg = 'Password and confirm password should be same';
            $err = 1;
            }else{
            $session = new Container('User') ;
            $userid = $session->offsetGet('userId');
            $result= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->changeDoctorPassword($admin_pass,$userid);
            $msg = 1;  
         return new ViewModel(array('err'=>$err,'errmsg'=>$errmsg,'msg'=>$msg));
            }
         }
      return new ViewModel(array('err'=>$err,'errmsg'=>$errmsg,'msg'=>$msg));
  }

    public function doctorimagechangeAction()
    { 
		  
		 //print_r($_FILES);exit;  
         $session = new Container('User');
         $doc_id  =$session->offsetGet('userId');
         //$msg_del_done = (int) $this->params()->fromRoute('idd',null);
         $uploadOk = 1;
         $success_msg=0 ;
         $doc_avatardetail = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectDoctorAvatar($doc_id) ;
      
         $request = $this->getRequest() ;    // getting current request object     
         if ($_FILES["file"]["name"])
		 { 
				$msg = array();
				$doc_avatar = rand().'avatar_'.$doc_id.substr($_FILES["file"]["name"],strpos($_FILES["file"]["name"],'.'));
				$target_file = $_SERVER['DOCUMENT_ROOT']."/img/placeholders/avatars/".$doc_avatar;
				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
				// Check if image file is a actual image or fake image
				$check = getimagesize($_FILES["file"]["tmp_name"]);
				if($check== false) {
					//  $err[] = "File is an image - " . $check["mime"] . ".";
					$uploadOk = 0;
					$msg['error'] = "File is an image - " . $check["mime"] . ".";
					
				}else if($_FILES["file"]["size"] > 2000000) {
					// $err[] = "Sorry, your file is too large.";
					$uploadOk = 0;
					$msg['error'] = "Sorry, your file is too large.";
					//echo "2";
				}else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
					//  $err[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					$uploadOk = 0;
					$msg['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					//echo "3";
				}
					// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					$success_msg=0 ;
				  }else{  
					   $success_msg = 1;
					   
					   $saveavatar= $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->editDoctorAvatar($doc_id,$doc_avatar);
					   //$doc_avatardetail = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectDoctorAvatar($doc_id) ;
					   move_uploaded_file($_FILES["file"]["tmp_name"], $target_file); 
					   //$success_msg=3 ;
					   //$success_msgtext= "The file ". basename($_FILES["file"]["name"]). " has been uploaded.";
					   $msg['file'] = "/img/placeholders/avatars/".$doc_avatar;					   
					   
					   
					   //return new ViewModel(array('success_msg'=>$success_msg,'id'=>$doc_id,'doc_avatar'=>$doc_avatardetail,'msg_del_done'=>$msg_del_done,'uploadOk'=>$uploadOk,'success_msgtext'=>$success_msgtext));
					}   
					$msg['success']=$success_msg;    
       }
       echo json_encode($msg);
       exit;
       //return new ViewModel(array('success_msg'=>$success_msg,'id'=>$doc_id,'doc_avatar'=>$doc_avatardetail,'msg_del_done'=>$msg_del_done,'uploadOk'=>$uploadOk,'success_msgtext'=>$success_msgtext));
    }
	public function providerimagechangeAction()
    { 
		  
		 //print_r($_FILES);exit;  
		 $pro_id= $this->params()->fromRoute('id',null);   
         $session = new Container('User');
         $doc_id  =$session->offsetGet('userId');
         //$msg_del_done = (int) $this->params()->fromRoute('idd',null);
         $uploadOk = 1;
         $success_msg=0 ;
         $doc_avatardetail = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectDoctorAvatar($doc_id) ;
      
         $request = $this->getRequest() ;    // getting current request object     
         if ($_FILES["file"]["name"])
		 { 
				$msg = array();
				$doc_avatar = rand().'avatar_'.$pro_id.substr($_FILES["file"]["name"],strpos($_FILES["file"]["name"],'.'));
				$target_file = $_SERVER['DOCUMENT_ROOT']."/img/placeholders/avatars/".$doc_avatar;
				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
				// Check if image file is a actual image or fake image
				$check = getimagesize($_FILES["file"]["tmp_name"]);
				if($check== false) {
					//  $err[] = "File is an image - " . $check["mime"] . ".";
					$uploadOk = 0;
					$msg['error'] = "File is an image - " . $check["mime"] . ".";
					
				}else if($_FILES["file"]["size"] > 2000000) {
					// $err[] = "Sorry, your file is too large.";
					$uploadOk = 0;
					$msg['error'] = "Sorry, your file is too large.";
					//echo "2";
				}else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
					//  $err[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					$uploadOk = 0;
					$msg['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					//echo "3";
				}
					// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					$success_msg=0 ;
				  }else{  
					   $success_msg = 1;
					   
					   $saveavatar= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->editProviderAvatar($pro_id,$doc_avatar);
					   //$doc_avatardetail = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectDoctorAvatar($doc_id) ;
					   move_uploaded_file($_FILES["file"]["tmp_name"], $target_file); 
					   //$success_msg=3 ;
					   //$success_msgtext= "The file ". basename($_FILES["file"]["name"]). " has been uploaded.";
					   $msg['file'] = "/img/placeholders/avatars/".$doc_avatar;					   
					   
					   
					   //return new ViewModel(array('success_msg'=>$success_msg,'id'=>$doc_id,'doc_avatar'=>$doc_avatardetail,'msg_del_done'=>$msg_del_done,'uploadOk'=>$uploadOk,'success_msgtext'=>$success_msgtext));
					}   
					$msg['success']=$success_msg;    
       }
       echo json_encode($msg);
       exit;
       //return new ViewModel(array('success_msg'=>$success_msg,'id'=>$doc_id,'doc_avatar'=>$doc_avatardetail,'msg_del_done'=>$msg_del_done,'uploadOk'=>$uploadOk,'success_msgtext'=>$success_msgtext));
    }
	public function teststripeAction(){
		
		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here https://dashboard.stripe.com/account/apikeys
		\Stripe\Stripe::setApiKey("sk_test_pzZdL0U9VmqJAm8XhHeCVJuT");

		$acct = \Stripe\Account::create(array(
		  "managed" => true,
		  "country" => "US",
		  "external_account" => array(
		    "object" => "bank_account",
		    "country" => "US",
		    "currency" => "usd",
		    "routing_number" => "110000000",
		    "account_number" => "000123456789",
		  ),
		  "tos_acceptance" => array(
		    "date" => 1459851033,
		    "ip" => '122.160.74.176'
		  )
		));

		$acct_id = $acct->id;
		if($acct_id){
			$pay = \Stripe\Charge::create(array(
			  "amount" => 50,
			  "currency" => "usd",
			  "source" => array(
			    object => "card",
			    number => 4000000000004210,
			    exp_month => 2,
			    exp_year => 2017
			  ),
			  "destination" => $acct_id
			));
			//var_dump(\Stripe\Account::retrieve($acct_id));
			//print_r($verification[fields_needed]);
			$ac = \Stripe\Account::retrieve($acct_id);	
						
			if(!empty($ac->verification->fields_needed)){	
				$account = \Stripe\Account::retrieve($acct_id);
				$account->legal_entity->dob->day = 10;
				$account->legal_entity->dob->month = 01;
				$account->legal_entity->dob->year = 1986;
				$account->legal_entity->first_name = "Jonathan";
				$account->legal_entity->last_name = "Goode";
				$account->legal_entity->type = "individual";
				$account->save();
				
				\Stripe\Charge::create(array(
				"amount" => 50,
				"currency" => "usd",
				"source" => array(
				object => "card",
				number => 4000000000004202,
				exp_month => 2,
				exp_year => 2017
				),
				"destination" => $acct_id
				));

				// Re-fetch the account to see what its status is.
				var_dump(\Stripe\Account::retrieve($acct_id));
			}
		}
		//echo $acct_id;
		//print_r($acct);
		echo 'Hedayat';exit;
	}

/*     manage  specialist doctor function start                                 */
public function specialistsplservicesAction()
  {  $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $splserviceDetails = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->splserviceFromDocId($docid);   
     return new ViewModel(array('splserviceDetails'=>$splserviceDetails));
  }
public function specialistsplservicesviewdetailsAction()
  { 
    $serviceid=$this->params()->fromRoute('id',null);  
    $spl_servicedetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->servicesDetailsFromId($serviceid); 
    return new ViewModel(array('spl_servicedetails'=>$spl_servicedetails));
  }
public function  specilistdelservicesAction(){
    $id = (int) $this->params()->fromRoute('id',null);
         $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteservices($id);
         $this->redirect()->toRoute('dashboardfront',array('action'=>'specialistsplservices'));

  }
public function specialistsplservicesaddAction()
  {    
    $spl_serviceid= $this->params()->fromRoute('id',null); 
    if(isset($spl_serviceid)){
      $spl_servicedetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->servicesDetailsFromId($spl_serviceid);
    }
     $request = $this->getRequest() ;    // getting current request object
        if ($request->isPost())
        {
          $service_type= $request->getPost('service_type');
          $service_name1= $request->getPost('service_name1');
          $service_name2= $request->getPost('service_name2');
          $service_name3= $request->getPost('service_name3');
          $service_name4= $request->getPost('service_name4');
          $service_name5= $request->getPost('service_name5');
          $service_name6= $request->getPost('service_name6');
          $service_name7= $request->getPost('service_name7');
          $service_name8= $request->getPost('service_name8');
          $service_name9= $request->getPost('service_name9');
          $service_name10= $request->getPost('service_name10');
          $price= $request->getPost('price');
          $session = new Container('User');
          $doc_id  =$session->offsetGet('userId');

          $savedoctor=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->saveSplServices($service_type,$service_name1,$service_name2,$service_name3,$service_name4,$service_name5,$service_name6,$service_name7,$service_name8,$service_name9,$service_name10,$price,$doc_id,$spl_serviceid);

         if($addnext==1){
          $this->redirect()->toRoute('dashboardfront',array('action'=>'specialistsplservicesadd'));
          }else{
          $this->redirect()->toUrl('/dashboardfront/specialistsplservices');
          }


        }
     return new ViewModel(array('service_id'=>$spl_serviceid,'spl_servicedetails'=>$spl_servicedetails,'exists'=>$exists));
  }
  public function specialistsdoctoraddAction()
    {    
    $session = new Container('User');
    $doctorid  =$session->offsetGet('userId');
    $msg=0;
    if(isset($doctorid)){
           $doctordetails=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->doctorDetailsFromId($doctorid);
      }
     $request = $this->getRequest() ;    // getting current request object
        if ($request->isPost())
          { 
          $doc_contact_person= $request->getPost('doc_contact_person');
          $doc_username= $request->getPost('doc_username');
          $doc_pass= $request->getPost('doc_pass');
           $exists=0;
          $doc_id= $request->getPost('doc_id');
          $userdetails = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->checkUserid($doc_username) ;
          if(isset($userdetails[0]['doc_id'])&& !$doc_id){
            $exists = 1;
          }else{
          $msg=1;
          $doc_firstname= $request->getPost('doc_firstname');
          $doc_lastname= $request->getPost('doc_lastname');
          $doc_email= $request->getPost('doc_email');
          $doc_phone= $request->getPost('doc_phone');
          $doc_zip= $request->getPost('doc_zip');
          $doc_address= $request->getPost('doc_address');
          $doc_phone2= $request->getPost('doc_phone2');
          $doc_sex= $request->getPost('doc_sex');
          $doc_speciality= $request->getPost('doc_speciality'); 
          $doc_speciality_detail= $request->getPost('doc_speciality_detail'); 
          $doc_license_no= $request->getPost('doc_license_no');
          $doc_contact_person= $request->getPost('doc_contact_person');
          $doc_status= $request->getPost('doc_status');
         
          $savedoctorid =  $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->saveDoctor($doc_id,$doc_firstname,$doc_lastname,$doc_email,$doc_phone,$doc_zip,$doc_address,$doc_phone2,$doc_sex,$doc_speciality,$doc_license_no,$doc_contact_person,$doc_status,$doc_username,$doc_pass,$doc_speciality_detail);
          if(!$doc_id){
            $doc_id=$savedoctorid ;
          }
          return new ViewModel(array('doctorid'=>$doctorid,'doctordetails'=>$doctordetails,'msg'=>$msg));
            }
          }
    return new ViewModel(array('doctorid'=>$doctorid,'doctordetails'=>$doctordetails,'msg'=>$msg));
  }
public function doctorspacilistccpayAction()
  {   
     $action = $this->params()->fromRoute('id',null); 
     $request= $this->getRequest();
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $inputKey = "2345432AD12H";
     $blockSize = 256;
     $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($docid);
     $custid=$doctorDetails[0]['cust_id']; 
     if($request->isPost()){
        $number = $request->getPost('number');
        $cvc       = $request->getPost('cvc');
        $exp_month  = $request->getPost('exp_month');
        $exp_year = $request->getPost('exp_year');
        $stripeToken = $request->getPost('stripeToken');
        $docurlid = $request->getPost('doc_id');
        $data  = substr($number,-4);
         if($action==3){               // for change subscription 
         $doctor_custid= $doctorDetails[0]['cust_id'] ;
        $inputText = $doctor_custid;
        // $vendor = new \MyModule\AES();
        // $aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
        // $dec = $aes->decrypt(); 
          $dec = $inputText;
       $cu = \Stripe\Customer::retrieve($dec);
       $cu->source = $stripeToken; // obtained with Stripe.js
       $cu->save();
      }else{
         $email =$doctorDetails[0]['doc_email'];
         $cust = \Stripe\Customer::create(array(
  "description" => "Customer - ".$email,
  "source" => $stripeToken
      ));
         $cu = \Stripe\Customer::retrieve($cust->id);
         $subs = $cu->subscriptions->create(array("plan" => "splplan"));
         /*  code of myencript function    start    */
         if(trim($subs->id)) {
         $inputText = $subs->id;
          $aes = new \MyModule\AES($inputText, $this->inputKey, $this->blockSize);
          $enc = $aes->encrypt();
          $aes->setData($enc);
         $subs_id=$enc ;
         } 
         /*  code of myencript function    end     */
         $encriptCust_id=$cust->id ;
         $DoctorSubscriptionInNotChange= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDoctorSubscriptionInNotChange($subs_id,$encriptCust_id,$docid);   
         if($subs->id){
            $DoctorSubscriptionInNotChange= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertDoctorSubscriptionInNotChange($docid,($subs->plan['amount']/100));
         }
       }     // else part of action close here  
        $inputText = $data;
        $aes = new \MyModule\AES($inputText, $inputKey, $blockSize);
        $enc = $aes->encrypt();
        $aes->setData($enc);
        $number_aes =$enc; 
        $updateDoctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDoctorDetails($docid,$number_aes,$cvc,$exp_month,$exp_year);
        $this->redirect()->toUrl('/logout') ;      
      }
     $cc_number=$doctorDetails[0]['cc_number'] ; 
     if(!empty($cc_number)){   // check if $inputText value exist .
     $inputText = $cc_number;
     $aes = new \MyModule\AES($inputText,$inputKey,$blockSize);
     $cc_numberAfterDecription=$aes->decrypt();
     }
return new ViewModel(array('docUserID'=>$docurlid,'doctorDetails'=>$doctorDetails,'action'=>$action,'publishable_key'=>$this->publishable_key,'cc_numberDecription'=>$cc_numberAfterDecription,'action'=>$action));
}
 public function specilistpatientAction(){
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
    //  $patientActiveDetails =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->patientActiveDetails($docid); 
    //  $patientInactiveDetails =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->patientInActiveDetails($docid);
      $listPatient =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->ReferralsPatienttoDoctorListingOnPatientClick($docid);
      $listonlyPatient =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatientsId();
     // $act_pats= count($patientActiveDetails);
    //  $inact_pats= count($patientInactiveDetails) ;
      $searchkey= $this->params()->fromRoute('id',null) ;
      // $doctorgrid=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->doctorgridListing($searchkey);
     $view = new ViewModel(array('act_pats'=>$act_pats,'inact_pats'=>$inact_pats,'listPatient'=>$listPatient,'listonlyPatient'=>$listonlyPatient));
     return $view;
    }
  public function specilistviewpatientAction()
  {    
     $patid = $this->params()->fromRoute('id',null); 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $patient= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetails($patid,$docid);  
     $patientonly= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatientsId();   
     return new ViewModel(array('patient'=>$patient,'patientonly'=>$patientonly,'patid'=>$patid));
  }
public function specilistpatientreferAction(){
    $patid= $this->params()->fromRoute('id',null);  
    $doclinkid= $this->params()->fromRoute('idd',null);   
    $session = new Container('User');
    $docid  =$session->offsetGet('userId');
    $doctorSpacilityDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailFromDocSpacility();
    $specialServicesDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->SpecialServicesList($doclinkid);
    if($doclinkid){
    $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($doclinkid);
    }
    $request= $this->getRequest();
      if ($request->isPost())
          { 
            $pat_id=$request->getPost('pat_id');
            $comments=$request->getPost('comments');
            $doclinkid=$request->getPost('doc_id');
         if($doclinkid){
          $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertReferPatient($doclinkid,
            $pat_id,$docid,$comments);
         }
         $this->redirect()->toRoute('dashboardfront',array('action'=>'doctorreferrals','id'=>$doclinkid)); 
        
         }
     return new ViewModel(array('doc_r'=>$doctorDetails,'pat_id'=>$patid,'doclinkid'=>$doclinkid,'spl_r'=>$doctorSpacilityDetails,'doclinkid'=>$doclinkid,'msg'=>$msg,'specialServicesDetails'=>$specialServicesDetails));
}
public function specilistreferlistAction(){
    $referid=$this->params()->fromRoute('id');
    $acceptstatus=$this->params()->fromRoute('idd'); 
    $session = new Container('User');
    $docid  =$session->offsetGet('userId');
    $totalReferCount= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferToDoctorCount($docid);
    $totalReferCountno =  $totalReferCount[0]['totalReferCount'];
   
    $totalReferAcceptedCount= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferAcceptedCount($docid);
    $totalAcceptedReferCountno=$totalReferAcceptedCount[0]['totalAcceptedReferCount'];
  
     $totalReferRejectedCount= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferRejectedCount($docid);
    $totalRegectedReferCount  = $totalReferRejectedCount['0']['totalRegectedReferCount'];
    
    $ReferralsPatientListing= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->ReferralsPatienttoDoctorListing($docid);
    $acceptedstatus =1;
    $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetails();
     if($acceptstatus==1000){
      $ReferralsPatientListing= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->ReferralsPatientStatusChange($acceptedstatus,$referid);
       $msg_accept = 'Patient Accepted';
       $this->redirect()->toUrl('/dashboardfront/specilistreferlist/1000');
     }
    if($acceptstatus==2000){
       $ReferralsPatientListing= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->ReferralsPatientStatusChange('2',$referid);
       $msg_reject = 'Patient Rejected';
      $this->redirect()->toUrl('/dashboardfront/specilistreferlist/2000');
     }
     return new ViewModel(array('totalReferCount'=>$totalReferCountno,'totalReferAcceptedCount'=>$totalAcceptedReferCountno,'totalRegectedReferCount'=>$totalRegectedReferCount,'ReferralsPatientListing'=>$ReferralsPatientListing,'doctorDetails'=>$doctorDetails,'msg'=>$referid));
}
 public function specilistwelcomeAction()
  {
   $session = new Container('User');
   $doc_id  =$session->offsetGet('userId'); 

	/**added for popup*/
	$providers = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->prividerByDocId($doc_id);
	
	
	$sendarray = array();
	foreach($providers as $k){
		array_push($sendarray,$k['id']);
	}
	/*check accept but call*/
	$iscall = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->isReferCall(implode(',',$sendarray));
	
	/*check refer*/
	$isreffer = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->isrefer(implode(',',$sendarray));
	if(isset($iscall[0]['total']) && $iscall[0]['total']!=0){
		$this->redirect()->toUrl("/dashboardfront/referralpopup2");
		
	}else if(isset($isreffer[0]['total']) && $isreffer[0]['total']!=0){
		$this->redirect()->toUrl("/dashboardfront/referralpopup1");
	}
	/**/

   $doctordashboardfrontdata= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalDoctorPatientCount($doc_id);
   $doc_fee =300;
   $total_pat =$doctordashboardfrontdata[0]['totalPatients'] ;
   $total_income =$doctordashboardfrontdata[0]['total_income'] ;
   $total_ref =$doctordashboardfrontdata[0]['totalref'] ;
   $refer_accepted =$doctordashboardfrontdata[0]['refer_accepted'] ;
   $refer_rejected =$doctordashboardfrontdata[0]['refer_rejected'] ;
   if($total_income >($doc_fee*10)) {
    $net_income = $total_income-(($total_income/100)*10)-$doc_fee;
   } else {
    $net_income = $total_income-$doc_fee;
  }
  return new viewModel(array('total_pat'=>$total_pat,'total_income'=>$total_income,'net_income'=>$net_income,'total_ref'=>$total_ref,'refer_accepted'=>$refer_accepted,'refer_rejected'=>$refer_rejected)) ;  
  }

 public function patientAction(){
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
      $DoctorPlansListing =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->DoctorPlansListing($docid);
      $listPatient =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatient($docid);
      $listonlyPatient =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatientsId();
      $act_pats= count($patientActiveDetails);
      $inact_pats= count($patientInactiveDetails) ;
      $searchkey= $this->params()->fromRoute('id',null) ;
      $showPatientNameId =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->showPatientNameId($docid);
      $showPatientNameIdjson=json_encode($showPatientNameId);
      $DoctorPlansListingjson=json_encode($DoctorPlansListing);
     $view = new ViewModel(array('act_pats'=>$act_pats,'planslisting'=>$DoctorPlansListingjson,'statelist'=>$showPatientNameIdjson,'listonlyPatient'=>$listonlyPatient));
     return $view;
    }
    public function  dashboardsettingAction()
    {
      return new ViewModel();
    }
    


public function  providerlocationeditAction()
{ 
$session = new Container('User');
$docid  =$session->offsetGet('userId');
$doctraveltypechk=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->docTravelTypeChk($docid);
//echo 'asdfsda';exit;
$doctraveltypeid=$doctraveltypechk[0]['traveling_type'];
$providerdetail=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectDoctorLocPro($docid);


 if(count($providerdetail)){
 foreach($providerdetail as $key=>$value)
  { 
    $myarray[$key]['id']=$value['id'];
    $myarray[$key]['doc_id'] =$value['doc_id'];
    $myarray[$key]['address']=$value['address'];
    $myarray[$key]['city'] =$value['city'];
    $myarray[$key]['state'] =$value['state'];
    $myarray[$key]['zip']=$value['zip'];
    $myarray[$key]['phone'] =$value['phone'];
    $myarray[$key]['practice_email'] =$value['practice_email'];
    $myarray[$key]['group'] =$doctraveltypeid ;
    if(count($value['provider'])==0)
    {
     $myarray[$key]['options'][]= array('id'=>1) ;
    }else{
      foreach($value['provider'] as $key1=>$val)
    {
        $myarray[$key]['options'][]= array('id'=>$key1+1);
        $myarray[$key]['first_name'][$key1+1]= $val['first_name'];
        $myarray[$key]['last_name'][$key1+1]= $val['last_name'];
        $myarray[$key]['gender'][$key1+1]= $val['gender'];
        $myarray[$key]['license'][$key1+1]= $val['licence_no'];
        $myarray[$key]['npi'][$key1+1]= $val['npi_no'];
        $myarray[$key]['dentist'][$key1+1]= $val['dentist_type'];
    }
   }
  }
 $provider  = json_encode($myarray);  
}else{
	$doctravel=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectProviderDetailByDocIdLocZero($docid);
     $key=1;
	$myarray['first_name'][$key]=$doctravel[0]['first_name']; // here it print first_name":{"1":"f1"}
	$myarray['last_name'][$key]= $doctravel[0]['last_name'];
	$myarray['gender'][$key]= $doctravel[0]['gender'];
    $myarray['license'][$key]=$doctravel[0]['licence_no'];
    $myarray['npi'][$key]=$doctravel[0]['npi_no'];
    $myarray['dentist'][$key]= $doctravel[0]['dentist_type'];
    $myarray['nolocation']=true;
    $myarray['options'][]=array('id'=>1);
    $provider=  json_encode(array($myarray));  // here it print like that[{"nolocation":"true","options":[{"id":1}],"first_name":{"1":"f1"},"last_name":{"1":"l1"},"group":{"1":1},"license":{"1":"12345"},"npi":{"1":"12345"},"dentist":{"1":2}}]  if i remove array() then print like that {"nolocation":"true","options":[{"id":1}],"first_name":{"1":"f1"},"last_name":{"1":"l1"},"group":{"1":1},"license":{"1":"12345"},"npi":{"1":"12345"},"dentist":{"1":2}} .
}
 return new ViewModel(array('providerdetail'=>$provider));
}
    public function  providerbillingeditAction()
    { 
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
      $doctor_detailsForId=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsByIdForSettingLocation($docid);
      $doctor_detailsForId = $doctor_detailsForId[0];
      $myarray =array();
      $myarray['accounttype'] =$doctor_detailsForId['account_type']; 
      $myarray['accountno'] = $doctor_detailsForId['bank_account_number'];
      $myarray['conf_account'] = $doctor_detailsForId['bank_account_number'];
      $myarray['routingno'] = $doctor_detailsForId['bank_routing_number'];
      $myarray['conf_routing'] = $doctor_detailsForId['bank_routing_number'];
      $myarray['acc_holder_name'] = $doctor_detailsForId['acc_holder_name'];

      $myarray['name_on_cc'] = $doctor_detailsForId['legal_name'];
      $myarray['cc_number'] = $doctor_detailsForId['cc_number'];
      $myarray['conf_cc_number'] = $doctor_detailsForId['cc_number'];
      $myarray['csv_no'] = $doctor_detailsForId['cvc_no'];
      $myarray['monthno'] = $doctor_detailsForId['creditcart_exp_month'];
      $myarray['exp_year'] = $doctor_detailsForId['creditcart_exp_year'];

      $doctor_detailsForIdjson=json_encode($myarray); 
      return new ViewModel(array('doctor_detailsForId'=> $doctor_detailsForIdjson));
    }
     public function submitBillingAction()
    { 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $post_date = file_get_contents("php://input");
     $data1 = json_decode($post_date);
     $data = (array) $data1;
     $docid=1; 
     $result=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDocBilling($docid,$data); 
     echo "1";die;
    }
     public function  practiceinfoeditAction()
    {  
 $session = new Container('User');
 $docid  =$session->offsetGet('userId');
 $doctor_detailsForId=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsByIdForSettingLocation($docid);                                
 $locationdetail=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectDocLocation($docid);
 $States=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->showStates();
 $myarray =array();
      $myarray['practice'] = $doctor_detailsForId[0]['practice_name'];
      $myarray['legal'] = $doctor_detailsForId[0]['leagal_business_name'];
      $myarray['group'] = $doctor_detailsForId[0]['traveling_type'];
      $chk=explode(",",$doctor_detailsForId[0]['kind_of_practice']);
      if($chk[0]==1){
      $chk1=true;
      $myarray['chk1'] = $chk1;
      }
      if($chk[1]==1){
      $chk2=true;
      $myarray['chk2'] = $chk2;
     }
      if($chk[2]==1){
      $chk3=true;
      $myarray['chk3'] = $chk3;
      }
        $val=0;
        $arrayObj = Array();
        $ArrayobjChoice=Array();

      if($locationdetail)
      {
        foreach ($locationdetail as $key => $value) 
        {   
        	$arrayObjlocid[$key+1]= $value['id'] ;
            $arrayObjaddr[$key+1]= $value['address'] ;
            $arrayObjcity[$key+1]= $value['city'] ;
            $arrayObjstate[$key+1]= $value['state'] ;
            $arrayObjzip[$key+1]= $value['zip'] ;
            $arrayObjphone[$key+1]= $value['phone'] ;
            $arrayObjemail[$key+1]= $value['practice_email'] ;
            $arrayObjtiming[$key+1]= $value['timing'] ;
            $val=$val+1;
        }
      }else{

      }
      $myarray['address'] =(object)$arrayObjaddr ;
      $myarray['city']    =(object)$arrayObjcity ;
      $myarray['state'] =(object)$arrayObjstate  ;
      $myarray['zip']    =(object)$arrayObjzip   ;
      $myarray['phone'] =(object)$arrayObjphone  ;
      $myarray['email']    =(object)$arrayObjemail ;
      $myarray['timing']    =(object)$arrayObjtiming ;
      $myarray['loc_id']    =(object)$arrayObjlocid;
      $location=json_encode($myarray);
      $statejson=json_encode($States);
        $choicejson=json_encode($ArrayobjChoice);
      return new ViewModel(array('statelist'=>$statejson,'locationdetail'=>$location,'choicesval'=> $val));
    }
   public function  providerinformationeditAction()
    {
     // $States=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->showStates();
          //$arr = (object) array('id'=>$val);
          //$loclistfordoctor[$key]['options']= array($arr);
   
     //$statejson=json_encode($States);
      return new ViewModel(array('statelist'=>$statejson));
    }
      public function planAction(){
      $delplanid= $this->params()->fromRoute('id',null);
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
      $listPlan =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->DoctorPlansListing($docid);
      //$listPatient =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatientsId($docid);
       //print_r($listPlan);
	if($listPlan[3]['plan_monthly_price']=='0.0000' && $listPlan[4]['plan_monthly_price']=='0.0000' && $listPlan[5]['plan_monthly_price']=='0.0000'){
		unset($listPlan[3]);
		unset($listPlan[4]);
		unset($listPlan[5]);
	}else{
		
	} 
      $DoctorPlansListingjson=json_encode($listPlan);
      $view = new ViewModel(array('listPlan'=>$DoctorPlansListingjson,'listPatients'=>$listPatient,'delplanid'=>$delplanid));
      return $view;
    }
    public function  delplanAction()
     {
         $id = (int) $this->params()->fromRoute('id',null);
         $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deletePlan($id);
         echo "1";exit;
     }
     public function getplandataAction()
     {
       $session = new Container('User');
      $docid  =$session->offsetGet('userId');
      $listPlan =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->DoctorPlansListing($docid);
      echo $DoctorPlansListingjson=json_encode($listPlan);exit;
     }
     public function  depositAction()
    { 
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
      $doctor_detailsForId=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsByIdForSettingLocation($docid);
      $doctor_detailsForId = $doctor_detailsForId[0];

      $myarray =array();
      $myarray['accountno'] = $doctor_detailsForId['deposit_account_no'];
      $myarray['conf_account'] = $doctor_detailsForId['deposit_account_no'];
      $myarray['routingno'] = $doctor_detailsForId['deposit_routing_no'];
      $myarray['conf_routing'] = $doctor_detailsForId['deposit_routing_no'];
      $myarray['acc_holder_name'] = $doctor_detailsForId['deposit_acc_holder_name'];
      $doctor_detailsForIdjson=json_encode($myarray); 
      return new ViewModel(array('doctor_detailsForId'=> $doctor_detailsForIdjson));
    }
      
       public function submitlocationAction()
      {
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
      $loclistfordoctor='';
      $post_date = file_get_contents("php://input");
      $data1 = json_decode($post_date);
      $data = (array) $data1;
     
      $legalbusinessname=$data['legal'];
      $practice=$data['practice'];
      $generalofficestatus=isset($data['chk1'])?$data['chk1']:0; 
      $specialityofficestatus=isset($data['chk2'])?$data['chk2']:0; 
      $generaldentalwithdiffspl=isset($data['chk3'])?$data['chk3']:0; 
      $addressobj   = isset($data['address'])?$data['address']:''; 
      $traveltype =$data['group'] ;
      $kind_of_practice = $generalofficestatus.",".$specialityofficestatus.",".$generaldentalwithdiffspl ;
      $userdetail =  $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDoctorDetailsLocation($docid,$legalbusinessname,$practice,$kind_of_practice,$traveltype);
      if($traveltype==1)
      {
      	$delprovider= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteproviderByDocId($docid);
      	$deletelocation= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteLocationByDocId($docid);
      	$loclistfordoctor= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectDocLocation($docid);
      }else{
       if($addressobj){
      $cityobj   = $data['city']; 
      $zipobj   = $data['zip']; 
      $phoneobj   = $data['phone'];  
      $emailobj   = isset($data['email'])?$data['email']:'';
  
      $resultinsertlocdetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDocLocation($docid,$data);

        return new ViewModel(array('loclistfordoctor'=>$loclistfordoctor)); 
      }
    }
       $val=1;
      if($loclistfordoctor){
        foreach ($loclistfordoctor as $key => $value)
         {
          $arr = (object)array('id'=>$val);
          $loclistfordoctor[$key]['options']= array($arr);
         }
       echo json_encode($loclistfordoctor); die;
       }else{
         echo '0';
         die;
       }
      return new ViewModel(array('loclistfordoctor'=>$loclistfordoctor)); 
    }
      public function addproviderinfoAction()
     {   
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
      $post_date = file_get_contents("php://input");
      $data1 = json_decode($post_date);
      $postdetail = (array) $data1;
      $request = $this->getRequest();
      if ($request->isPost()) {
      $userdetail = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteproviderByDocId($docid);
         foreach ($postdetail as $key => $value) {
          foreach ($value->first_name as $keys => $val) {
           $userdetail = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->addProviderInfo($docid,$val,$value->last_name->$keys,$value->dentist->$keys,$value->license->$keys, $value->npi->$keys,$value->id,$value->gender->$keys);
            }
          }
       }
      echo 'true';die; 
    }
    public function submitdepositAction()
    { 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $post_date = file_get_contents("php://input");
     $data1 = json_decode($post_date);
     $data = (array) $data1;
     $result=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDocDepositAccount($docid,$data); 
     echo "1";die;
    } 
     public function  providersequrityeditAction()
    { 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $request = $this->getRequest();
      if ($request->isPost())
      {
       $post_date = file_get_contents("php://input");
       $data1 = json_decode($post_date);
       $postdetail = (array)$data1;
       if($postdetail['code']){
        $docdetails  =  $this->getServiceLocator()->get("Dashboardfront\Model\DashboardfrontTable")->doctorDetailsFromId($docid);
          if($docdetails[0]['sequrity_code']== $postdetail['code']){
            $userdetail  =  $this->getServiceLocator()->get("UserTable")->updateDocSequrityCodeByUserId($docid,$postdetail['new_code']);
            echo "1"; die;
          }else{
           echo "3" ;  die;   // Mismatch sequrity code check  .  
          }
       }else{
       $userPassword = new UserPassword();
       $encyptPass = $userPassword->create($postdetail['acc_current_pass']);
       $authService = $this->getServiceLocator()->get('AuthService');
       $userdetail  =  $this->getServiceLocator()->get("UserTable")->authenticateDoctor($postdetail['acc_holder_name'] ,$encyptPass);
       $resultid = $userdetail['0']['doc_id'] ; 
       if (isset($resultid)) {
       $encyptNewPass = $userPassword->create($postdetail['new_pass']);
        $userdetail  =  $this->getServiceLocator()->get("UserTable")->updateDocPasswordByUserId($resultid,$encyptNewPass);
        echo "1" ;  die;
       }else{
         echo "2" ; die;    // for not valid user id
       }
      }    // sequrity code checking bracket close here.
    }
      return new ViewModel();
    }
 
  public function referAction(){
     
      $session = new Container('User');
      $docid  =$session->offsetGet('userId');
     
      $TotalReferToDoctorCount =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferToDoctorCount($docid);
      $TotalReferAcceptedCount =$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferAcceptedCount($docid);
     
      $ReferralsPatienttoDoctorListing = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->ReferralsPatienttoDoctorListing($docid);
    //  print_r($ReferralsPatienttoDoctorListing);die;
      $total_refer_count = $TotalReferToDoctorCount[0][totalReferCount];
      $total_refer_accepted_count = $TotalReferAcceptedCount[0][totalAcceptedReferCount];
     
      //echo "<pre>";print_r($ReferralsPatienttoDoctorListingjson);die;
     
     $view = new ViewModel(array('tot_refer_count'=>$total_refer_count,'tot_refer_accepted_count'=>$total_refer_accepted_count,
                                 'referlisting'=>$ReferralsPatienttoDoctorListing));
     return $view;
    }  
  
   public function  specialistsupportAction()
    { 
    $emailreceiver ='Support@DentalPlanSoftware.com' ;
      $request =$this->getRequest();
      if($request->isPost())
      {
        $post_date = file_get_contents("php://input");
      $data1 = json_decode($post_date);
      $data = (array) $data1;
      $fname   = $data['name']; 
      $lname    =  $data['lname']; 
      $contactname = $fname." ".$lname ;
      $contactphone = $data['phone'];  
      $email = $data['email'];  
      $biography = $data['biography'];

      $maillink='Name: '.$contactname.'<br>Email Id: '.$email .'<br>Phone: '.$contactphone.'<br>Message: '.$biography; 
                         /* code for mail start   */
                        $footer = "The Admin Team";
                        $html = new Mime\Part($maillink);
                        $html->type = Mime\Mime::TYPE_HTML;
                        $text = new Mime\Part($footer);
                        $text->type = Mime\Mime::TYPE_TEXT;
                        $maillink = new Mime\Message();
                        $maillink->setParts(array($html, $text));
                        $message = new \Zend\Mail\Message();
                        $message->setBody($maillink);
                        $message->setFrom($email);
                        $message->setSubject("New contact us Entry");
                        $message->addTo($emailreceiver) ;
                        $SmtpOptions =  new \Zend\Mail\Transport\SmtpOptions();
                        $SmtpOptions->setHost('smtp.gmail.com') 
                              ->setConnectionClass('login')
                              ->setName('smtp.gmail.com')
                              ->setConnectionConfig(array(
                               'username'=>'lasttest126@gmail.com',
                               'password'=>'8505849556',
                               'ssl'     =>'tls',
                        ));
                        $transport  =  new \Zend\Mail\Transport\Smtp($SmtpOptions) ;
                        $transport->send($message);
                         /* code for mail start   */
        echo "1";die;
      }
     return new ViewModel();
    }
   /* public function providerlistAction()
    { 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $data = (array) $data1;
     $result=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectProAndLocation($docid); 
     $providerlist = json_encode($result);
     return new viewModel(array('providerlist'=>$providerlist));
    } 
    */
    public function providerlistAction()
    { 
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $data = (array) $data1;
     $locationnochk=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectDocLocation($docid);
     if(count($locationnochk)>0)
     {
     $result=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectProAndLocation($docid); $providerlist = json_encode($result);
       return new viewModel(array('providerlist'=>$providerlist));
     }else{
       $result=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectProviderDocId($docid); $providerlist = json_encode($result);
       return new viewModel(array('providerlist'=>$providerlist));
       }
    } 
    /*hedayat**/
    
    public function doctorprofileAction()
  { 
    $provider_id= $this->params()->fromRoute('id',null);   
    $session = new Container('User');
    $docid  =$session->offsetGet('userId');
    $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailsFromId($docid);
    $providers= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->prividerById($provider_id);
	//echo $provider_id.'hedayat';
    //print_r($providers);exit;
    $location= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectLocById($providers[0]['location_info_id']);
    //print_r($location);exit;
    //$locdata = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectDoctorLocPro($docid) ;
    //print_r($providers);
     $providers[0]['pro_avatar'] = $providers[0]['pro_avatar'] ? $providers[0]['pro_avatar'] : 'profile-pic.jpg';
     $request= $this->getRequest();
     if ($request->isPost())
     { 
          $doc_username= $request->getPost('doc_username');
          $doc_firstname= $request->getPost('doc_firstname');
          $doc_lastname= $request->getPost('doc_lastname');
          $doc_email= $request->getPost('doc_email');
          $doc_phone= $request->getPost('doc_phone');
          $doc_zip= $request->getPost('doc_zip');
          $doc_address= $request->getPost('doc_address');
          $doc_phone2= $request->getPost('doc_phone2');
          $doc_sex= $request->getPost('doc_sex');
          $doc_speciality= $request->getPost('doc_speciality');
          $doc_license_no= $request->getPost('doc_license_no');
          $doc_contact_person= $request->getPost('doc_contact_person');
          $doc_status= $request->getPost('doc_status');
          $doc_id= $request->getPost('doc_id');
          $savedoctor=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->savedoctor($doc_id,$doc_firstname,$doc_lastname,$doc_email,$doc_phone,$doc_zip,$doc_address,$doc_phone2,$doc_sex,$doc_speciality,$doc_license_no,$doc_contact_person,$doc_status,$doc_username);
          $msg =1;
          $this->redirect()->toRoute('dashboardfront',array('action'=>'doctoredit','id'=>$msg)); 
     }
     return new ViewModel(array('doc_id'=>$docid,'doc_r'=>$doctorDetails,'msg'=>$msg,'location'=>$location,'providers'=>$providers));
  }
  public function savedoctorbioAction(){
	$session = new Container('User');
	$userdatails = $session->offsetGet('userdetail');
	$doctorid = $userdatails['doc_id'];
	$post_date = file_get_contents("php://input");
	$data = json_decode($post_date,true);  
	//echo $post_date;exit;
	$msg = array();
	if($post_date){	  
		$savedoctor=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->savedoctorbio($data['id'],strip_tags($data['bio']));
		
		$msg['success']=1; 
		//$msg['data']=$post_date; 
	}
	echo json_encode($msg);exit;
  }
  public function privateplanAction(){
	$session = new Container('User');
	$userdatails = $session->offsetGet('userdetail');
	$doctorid = $userdatails['doc_id'];
	$feeschedule=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->getfeeschedule($doctorid);
	//print_r($feeschedule);
	$adalist=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->getadalist();

	$arrangedata = array();
	foreach($feeschedule as $key=>$val){
		if($val['price']!=0){
		foreach($adalist as $adaval){
			if($val['ada_code']==$adaval['ada_code']){
				if(!empty($adaval['category'])){
				 $adaval['price']=$val['price'];
				 $arrangedata[$adaval['category']][] = $adaval;
				 
				 $arrangedata[$adaval['category']][0]['silver'] = '100';
				 $arrangedata[$adaval['category']][0]['platinum'] = '80';
				 $arrangedata[$adaval['category']][0]['diamond'] = '60';
			 }
			}
			
		}
		}
	 }
	
	
	//print_r($arrangedata);exit;
	echo json_encode($arrangedata);exit;
	
  }
  public function submitbankinguserAction(){
	$session = new Container('User');
	$userdatails = $session->offsetGet('userdetail');
	$doctorid = $userdatails['doc_id'];
	$post_date = file_get_contents("php://input");
	$data = json_decode($post_date,true);  
	
	if(!empty($data)){
		
		
				$planid= array();
				$session = new Container('User');
				$doc_id  =$session->offsetGet('userId'); 
				 
				//  $doc_id = $request->getPost('doc_id');
				$plan_name = 'Silver Plan';
				$heading_line = $data['pricing']['silver'];
				$plan_monthly_price = $data['pricing']['silvermember'];
				$plan_yearly_price = $data['discount']['yearly_max_silver'];
				$addon_monthly_price = $data['pricing']['silveraddon'];
				$addon_yearly_price = ''; 
								
				$planid['silverplanid']=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlans($doc_id,$plan_name,$heading_line,$plan_monthly_price,$plan_yearly_price,$addon_monthly_price,$addon_yearly_price);
				
				
				$planid['platinumplanid']=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlans($doc_id,'Platinum Plan',$data['pricing']['platinum'],$data['pricing']['platinummember'],$data['discount']['yearly_max_platinum'],$data['pricing']['platinumaddon'],$addon_yearly_price);
				
				$planid['diamondplanid']=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlans($doc_id,'Diamond Plan',$data['pricing']['diamond'],$data['pricing']['diamondmember'],$data['discount']['yearly_max_diamond'],$data['pricing']['diamondaddon'],$addon_yearly_price);
				
				/*business plan*/
			if($data['business']){
				$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlans($doc_id,'Business Silver Plan','',$data['pricing']['businesssilver'],'','','');
				$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlans($doc_id,'Business Platinum Plan','',$data['pricing']['businesplatinum'],'','','');
				$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlans($doc_id,'Business Diamond Plan','',$data['pricing']['businessdiamond'],'','','');
			}
				/*Fee shedhule*/
				foreach($planid as $plankey=>$pid){
					
					
					foreach($data['fees'] as $key=>$value){
						
					   $service_type  = $value[0]['category'];
					   if($plankey == 'silverplanid')
							$service_notes = $value[0]['silver'];
					   if($plankey == 'platinumplanid')
							$service_notes = $value[0]['platinum'];
					   if($plankey == 'diamondplanid')
							$service_notes = $value[0]['diamond'];
					   
					   foreach($value as $i =>$val){
						   //print_r($val);exit;
							${"service_name".$i}  = $val['ada_code'].' '.$val['desc'];
						}
					   
				$saveplan= $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->addPlanServices($service_type,$service_name1,$service_name2,$service_name3,$service_name4,$service_name5,$service_name6,$service_name7,$service_name8,$service_name9,$service_name10,$service_notes,$pid);
				}
			}
			
			/*save finance details*/
			$financedata['lowestpayment'] = isset($data['finance']['lowestpayment'])?$data['finance']['lowestpayment']:'';
			$financedata['downpayment'] = isset($data['finance']['downpayment'])?$data['finance']['downpayment']:'';
			$financedata['chargelatefee'] = isset($data['finance']['chargelatefee'])?$data['finance']['chargelatefee']:'';
			
			
			$savedoctor=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->savedoctorfinance($doc_id,$financedata);
			
	
	}
	print_r($data);
	exit;
	
	
  }
  public function patientprofileAction()
    { 
     $patid= $this->params()->fromRoute('id',null);   
     $session = new Container('User');
     $docid  =$session->offsetGet('userId');
     $patdetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatientsdetailsById($patid);
     $plandetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PlanDetails($patdetails[0]['plan_id']);
    // print_r($patdetails);die;
     return new ViewModel(array('doc_id'=>$docid,'patient_details'=>$patdetails,'plandetails'=>$plandetails));
    }  

  
/*  shashi function here start                          */
 public function newreferralAction()
 {
 $patid= $this->params()->fromRoute('id',null);  
 $session = new Container('User');
 $docid  =$session->offsetGet('userId'); 
 $doctorSpacilityDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->doctorDetailFromDocSpacility();
 $post_date = file_get_contents("php://input");
 $data = json_decode($post_date,true);  
 $request= $this->getRequest();
      if ($request->isPost())
          { 
            $dentist_type = $data['provider_dentist_id'];
            $datearray=explode('/',$data['dob']);
            $aptData=$datearray[2].'-'.$datearray[0].'-'.$datearray[1]; 
            $refer_to=$data['provider_id'];
            $refer_by=$docid ; 
            $patid=$data['pat_id'];
            $alt1=$data['alt1'];   
            $alt2= $data['alt2'];     
            $comments= $data['comments']; 
            $appointment_date =$aptData; 
            $appointment_time= $data['timing'];  
            $doctorDetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertReferPatient($refer_by,$patid,$refer_to,$alt1,$alt2,$comments,$appointment_date,$appointment_time,$dentist_type);
            echo "1";
            die;
         }
     return new ViewModel(array('pat_id'=>$patid,'providerlist'=>json_encode($doctorSpacilityDetails),'providerlist1'=>$specialServicesDetails));
   }
 public function patientimagechangeAction()
    { 
          $pat_id = (int) $this->params()->fromRoute('id',null);
       
         $uploadOk = 1;
         $success_msg=0 ;
         $doc_avatardetail = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectPatientAvatar($pat_id) ;
      
         $request = $this->getRequest() ;    // getting current request object     
         if ($_FILES["file"]["name"])
		 { 
				$msg = array();
				$doc_avatar = rand().'avatar_'.$pat_id.substr($_FILES["file"]["name"],strpos($_FILES["file"]["name"],'.'));
				$target_file = $_SERVER['DOCUMENT_ROOT']."/img/placeholders/avatars/".$doc_avatar;
				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
				// Check if image file is a actual image or fake image
				$check = getimagesize($_FILES["file"]["tmp_name"]);
				if($check== false) {
					//  $err[] = "File is an image - " . $check["mime"] . ".";
					$uploadOk = 0;
					$msg['error'] = "File is an image - " . $check["mime"] . ".";
					
				}else if($_FILES["file"]["size"] > 2000000) {
					// $err[] = "Sorry, your file is too large.";
					$uploadOk = 0;
					$msg['error'] = "Sorry, your file is too large.";
					//echo "2";
				}else if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
					//  $err[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					$uploadOk = 0;
					$msg['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					//echo "3";
				}
					// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					$success_msg=0 ;
				  }else{  
					   $success_msg = 1;
					   
					   $saveavatar= $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->editPatientAvatar($pat_id,$doc_avatar);
					   //$doc_avatardetail = $this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->selectDoctorAvatar($doc_id) ;
					   move_uploaded_file($_FILES["file"]["tmp_name"], $target_file); 
					   //$success_msg=3 ;
					   //$success_msgtext= "The file ". basename($_FILES["file"]["name"]). " has been uploaded.";
					   $msg['file'] = "/img/placeholders/avatars/".$doc_avatar;					   
					   
					   
					   //return new ViewModel(array('success_msg'=>$success_msg,'id'=>$doc_id,'doc_avatar'=>$doc_avatardetail,'msg_del_done'=>$msg_del_done,'uploadOk'=>$uploadOk,'success_msgtext'=>$success_msgtext));
					}   
					$msg['success']=$success_msg;    
       }
       echo json_encode($msg);
       exit;
       //return new ViewModel(array('success_msg'=>$success_msg,'id'=>$doc_id,'doc_avatar'=>$doc_avatardetail,'msg_del_done'=>$msg_del_done,'uploadOk'=>$uploadOk,'success_msgtext'=>$success_msgtext));
    }
	public function referralpopup2Action(){
		/*
		 * Status 0= pending, 1=Accept, 2=decline,3=accept but call, 4=missed
		 * */
		$session = new Container('User');
		$doc_id  =$session->offsetGet('userId');  	
		$doc_lastname  =$session->offsetGet('doc_lastname');  	
			
		$providers = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->prividerByDocId($doc_id);
		
		
		$sendarray = array();
		foreach($providers as $k){
			array_push($sendarray,$k['id']);
		}
		$refer = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->referByCall(implode(',',$sendarray));
			//print_r($refer);
		return new ViewModel(array('refer'=>$refer,'doc_lastname'=>$doc_lastname));
	}
	public function referralpopup1Action(){
		/*
		 * Status 0= pending, 1=Accept, 2=decline,3=accept but call, 4=missed
		 * */
		$session = new Container('User');
		$doc_id  =$session->offsetGet('userId');  	
		$doc_lastname  =$session->offsetGet('doc_lastname');  	
			
		$providers = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->prividerByDocId($doc_id);
		
		
		$sendarray = array();
		foreach($providers as $k){
			array_push($sendarray,$k['id']);
		}
		$refer = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->refer(implode(',',$sendarray));
			
		return new ViewModel(array('refer'=>$refer,'doc_lastname'=>$doc_lastname));
	}
	public function submitpopupAction(){
		/*
		 * Status 0= pending, 1=Accept, 2=decline,3=accept but call, 4=missed
		 * */
	  $request = $this->getRequest();
      if ($request->isPost())
      {
		  $session = new Container('User');
		  $docid  =$session->offsetGet('userId');
		  $post_date = file_get_contents("php://input");
		  $data = json_decode($post_date,true);
		  
		  
		if($data['reftype']=='accept'){
			
		  foreach($data['time'] as $refer_id=>$val){		  
				$refer = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->referAccept($refer_id,$data['procedure'][$refer_id]);
		  }
		}elseif($data['reftype']=='decline'){
			
		  foreach($data['time'] as $refer_id=>$val){		  
				$refer = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->referDecline($refer_id);
		  }
		}elseif($data['reftype']=='call'){
			
			foreach($data['time'] as $refer_id=>$val){ 
				$refer = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->referCall($refer_id);
		  }
		}
      
	}
	//print_r($data);
      echo '1';
      exit;
  }
  public function submitpopup2Action(){
		/*
		 * Status 0= pending, 1=Accept, 2=decline,3=accept but call, 4=missed
		 * */
	  $request = $this->getRequest();
      if ($request->isPost())
      {
		  $session = new Container('User');
		  $docid  =$session->offsetGet('userId');
		  $post_date = file_get_contents("php://input");
		  $data = json_decode($post_date,true);
		  
		  
		//if($data['reftype']=='accept'){
			
		  foreach($data['times'] as $refer_id=>$val){		  
				$datearray=explode('/',$data['date'][$refer_id]);
				$apttimes=$data['times'][$refer_id];
				$aptData=$datearray[2].'-'.$datearray[0].'-'.$datearray[1];
				
				$refer = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->referAcceptByCall($refer_id,$data['procedure'][$refer_id],$aptData,$apttimes);
		  }
		//}
      
	}
	//print_r($data);
      echo '1';
      exit;
  }
public function referralalertAction(){
	  echo 'hedayat';
	  $refer = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->findRefer();
	  foreach($refer as $val){
		  
		//$to_time = strtotime($val['refer_time'].' +1 day');
		$to_time = strtotime($val['refer_time'].' +10 minutes');
		//echo date('Y-m-d H:i:s',$to_time).'->'.date('Y-m-d H:i:s');
		$from_time = strtotime(date('Y-m-d H:i:s'));
		$getdif =  ($to_time - $from_time).'<br>';
		if($getdif < 0){
			$insernewrow = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->changeReferStatusMissed($val['ref_id']);
			if($val['alternate1']==0){
				$insernewrow = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertalternate2($val);
			}else{
			$insernewrow = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertalternate1($val);
			}
			echo 1;
		}
	  }
	  
	  //print_r($refer);
	  exit;
	}
  public function referthanksAction()
    { 
    $data= $this->params()->fromRoute('id',null); 
    $dataArray=explode('-',$data); 
    $patid=$dataArray['0'];
    $providerid=$dataArray['1'];
    $alt1=$dataArray['2'];
    $alt2=$dataArray['3'];

   $patdetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->listPatientsdetailsById($patid);
    $patientname=$patdetails[0]['patient_firstname']." ".$patdetails[0]['patient_lastname'];

    $providerdetails=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->prividerById($providerid);
    $providername=$providerdetails[0]['first_name'].' '.$providerdetails[0]['last_name'];

    if($alt1!=0){
    $alt1details=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->prividerById($alt1);	
    $alt1name= $alt1details[0]['first_name'].' '. $alt1details[0]['last_name'];
    }

    if($alt2!=0){
    $alt2details=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->prividerById($alt2);	
    $alt2name= $alt2details[0]['first_name'].' '. $alt2details[0]['last_name'];
    }
   return new ViewModel(array('patient_name'=>$patientname,'providername'=>$providername,'alt1name'=>$alt1name,'alt2name'=>$alt2name));
    } 

    public function specialistreferralAction()
    { 
    $session = new Container('User');
    $docid  =$session->offsetGet('userId'); 
    $result=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->referralListingoForSpecilist($docid); 
    $receivereferral=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferalCountToDoctor($docid);
    $acceptreferral=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferalAcceptToDoctor($docid);

    $missedreferral=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferalMissedToDoctor($docid);
    $declinereferral=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferalDeclineToDoctor($docid);
   
    $pendingreferral=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->totalReferalPendingToDoctor($docid);
    $receivereferralcount= $receivereferral[0]['total_receive'];
   $acceptreferralcount= $acceptreferral[0]['total_accept'];
   $missedreferralcount= $missedreferral[0]['total_missed']; 
 
   $declinereferralcount= $declinereferral[0]['total_decline'];
   $pendingreferralcount= $pendingreferral[0]['total_pending'];

    $referrallistdata= json_encode($result);
     return new viewModel(array('referrallistdata'=>$referrallistdata,
     	'receivereferral'=>$receivereferralcount,'acceptreferralcount'=>$acceptreferralcount,'declinereferral'=>$declinereferralcount,'pendingreferral'=>$pendingreferralcount,'missedreferral'=>$missedreferralcount
     	));
    }
 
/*  shashi function here end     */  
public function bankingdoctorspecialistAction()
	{    
		$doctorid= $this->params()->fromRoute('id',null); 
		if(isset($doctorid)){
		   $doctordetails=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->doctorDetailsFromId($doctorid);
		}
		$request = $this->getRequest() ;    // getting current request object
		$success_msg='';
		
		
        if ($request->isPost())
        {  
			
			$session = new Container('User');
			$userdatails = $session->offsetGet('userdetail');
			
			$doctorid = $userdatails['doc_id'];
			$post_date = file_get_contents("php://input");
			$data = json_decode($post_date,true);
			

			$doc_data['deposit_account_no']= $data['account_number'];
			$doc_data['deposit_routing_no']= $data['routing_number'];
			$doc_data['bank_account_type']= '';
			$doc_data['deposit_acc_holder_name']= $data['name'];		
			$doc_data['doc_id'] = $doctorid;
			
			$stripeToken = $request->getPost('stripeToken');  
			/*$doc_data = array();  
			$doc_data['bank_routing_number']= $request->getPost('bank_routing_number');
			$doc_data['bank_account_number']= $request->getPost('bank_account_number');
			$doc_data['bank_account_type']= $request->getPost('bank_account_type');
			$doc_data['legal_name']= $request->getPost('legal_name');		
			$doc_data['doc_id']= $request->getPost('doc_id');   
			* */       
			$savedoctor=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->savedoctorbank($doc_data);			
			$doctordetails=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->doctorDetailsFromId($doctorid);
			//echo '<pre>'; print_r($doctordetails);
          
				//close for dev
				/*\Stripe\Stripe::setApiKey($this->stripesecret_key);
				$account = \Stripe\Account::create(array(
				"managed" => false,
				"country" => "US",
				"email" => $doctordetails[0]['doc_email']
				));
				
				$aes = new \MyModule\AES($account->id, $this->inputKey, $this->blockSize);
				$enc_acid = $aes->encrypt();
				*///close for dev
								
				//$enc_acid = $account->id;	
							
				//echo '<pre>';
				//print_r($account);
				/*$cu = \Stripe\Account::retrieve($account->id);
				$bankdetails = $cu->external_accounts->create(array("external_account" => $stripeToken));
				
				$aes = new \MyModule\AES($bankdetails->id, $this->inputKey, $this->blockSize);
				$enc_acid = $aes->encrypt();*/
				
				//$aes->setData($enc);
				//$subs_id=$enc ;
				
				
				
				$doc_ac_data = array();
				//$doc_ac_data['account_id'] = $enc_acid;//close for dev
				$doc_ac_data['account_id'] = '';
				$doc_ac_data['bank_acc_id'] = '';
				$doc_ac_data['doc_id'] = $doc_data['doc_id'];
				
				$savedoctorac=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->saveDoctorBankAc($doc_ac_data);
				$success_msg='Bank account saved successfully.';
				/*$update_qry = "update doctor_details set account_id='".mysqli_real_escape_string($cn,myencrypt($account->id))."' where doc_id=".mysqli_real_escape_string($cn,$doc_id);
				mysqli_query($cn,$update_qry);*/
				
				/*$update_qry = "update doctor_details set bank_acc_id='".mysqli_real_escape_string($cn,myencrypt($bankdetails->id))."' where doc_id=".mysqli_real_escape_string($cn,$doc_id);
				mysqli_query($cn,$update_qry);*/
				echo 1;exit;
				
				//  print_r($request->getPost());die ;
        
        }
        $doctordetails =array();
        //print_r($doctordetails);
     return new ViewModel(array('success_msg'=>$success_msg,'doctorid'=>$doctorid,'doctordetails'=>$doctordetails,'publishable_key'=>$this->publishable_key));
  }

public function docfeescheduleAction(){
		return new ViewModel(array());
	}
public function updatepriceAction(){
		$session = new Container('User');
		$docid  =$session->offsetGet('userId');
		 
		$plans=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->DoctorPlansListing($docid);
		//print_r($plans);
		$rdata = array();
		//foreach($plan as $val){
			$rdata['plan_id1']=$plans[0]['plan_id'];
			$rdata['silver']=$plans[0]['heading_line'];
			$rdata['silvermember']=$plans[0]['plan_monthly_price'];
			$rdata['silveraddon']=$plans[0]['addon_monthly_price'];
			
			$rdata['plan_id2']=$plans[1]['plan_id'];
			$rdata['platinum']=$plans[1]['heading_line'];
			$rdata['platinummember']=$plans[1]['plan_monthly_price'];
			$rdata['platinumaddon']=$plans[1]['addon_monthly_price'];
			
			$rdata['plan_id3']=$plans[2]['plan_id'];
			$rdata['diamond']=$plans[2]['heading_line'];
			$rdata['diamondmember']=$plans[2]['plan_monthly_price'];
			$rdata['diamondaddon']=$plans[2]['addon_monthly_price'];
			
			$rdata['plan_id4']=$plans[3]['plan_id'];
			$rdata['plan_id5']=$plans[4]['plan_id'];
			$rdata['plan_id6']=$plans[5]['plan_id'];
			$rdata['businesssilver']=$plans[3]['plan_monthly_price'];
			$rdata['businesplatinum']=$plans[4]['plan_monthly_price'];
			$rdata['businessdiamond']=$plans[5]['plan_monthly_price'];
			
			if($plans[3]['plan_monthly_price']!='0.0000' || $plans[4]['plan_monthly_price']!='0.0000' || $plans[5]['plan_monthly_price']!='0.0000'){
				$rdata['business']='1';
			}else{
				$rdata['business']='0';
			}
			
			
		//}
		//echo '<pre>';
		//print_r($rdata);
		return new ViewModel(array('plans'=>$rdata));
	}
	public function plansaveingAction(){
		$planid= array();
		$session = new Container('User');
		$doc_id  =$session->offsetGet('userId'); 
		$post_data = file_get_contents("php://input");
		$data = json_decode($post_data,true);
		  //print_r($data);exit;
		if(!empty($data)){
		//  $doc_id = $request->getPost('doc_id');
		$plan_name = 'Silver Plan';
		$heading_line = $data['pricing']['silver'];
		$plan_monthly_price = $data['pricing']['silvermember'];
		$plan_yearly_price = '';
		$addon_monthly_price = $data['pricing']['silveraddon'];
		$addon_yearly_price = ''; 
						
		//updatePlans($plan_id,$heading_line,$plan_monthly_price,$addon_monthly_price)
		$planid['silverplanid']=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->updatePlans($data['pricing']['plan_id1'],$heading_line,$plan_monthly_price,$addon_monthly_price);
		
		$planid['platinumplanid']=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->updatePlans($data['pricing']['plan_id2'],$data['pricing']['platinum'],$data['pricing']['platinummember'],$data['pricing']['platinumaddon']);
		
		$planid['diamondplanid']=$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->updatePlans($data['pricing']['plan_id3'],$data['pricing']['diamond'],$data['pricing']['diamondmember'],$data['pricing']['diamondaddon']);
		
		/*business plan*/
		if($data['business']==1){
			
		$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->updatePlans($data['pricing']['plan_id4'],'Business Silver Plan',$data['pricing']['businesssilver'],'');
		$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->updatePlans($data['pricing']['plan_id5'],'Business Platinum Plan',$data['pricing']['businesplatinum'],'');
		$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->updatePlans($data['pricing']['plan_id6'],'Business Diamond Plan',$data['pricing']['businessdiamond'],'');
		
		}else{
			$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->updatePlans($data['pricing']['plan_id4'],'Business Silver Plan','','');
			$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->updatePlans($data['pricing']['plan_id5'],'Business Platinum Plan','','');
			$this->getServiceLocator()->get('Dashboard\Model\DashboardTable')->updatePlans($data['pricing']['plan_id6'],'Business Diamond Plan','','');
			
		}
	
		}
		echo 1;
		exit;
	}
	 public function submitspecialistbankingAction(){
	$session = new Container('User');
	$userdatails = $session->offsetGet('userdetail');
	$doctorid = $userdatails['doc_id'];
	$post_date = file_get_contents("php://input");
	$data = json_decode($post_date,true);  
	
	if(!empty($data)){
		
		
				$planid= array();
				$session = new Container('User');
				$doc_id  =$session->offsetGet('userId'); 				 
				
			
			/*save finance details*/
			$financedata['lowestpayment'] = isset($data['finance']['lowestpayment'])?$data['finance']['lowestpayment']:'';
			$financedata['downpayment'] = isset($data['finance']['downpayment'])?$data['finance']['downpayment']:'';
			$financedata['chargelatefee'] = isset($data['finance']['chargelatefee'])?$data['finance']['chargelatefee']:'';
			
			
			$savedoctor=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->savedoctorfinance($doc_id,$financedata);
			
	
	}
	print_r($data);
	exit;
	
	
  }
 function searchzipAction(){
	  
	$data = file_get_contents("php://input");
	$objData = json_decode($data);

	$zipdata=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->searchbyzip($objData->data);
	if(!empty($zipdata))
		echo json_encode($zipdata);
		
	exit;
  
  } 
public function specialistpatientlistAction(){
	  $session = new Container('User');
	  $doc_id  =$session->offsetGet('userId'); 
	  $list = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->ReferralsPatientListing($doc_id);
	  //print_r($list);
	  
  return new ViewModel(array('list'=>$list));
  }
 public function deletelocproviderAction(){
  	$locid= $this->params()->fromRoute('id',null); 
    $zipdata=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteLocAndProviderByLocId($locid);
  }
public function savepataddrAction(){
	$post_date = file_get_contents("php://input");
	$data = json_decode($post_date,true);  
	//print_r($data);exit;
	$msg = array();
	if($post_date){	  
		$savedoctor=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->savepataddress($data['pat_id'],strip_tags($data['address']));
		
		$msg['success']=1; 
		//$msg['data']=$post_date; 
	}
	echo json_encode($msg);exit;
  }
	public function docpatienteditAction()
  {  	  
    
    $patient_id = $this->params()->fromRoute('id',null);  
	//$planid =6;
    $session = new Container('User');
	$docid  =$session->offsetGet('userId');	
	if(!empty($patient_id)){
	$patientdetail= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->PatientDetails($patient_id,$docid); 
	$patients = array();
	$patients['first_name'][1] = $patientdetail[0]['patient_firstname'];
	$patients['last_name'][1] = $patientdetail[0]['patient_lastname'];
	$patients['sex'][1] = $patientdetail[0]['patient_sex']?$patientdetail[0]['patient_sex']:0;
	$datearray=explode('-',$patientdetail[0]['patient_dob']);
	
	$patients['dob'][1] = $datearray[1].'/'.$datearray[2].'/'.$datearray[0];
	$patients['phone'][1] = $patientdetail[0]['patient_phone'];
	$patients['address'][1] = $patientdetail[0]['patient_address'];
	$patients['zip'][1] = $patientdetail[0]['patient_zip'];
	$patients['city'][1] = $patientdetail[0]['patient_city'];
	$patients['state'][1] = $patientdetail[0]['patient_state'];
	$patients['email'][1] = $patientdetail[0]['patient_email'];
	$patients['pat_id'][1] = $patientdetail[0]['patient_id'];
	}
	$request= $this->getRequest();
	if($request->isPost()){
	
		$post_date = file_get_contents("php://input");
		$data = json_decode($post_date,true);
		
		$returndata = array();
			
					
			//echo $data['last_name'][$key];exit;
				
			// print_r($request->getPost());die;
			$update = array();
			
			
			$update['patient_firstname']=$data['first_name'][1];
			$update['patient_lastname']=$data['last_name'][1];
			
			$datearray=explode('/',$data['dob'][1]);
			
			$update['patient_dob']=$datearray[2].'-'.$datearray[0].'-'.$datearray[1];;
			$update['patient_sex']=$data['sex'][1];
			$update['patient_address']=$data['address'][1];
			$update['patient_zip']=$data['zip'][1];
			$update['patient_city']=$data['city'][1];
			$update['patient_state']=$data['state'][1];
			$update['patient_phone']=$data['phone'][1];
			
			$update['patient_email']=$data['email'][1];
			
			
			$patid = $data['pat_id'][1];
 
		  $returnid=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->patientUpdateByDoc($update,$patid); 
		 // echo  $pat_id ;die;       
       
          
         						
   
	   echo 1;exit;
   }    // post bracket close here ...
     return new ViewModel(array('patientid'=>$patient_id,'patientdetail'=>$patients));
     //return new ViewModel(array('planid'=>$planid,'patientid'=>$patientid,'parentid'=>$parentid,'addon'=>$addon));
}  
 public function deletereferAction()
   {
  	$referpatientid= $this->params()->fromRoute('id',null); 
    $zipdata=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteReferPatient($referpatientid);
  } 
}





