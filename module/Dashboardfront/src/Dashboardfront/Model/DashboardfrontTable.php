<?php


namespace Dashboardfront\Model;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;


class DashboardfrontTable
{
	 protected $adapter;
   public $resultSetPrototype;
   public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet ();
    }
  public function savesupports($doc_id,$first_name,$last_name,$email_id,$address,$phone,$message)
  {
    $sql = new Sql($this->adapter);
    $sqlquery= "insert into support(`doc_id`, `first_name`, `last_name`, `email_id`, `address`, `phone`, `message`, `add_date`) values('$doc_id','$first_name','$last_name','$email_id','$address','$phone','$message',now())";
    $result= $this->adapter->query($sqlquery,Adapter::QUERY_MODE_EXECUTE) ;
    return $result;
  }
  public function splserviceFromDocId($docid){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('spl_services')->where(array('doc_id'=>$docid));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 

   public function doctorDetailFromDocSpacility()
   {
     $select = "select l.*, p.*,p.id as provider_id,d.kind_of_practice from doctor_details d, location_info l, provider_info p where l.id=p.location_info_id
       and l.doc_id=d.doc_id  and d.doc_speciality>2";  
     $result = $this->adapter->query($select , Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
    } 


  public function servicesDetailsFromId($serviceid){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('spl_services')->where(array('service_id'=>$serviceid));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
  
  public function saveSplServices($service_type,$service_name1,$service_name2,$service_name3,$service_name4,$service_name5,$service_name6,$service_name7,$service_name8,$service_name9,$service_name10,$price,$doc_id,$spl_serviceid){
    if($spl_serviceid){
     $qry = "update spl_services set service_type='$service_type',service_name1='$service_name1',service_name2='$service_name2',service_name3='$service_name3',service_name4='$service_name4',service_name5='$service_name5',service_name6='$service_name6',service_name7='$service_name7',service_name8='$service_name8',service_name9='$service_name9',service_name10='$service_name10',price='$price' where service_id=".$spl_serviceid ;
    }else{
     $qry = "insert into spl_services(`service_type`, `service_name1`, `service_name2`, `service_name3`, `service_name4`, `service_name5`, `service_name6`, `service_name7`, `service_name8`, `service_name9`, `service_name10`, `price`, `doc_id`) values('$service_type','$service_name1','$service_name2','$service_name3','$service_name4','$service_name5','$service_name6','$service_name7','$service_name8','$service_name9','$service_name10','$price','$doc_id')"; 
     }
    $result = $this->adapter->query($qry, Adapter::QUERY_MODE_EXECUTE);
    return $result;
    } 
     public function  deleteservices($id)
    {

             $sql = new Sql ($this->adapter);
             $select = $sql->delete('spl_services')->where(array('service_id'=>$id)) ;
             $statement = $sql->prepareStatementForSqlObject ($select);
             $result = $this->resultSetPrototype->initialize ($statement->execute ());
             return $result;  
    }


  public function totalDoctorPatientCount($docid){
      $sql = new Sql ($this->adapter);
      $countRowQuery = "SELECT
              (select count(patient_id) as pat_total from patients where doc_id=$docid and patient_status=1) as totalPatients, (select count(*) as totalref from refer_patient where refer_to=$docid) as totalref,(select count(ref_id) as refer_accepted  from refer_patient where status=1 and refer_to=$docid) as refer_accepted ,(select count(ref_id) as refer_rejected  from refer_patient where status=2 and refer_to=$docid) as refer_rejected ,
              (select sum(pp.amount) as pat_total_revenue from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id=$docid group by MONTH(pp.pay_date) order by pp.pay_date desc limit 1) as total_income" ;
   $countData = $this->adapter->query($countRowQuery, Adapter::QUERY_MODE_EXECUTE)->toArray();
   return $countData;
    }
  public function changeAdminPassword($password,$userid)
    {  
    $sql = new Sql($this->adapter);
    $select = $sql->update('users')->set(array('password'=>md5($password)))->where(array(
            'user_id' =>$userid)); 
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $this->resultSetPrototype->initialize ($statement->execute ());
    return $result ;
   }
  public function changeAdminUsername($adminuser,$userid)
    {  
    $sql = new Sql($this->adapter);
    $select = $sql->update('users')->set(array('email'=>$adminuser))->where(array(
            'user_id' =>$userid)); 
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $this->resultSetPrototype->initialize ($statement->execute ());
    return $result ;
   }
  public function doctorDetails(){
    $sql = new Sql ($this->adapter);
    $select = $sql->select()
                 ->from('doctor_details');
    $statement = $sql->prepareStatementForSqlObject ($select);
    $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
      public function PlansListing(){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('plans');
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
    public function DoctorPlansListing($doc_id){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('plans')->where(array('doc_id'=>$doc_id));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
     foreach($result as $plankey=>$planvalue)
     { 
      $planid=$planvalue['plan_id'];
      if($planid)
        {
          $sql= "select count(*) as patientcount from patients where plan_id=$planid and  doc_id=$doc_id" ;
          $patientcountarray=$this->adapter->query($sql,Adapter::QUERY_MODE_EXECUTE)->toArray();
          $patientcount = $patientcountarray[0]['patientcount'];
          $result[$plankey]['patientcount'] =$patientcount;
        }
     }
    return $result;
    } 
   
   public function listplanservice(){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('plan_services');
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
    public function PlanDetails($plan_id){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('plans')->where(array('plan_id'=>$plan_id));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
    public function PlanserviceDetails($plan_id){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('plan_services')->where(array('plan_id'=>$plan_id));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 

     public function doctorDetailsFromId($docid){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('doctor_details')->where(array('doc_id'=>$docid));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
    public function checkUserid($docusername){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('doctor_details')->where(array('doc_username'=>$docusername));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
    public function checkUseridByEmail($docusername){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('doctor_details')->where(array('doc_email'=>$docusername));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
    public function checkadminUseridByEmail($docusername){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('users')->where(array('email'=>$docusername));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
     return $result;
    } 
  public function saveDoctor($doc_id,$doc_firstname,$doc_lastname,$doc_email,$doc_phone,$doc_zip,$doc_address,$doc_phone2,$doc_sex,$doc_speciality,$doc_license_no,$doc_contact_person,$doc_status,$doc_username,$password,$sequrity_code,$tinnumber){
        if($doc_status=='on') {
         $docstatus = 1;
          } else {
            $docstatus = 0;
          }
      $salt = 'aUJGgadjasdgdj';
      $password=sha1($salt . $password); 
    if($doc_id){
     $qry = "update doctor_details set doc_firstname='".$doc_firstname."',doc_lastname='".$doc_lastname."',doc_speciality='".$doc_speciality."',doc_license_no='".$doc_license_no."',doc_address='".addslashes($doc_address)."',doc_zip='".$doc_zip."',doc_phone='".$doc_phone."',doc_contact_person='".$doc_contact_person."',doc_status='".$docstatus."',doc_sex='".$doc_sex."',doc_speciality_detail='".$doc_speciality_detail."' where doc_id=".$doc_id;
    }else{
     $qry = "insert into doctor_details(doc_firstname,doc_lastname,doc_speciality,doc_username,doc_address,doc_zip, doc_phone, doc_email, doc_pass, doc_contact_person,doc_license_no, doc_status,add_date,doc_sex,doc_speciality_detail,tinno,sequrity_code) values('$doc_firstname','$doc_lastname','$doc_speciality','$doc_username','$doc_address','$doc_zip','$doc_phone','$doc_email','$password','$doc_contact_person','$doc_license_no','$docstatus',now(),'$doc_sex','$doc_speciality_detail','$tinnumber','$sequrity_code')";
     }
    $result = $this->adapter->query($qry, Adapter::QUERY_MODE_EXECUTE);
      return $this->adapter->getDriver()->getLastGeneratedValue();
    } 
    public function updateDoctorDetails($docid,$number,$cvc,$exp_month,$exp_year){
  $update_qry = "update doctor_details set cc_number='$number',cc_month='$exp_month',cc_year='$exp_year' where doc_id=$docid";
	 $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE);
    return $result;
   }
   public function updateDocBilling($docid,$data)
    { 
      $accountno=$data['accountno'] ;
      $routingno = $data['routingno'] ;
      $acc_holder_name = $data['acc_holder_name'];
      $name_on_cc = $data['name_on_cc'] ;
      $cc_number = $data['cc_number'];
      $csv_no = $data['csv_no'];
      $monthno = $data['monthno'];
      $exp_year = $data['exp_year'];
      $account_type = $data['accounttype'];
      

      $qry = "update doctor_details set bank_account_number='$accountno',bank_routing_number='$routingno',acc_holder_name='$acc_holder_name',legal_name='$name_on_cc',cc_number='$cc_number',cvc_no='$csv_no',acc_exp_data='$exp_date',creditcart_exp_month='$monthno',creditcart_exp_year='$exp_year', account_type='$account_type',login_step_complete=1  where doc_id=".$docid ;
    $result=$this->adapter->query($qry, Adapter::QUERY_MODE_EXECUTE); 
    return $result;
    }
   public function cancelDoctorSubscription($docid){
    $update_qry = "update doctor_details set subs_cancel_dt=now(),doc_status='0' where doc_id=".$docid ; 
    $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE);
    return $result;
    }
   public function updateDoctorSubscriptionInNotChange($subsid,$custid,$docid)
   {
	
   $update_qry = "update doctor_details set cust_id='$custid',subs_id='$subsid' where doc_id='$docid'";
   $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE);
   return $result;
   }
    
  public function insertDoctorSubscriptionInNotChange($docid,$subsplanamt)
  {
  $insert_qry = "insert into doc_payments(doc_id,amount,pay_date,pay_status) values('$doc_id','$subsplanamt',now(),'1')";
  $result = $this->adapter->query($insert_qry, Adapter::QUERY_MODE_EXECUTE);
  return $result;
  }


    public function patTotalRevenue($docid,$doc_firstname){
    $patTotalRevenue= "select sum(pp.amount) as pat_total_revenue from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id=$docid";
    $result = $this->adapter->query($patTotalRevenue, Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
    } 
  public function PatientDetails($patid,$docUserID){
    $patTotalRevenue= "select p.*,pl.plan_name from patients p, plans pl where p.plan_id=pl.plan_id and p.patient_id='$patid' and p.doc_id='$docUserID'";
    $result = $this->adapter->query($patTotalRevenue, Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
    } 
    

  public function doctorActiveDetails(){
      $sql = new Sql ($this->adapter);
      $activeDoctor="select doc_id from doctor_details where doc_speciality<3 and doc_status=1" ;
      $result= $this->adapter->query($activeDoctor, Adapter::QUERY_MODE_EXECUTE)->toArray();
      return $result;
    }  
  public function PatientPlanDetails($pat_id,$docUserID){
      $sql = new Sql ($this->adapter);
      $PatientPlan="select p.*, pn.plan_name from patients p, plans pn where pn.plan_id=p.plan_id and patient_id='$pat_id'" ;
      $result= $this->adapter->query($PatientPlan, Adapter::QUERY_MODE_EXECUTE)->toArray();
      return $result;
    }  
  public function doctorInActiveDetails(){
      $sql = new Sql ($this->adapter);
      $inActiveDoctor="select doc_id from doctor_details where doc_speciality<3 and doc_status<>1" ;
      $result= $this->adapter->query($inActiveDoctor, Adapter::QUERY_MODE_EXECUTE)->toArray();
      return $result;
    } 
    public function PatientDetailsById($patient_id){
      $sql = new Sql ($this->adapter);
      $patients="select * from patients where patient_id=$patient_id" ;
      $result= $this->adapter->query($patients, Adapter::QUERY_MODE_EXECUTE)->toArray();
      return $result;
    } 
    public function  deleteDoctors($id)
    {
             $sql = new Sql ($this->adapter);
             $select = $sql->delete('doctor_details')->where(array('doc_id'=>$id)) ;
             $statement = $sql->prepareStatementForSqlObject ($select);
             $result = $this->resultSetPrototype->initialize ($statement->execute ());
             return $result;  
    }
  public function specialistdoctorActiveDetails(){
      $sql = new Sql ($this->adapter);
      $activeDoctor="select doc_id from doctor_details where doc_speciality=3 and doc_status=1" ;
      $result= $this->adapter->query($activeDoctor, Adapter::QUERY_MODE_EXECUTE)->toArray();
      return $result;
    }  
  public function specialistdoctorInActiveDetails(){
      $sql = new Sql ($this->adapter);
      $inActiveDoctor="select doc_id from doctor_details where doc_speciality=3 and doc_status<>1" ;
      $result= $this->adapter->query($inActiveDoctor, Adapter::QUERY_MODE_EXECUTE)->toArray();
      return $result;
    } 


  public function viewSupport(){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('support');
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
  public function viewReports($reportid,$doc_id){
     $doc_id=$doc_id;
     $sql = new Sql ($this->adapter);
      if($reportid=='1'){
     $report ="select sum(pp.amount) as pat_total_revenue, YEAR(pp.pay_date) as col1 from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id=$doc_id group by YEAR(pp.pay_date) order by pp.pay_date desc" ; 
     }else if($reportid=='2'){
     $report ="select sum(pp.amount) as pat_total_revenue, MONTH(pp.pay_date) as col1, YEAR(pp.pay_date) as col2 from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id='$doc_id' group by MONTH(pp.pay_date) order by pp.pay_date desc" ;
     }else if($reportid=='3'){
     $report ="select sum(pp.amount) as pat_total_revenue, pp.pay_date as col1 from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id='$doc_id' group by pp.pay_date order by pp.pay_date desc" ;
     }else{
      $report = "select sum(pp.amount) as pat_total_revenue from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id='$doc_id' order by pp.pay_date desc" ;
     }
      $result= $this->adapter->query($report,Adapter::QUERY_MODE_EXECUTE)->toArray();  
      return $result;
   } 
    
    public function docPayDetails($docid,$planid){   //1->yearly,2->monthly,3->days
     $sql = new Sql ($this->adapter);
      if($planid=='1'){
     $report ="select sum(pp.amount) as pat_total_revenue, YEAR(pp.pay_date) as col1 from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id=".$docid." group by YEAR(pp.pay_date) order by pp.pay_date desc" ; 
     }else if($planid=='2'){
     $report ="select sum(pp.amount) as pat_total_revenue, MONTH(pp.pay_date) as col1, YEAR(pp.pay_date) as col2 from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id=".$docid." group by MONTH(pp.pay_date) order by pp.pay_date desc" ;
     }else if($planid=='3'){
     $report ="select sum(pp.amount) as pat_total_revenue, pp.pay_date as col1 from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id=".$docid." group by pp.pay_date order by pp.pay_date desc" ;
     }else{
     $report = "select sum(pp.amount) as pat_total_revenue from patients_payments pp, patients p where p.patient_id = pp.patient_id and p.doc_id=".$docid." order by pp.pay_date desc" ;
     }
      $result= $this->adapter->query($report,Adapter::QUERY_MODE_EXECUTE)->toArray();  
      return $result;
   } 
    public function listPatient($docid){
     $patientlist ="select p.*, pn.plan_name from patients p, plans pn where pn.plan_id=p.plan_id and p.doc_id=$docid" ; 
     
     $result= $this->adapter->query($patientlist,Adapter::QUERY_MODE_EXECUTE)->toArray();  
     return $result;
   } 
   public function listPatientForDocPatient($patid,$docid){
     $patientlist ="select p.*,pl.plan_name from patients p, plans pl where p.plan_id=pl.plan_id and p.patient_id='$patid' and p.doc_id='$docid'"; 
    // echo $patientlist ; 
     $result= $this->adapter->query($patientlist,Adapter::QUERY_MODE_EXECUTE)->toArray();  
     return $result;
   } 

    public function paidAmount($st,$en,$doc_id){
     $patientlist ="select amount as amt from doc_payout where payout_date>'".$st."' and payout_date<='".$en."' and payout_status='".'1'."' and doc_id='".$doc_id."'"; 
     $result= $this->adapter->query($patientlist,Adapter::QUERY_MODE_EXECUTE)->toArray();  
     return $result;
   }
   
  public function spanAmount($st,$en,$doc_id){

    $patientlist ="select sum(pp.amount) as amt from patients_payments pp, patients p where pp.pay_date>='".$st."' and pp.pay_date<'".$en."' and pp.patient_id=p.patient_id and p.doc_id='".$doc_id."'"; 
     $result= $this->adapter->query($patientlist,Adapter::QUERY_MODE_EXECUTE)->toArray();  
     return $result;
   }
  public function listPatientsId(){
     $sql = new Sql($this->adapter);
     $select=$sql->select()->from('patients');
     $statement = $sql->prepareStatementForSqlObject($select);
     $result=$this->resultSetPrototype->initialize($statement->execute())->toArray(); 
     return $result;
   } 
  public function savePermission($permisssion,$roleid)
	{   
		$resource_id=1 ;
		$sql = new Sql ($this->adapter);
		$select = $sql->insert('permission')->values(array(
         'id' => '',
         'permission_name' => $permisssion,
         'resource_id'=>$resource_id,
     ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $this->resultSetPrototype->initialize ($statement->execute ());
    $permissionlastId = $this->adapter->getDriver()->getLastGeneratedValue();
    $select = $sql->insert('role_permission')->values(array(
         'id' => '',
         'role_id' => $roleid,
         'permission_id'=>$permissionlastId ,
     ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $this->resultSetPrototype->initialize ($statement->execute ());
    return $result;
	}
    public function saveRagister($username,$email,$password,$roleid,$fname,$lname)
    {
        $status = 'N' ;
        $loginno = '0' ;
        $sql = new Sql($this->adapter);
        $select = $sql->insert('users')->values(array(
            'user_id' => '',
            'username' =>$username,
            'email' =>$email,
            'password'  => $password ,
            'login_no'  => $loginno ,
			      'fname'  => $fname ,
			      'lname'  => $lname ,
   ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $this->resultSetPrototype->initialize ($statement->execute ());
    $userlastId = $this->adapter->getDriver()->getLastGeneratedValue();
    $select = $sql->insert('user_role')->values(array(
         'id' => '',
         'role_id' => $roleid,
         'user_id'=>$userlastId ,
     ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $this->resultSetPrototype->initialize ($statement->execute ());
    return $result; 
  }
  
    public function patientInActiveDetails($docUserID){
      $sql = new Sql ($this->adapter);
      $activeDoctor="select patient_id from patients where doc_id='$docUserID' and patient_status<>1" ;
      $result= $this->adapter->query($activeDoctor, Adapter::QUERY_MODE_EXECUTE)->toArray();
      return $result;
    }  
  public function patientActiveDetails($docUserID){
      $sql = new Sql ($this->adapter);
      $inActiveDoctor="select patient_id from patients where doc_id=$docUserID and patient_status=1" ;
      $result= $this->adapter->query($inActiveDoctor, Adapter::QUERY_MODE_EXECUTE)->toArray();
      return $result;
    } 
  
    public function  delpatient($id)
    {      
             $sql = new Sql ($this->adapter);
             $select = $sql->delete('patients')->where(array('patient_id'=>$id)) ;
             $statement = $sql->prepareStatementForSqlObject ($select);
             $result = $this->resultSetPrototype->initialize ($statement->execute ());
             return $result;  
    }
      public function  deletePlan($id)
    {      
             $sql = new Sql ($this->adapter);
             $select = $sql->delete('plans')->where(array('plan_id'=>$id)) ;
             $statement = $sql->prepareStatementForSqlObject ($select);
             $result = $this->resultSetPrototype->initialize ($statement->execute ());
             return $result;  
    }
     public function insertPatient($plan_id,$plan_cycle,$patient_salutation,$patient_firstname,$patient_lastname,$patient_dob,$patient_ssn,$patient_sex,$patient_address,$patient_zip,$patient_phone,$patient_mobile,$patient_email,$patient_password,$pat_id,$addon,$parent_id,$docid,$patient_status,$patient_consent,$patient_city,$patient_state) 
     {
     $datearray=explode('/',$patient_dob);
     $newDateOB=$datearray[2].'-'.$datearray[0].'-'.$datearray[1];
     if($patient_password==''){
      $patient_password =0;
     }
     if($patient_password==''){
      $patient_password =0;
     }
     if($patient_status=='on')
    $pstatus = 1;
  else
    $pstatus = 0;
  if($patient_consent=='on')
    $pconsent = 1;
  else
    $pconsent = 0;
   if(empty($pat_id)){
     $patientinsert ="insert into patients(patient_salutation,patient_firstname,patient_lastname,patient_ssn,patient_dob,patient_sex,patient_address,patient_zip,patient_phone,patient_mobile,patient_email,patient_pass,patient_family_info,patient_lastprocedure_info,patient_consent,plan_id, doc_id,plan_start_date,plan_cycle,parent_id,patient_city,patient_state) values('$patient_salutation','$patient_firstname','$patient_lastname','$patient_ssn','$newDateOB','$patient_sex','$patient_address','$patient_zip','$patient_phone','$patient_mobile','$patient_email',md5($patient_password),'$patient_family_info','$patient_lastprocedure_info','$pconsent','$plan_id','$docid',now(),'$plan_cycle','$parent_id','$patient_city','$patient_state')";
     }else{
        $patientinsert ="update patients set patient_salutation='$patient_salutation', patient_firstname='$patient_firstname',patient_lastname='$patient_lastname',patient_ssn='$patient_ssn',patient_dob='$newDateOB',patient_address='$patient_address',patient_zip='$patient_zip',patient_phone='$patient_phone' ,patient_mobile='$patient_mobile',patient_family_info='$patient_family_info',patient_lastprocedure_info='$patient_lastprocedure_info',patient_status='$pstatus',patient_consent='$pconsent' where patient_id=".$pat_id; 
     } 
     $result= $this->adapter->query($patientinsert,Adapter::QUERY_MODE_EXECUTE); 
     $userlastId = $this->adapter->getDriver()->getLastGeneratedValue(); 
     return $userlastId;
   }

  public function changeDoctorPassword($password,$userid)
    {  
    $salt = 'aUJGgadjasdgdj';
    $password=sha1($salt . $password); 
    $sql = new Sql($this->adapter);
    $select = $sql->update('doctor_details')->set(array('doc_pass'=>$password))->where(array(
            'doc_id' =>$userid)); 
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $this->resultSetPrototype->initialize ($statement->execute ());
    return $result ;
   }
 public function planstripInsert($stripeplancode,$plan_price)
 {
  $planstripInsert ="insert into plan_stripe(plan_code,amount) values('$stripeplancode','$plan_price')"; 
  $result= $this->adapter->query($planstripInsert,Adapter::QUERY_MODE_EXECUTE); 
  return $result;
 }
  public function getPatientAddons($parent_id)
 {
  $sql=new Sql($this->adapter);
  $select = $sql->select()->from('patients')->where(array('parent_id'=>$parent_id)) ;
  $statement=$sql->prepareStatementForSqlObject($select) ;
  $result=$this->resultSetPrototype->initialize($statement->execute());
  return $result->count();
 }
  public function getMainPatient($patient_id)
 {
  $sql=new Sql($this->adapter);
  $select = $sql->select()->from('patients')->where(array('patient_id'=>$patient_id)) ;
  $statement=$sql->prepareStatementForSqlObject($select) ;
  $result=$this->resultSetPrototype->initialize($statement->execute());
  if($result[0]['parent_id']==0) {
      return $result[0]['patient_id'];
    } else {
      return $result[0]['parent_id'];
    }
 }
  public function chkStripePlanExists($planstringname)
 {
     $sql = new Sql($this->adapter);
     $select=$sql->select()->from('plan_stripe')->where(array('plan_code'=>$planstringname)) ;
     $statement = $sql->prepareStatementForSqlObject($select);
     $result=$this->resultSetPrototype->initialize($statement->execute())->toArray(); 
     return $result;
 }  

 public function stripePlanInsert($stripeplancode,$plan_price){
    $sql = new Sql($this->adapter);
    $select= "insert into plan_stripe(plan_code,amount) values('$stripeplancode','$plan_price')";
    $result= $this->adapter->query($select,Adapter::QUERY_MODE_EXECUTE); 
    return $result;
  }
 public function patientUpdate($encryptcustid,$encryptsubsid,$patid)
 {
  $sql = new Sql($this->adapter);
  $select =$sql->update('patients')->set(array('cust_id'=>$encryptcustid,'subs_id'=>$encryptsubsid))->where(array('patient_id'=>$patid)) ;
 // echo $sql->getSqlStringForSqlObject($select); die;
  $statement = $sql->prepareStatementForSqlObject($select);
  $result=$this->resultSetPrototype->initialize($statement->execute()); 
  return $result;
 }
 public function patientsPaymentsInsert($patid,$planid,$amt,$status)
 {
  $sql = new Sql($this->adapter);
  $select= "insert into patients_payments (patient_id, plan_id,amount,pay_date,pay_status) values('$patid','$planid','$amt',now(),'1')";
  $result= $this->adapter->query($select,Adapter::QUERY_MODE_EXECUTE); 
  return $result;
 }
  public function updatePatientDetails($patid,$encriptnumber,$encriptexp_month,$encriptexp_year){
    $update_qry = "update patients set cc_number='$encriptnumber',cc_month='$encriptexp_month',cc_year='$encriptexp_year' where patient_id=$patid";
    $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE);
    return $result;
    }
 public function SpecialServicesList()
 {
   $sql =new Sql($this->adapter);
   $select= $sql->select()->from('spl_services');
   $statement = $sql->prepareStatementForSqlObject($select);
   $result=$this->resultSetPrototype->initialize($statement->execute())->toArray(); 
  return $result;
 }
public function insertReferPatient($refer_by,$patid,$refer_to,$alt1,$alt2,$comments,$appointment_date,$appointment_time,$dentist_type)
  { 
    
    $date= date('Y-m-d');
    $sql =new Sql($this->adapter);
    $select= $sql->insert('refer_patient')->values(array(
        'patient_id'=>$patid,
        'refer_by'=> $refer_by   ,
        'comments'=>$comments ,
        'refer_to'=>$refer_to,
        'alternate1'=>$alt1 ,
        'alternate2'=>$alt2 ,
        'date_reffered'=>$date ,
        'appointment_date'=>$appointment_date ,
        'appointment_time'=>$appointment_time ,
        'dentist_type'=>$dentist_type 
     )) ;
    $statement = $sql->prepareStatementForSqlObject($select);
    $result=$this->resultSetPrototype->initialize($statement->execute()); 
  }
 public function DoctorReferralsListing($docid){
 $update_qry = "select p.*, r.*, d.doc_firstname, d.doc_lastname from patients p, refer_patient r, doctor_details d where r.refer_by=d.doc_id and p.patient_id=r.patient_id and d.doc_id=".$docid;
    $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE);
    return $result;
  }
  public function SpecilistPatientListing($docid){
   $update_qry = "select p.*, r.*, d.doc_firstname, d.doc_lastname from patients p, refer_patient r, doctor_details d where r.refer_by=d.doc_id and p.patient_id=r.patient_id and d.doc_id=".$docid; 
    $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE);
    return $result;
  }
  public function totalReferToDoctorCount($docid)
  {
    $querystring="select count(ref_id) as totalReferCount from refer_patient where refer_by=$docid" ;
    $result =$this->adapter->query($querystring,Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
  }
  public function ReferralsPatienttoDoctorListingOnPatientClick($docid){
   $update_qry = "select p.*, r.*, d.doc_firstname, d.doc_lastname from patients p, refer_patient r, doctor_details d where r.refer_to=d.doc_id and r.status=1 and p.patient_id=r.patient_id"; 
    $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE);
    return $result;
  }
  public function ReferralsPatientStatusChange($statusid,$referid){
   $update_qry = "update refer_patient set status='$statusid' where ref_id=$referid"; 
   $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE); 
  return $result;
    }
  public function insertTin($tinno)
  {
  $sql = new Sql($this->adapter);
  $select="insert into doctor_details(tinno) values('$tinno')";
  $result= $this->adapter->query($select,Adapter::QUERY_MODE_EXECUTE); 
  return $result;
  }

  public function deleteproviderByDocId($docid){
    $dq= "delete from provider_info where doc_id=$docid";
    $detele=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE);
  }
  public function showStates()
  {
   $sql =new Sql($this->adapter);
   $select= $sql->select()->columns(array('id','name'))->from('states');
   $statement = $sql->prepareStatementForSqlObject($select);
   $results=$this->resultSetPrototype->initialize($statement->execute())->toArray();
   return $results ;
  }
 public function showPatientNameId($doc_id)
  {
   $sql =new Sql($this->adapter);
   // $update_qry = "select p.patient_id,p.patient_salutation,p.patient_firstname,p.patient_lastname,p.patient_email,p.patient_phone,plan.plan_id,plan.plan_name,p_pay.pay_status from patients p, plans plan, patients_payments p_pay where p.patient_id=p_pay.patient_id and plan.plan_id=p_pay.plan_id"; 
   $update_qry="select p.*, pn.plan_name,pn.heading_line from patients p, plans pn where pn.plan_id=p.plan_id and p.doc_id=$doc_id";
   $results=$this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE)->toArray();
   return $results ;
  }
  
  public function selectDocLocation($docid)  
  {
   $sql =new Sql($this->adapter);
   $select= $sql->select()->from('location_info')->where(array('doc_id'=>$docid));
   $statement = $sql->prepareStatementForSqlObject($select);
   $result=$this->resultSetPrototype->initialize($statement->execute())->toArray(); 
   return $result;
  }
   /*  hydayat  */
    public function feeInsert($data){
    $sql =new Sql($this->adapter);
    $select= $sql->insert('fee_schedule')->values(array('doc_id'=>$data['doc_id'],
        'desc'=> $data['desc'],
        //'user_friendly_desc'=>$data['user_friendly_desc'] ,
        'ada_code'=>$data['ada_code'],
        'price'=>$data['price'],
        'status'=>1,
     )) ;
   $statement = $sql->prepareStatementForSqlObject($select);
   $result=$this->resultSetPrototype->initialize($statement->execute()); 
  return $result; 
    }
    public function deleteFeeSchedule($doc_id){
    
  
     $result = $this->adapter->query("SET SQL_SAFE_UPDATES = 0", Adapter::QUERY_MODE_EXECUTE);
     $truncate_qry = "delete from `fee_schedule` where doc_id=$doc_id"; 
     $result = $this->adapter->query($truncate_qry, Adapter::QUERY_MODE_EXECUTE);
     $results = $this->adapter->query("SET SQL_SAFE_UPDATES = 1", Adapter::QUERY_MODE_EXECUTE); 
  
  }
/*  hydayat  */

   public function selectDoctorLocPro($docid){
   $query="select * from location_info where doc_id=$docid" ;
   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
   $returndata = $result;
   foreach($result as $key=>$val){
     $query="select * from provider_info where doc_id=$docid and location_info_id=".$val['id'] ;
     $returndata[$key]['provider']=$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
   }

   return $returndata; 
}

public function getfeeschedule($docid){
	$query="select * from fee_schedule where doc_id=$docid order by category" ;
	$result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
	return $result;
}
public function savedoctorfinance($docid,$data){
	
	$update_qry = "update doctor_details set lowestpayment='".$data['lowestpayment']."', downpayment='".$data['downpayment']."', chargelatefee='".$data['chargelatefee']."', all_steps_complete=1 where doc_id=$docid"; 
	$result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE); 
	return $result;
}
  public function updateDocDepositAccount($docid,$data)
    {  
     $deposit_accountno=$data['accountno'] ;
      $deposit_routingno = $data['routingno'] ;
      $deposit_acc_holder_name = $data['acc_holder_name'];
      $qry1 = "update doctor_details set deposit_account_no='$deposit_accountno',deposit_routing_no='$deposit_routingno',deposit_acc_holder_name='$deposit_acc_holder_name' where doc_id=".$docid ;
    $result=$this->adapter->query($qry1, Adapter::QUERY_MODE_EXECUTE); 
    return $result;
    }
     public function totalReferAcceptedCount($docid)
  {
    $query="select count(ref_id) as totalAcceptedReferCount from refer_patient where status=0 and refer_by=$docid" ;
    $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
  }
   public function totalReferRejectedCount($docid)
  {
    $query="select count(ref_id) as totalRegectedReferCount from refer_patient where status=2 and refer_to=$docid" ;
    $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
  }
   public function ReferralsPatienttoDoctorListing($docid){
     $update_qry = "select p.patient_firstname,p.patient_lastname,r.status,r.alternate1,r.ref_id,r.refer_to,r.alternate2,r.date_reffered,r.dentist_type,r.comments, d.doc_firstname, d.doc_lastname from patients p, refer_patient r, doctor_details d where  r.refer_by=d.doc_id and  p.patient_id=r.patient_id and d.doc_id=$docid";
     $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE)->toArray();
	foreach($result as $key=>$val){
                 if($val['refer_to']){
		 $sql1 = "select first_name, last_name from provider_info where id=".$val['refer_to'];
		 $result1 = $this->adapter->query($sql1, Adapter::QUERY_MODE_EXECUTE)->toArray();
                 }
	         if($val['alternate1']){
	         $sql2 = "select first_name, last_name from provider_info where id=".$val['alternate1'];
		 $result2 = $this->adapter->query($sql2, Adapter::QUERY_MODE_EXECUTE)->toArray();
                 }
                 if($val['alternate2']){
	          $sql3 = "select first_name, last_name from provider_info where id=".$val['alternate2'];
	          $result3 = $this->adapter->query($sql3, Adapter::QUERY_MODE_EXECUTE)->toArray();
		 }
	           $result[$key]['refer_to_name']= $result1[0]['first_name'].' '.$result1[0]['last_name'];
		   $result[$key]['alternate1_name']= $result2[0]['first_name'].' '.$result2[0]['last_name'];
		   $result[$key]['alternate2_name']= $result3[0]['first_name'].' '.$result3[0]['last_name'];
          
		
	}
//print_r($result);die;
    return $result;
  }

     public function selectProAndLocation($docid){
   $query="select l.*,p.* from location_info l join provider_info p on l.id=p.location_info_id  where l.doc_id=$docid";
   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
   return $result; 
}
public function zipInsert($data){
		$state = $data['state'];
		$city = $data['city'];
		$zip = $data['zip'];
	 $select ="insert into zip_code (id, state, city,zip) values('','$state','$city','$zip')" ;  
  
    $result=$this->adapter->query($select,Adapter::QUERY_MODE_EXECUTE);
    return $result; 
	}
	public function searchbyzip($data){
	$query="SELECT state, city FROM zip_code WHERE zip ='".$data."'" ;
	$result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
	return $result; 
	}
/*            shashi start                  */
  public function  docTravelTypeChk($doc_id)
  {
   $query="select traveling_type from doctor_details where doc_id=$doc_id" ;
   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
   return $result;   
  }
 public function updateDoctorDetailsWhenNoLocation($doc_id,$traveltype){
   $qry = "update doctor_details set traveling_type='".$traveltype."' where doc_id=".$doc_id;
    $result=$this->adapter->query($qry, Adapter::QUERY_MODE_EXECUTE); 
    return $result;
 }
 public function updateDoctorDetailsLocation($doc_id,$legalbusinessname,$practice,$kind_of_practice,$traveltype)
   { 
  $qry = "update doctor_details set traveling_type='".$traveltype."' , kind_of_practice='".$kind_of_practice."',practice_name='".$practice."',leagal_business_name='".$legalbusinessname."' where doc_id=".$doc_id;
    $result=$this->adapter->query($qry, Adapter::QUERY_MODE_EXECUTE); 
    return $result;
  }
   public function doctorDetailsByIdForSettingLocation($docid){
      $sql = new Sql ($this->adapter);
     $select = $sql->select()->columns(
        array(
            'bank_account_number','bank_routing_number','acc_holder_name','legal_name','cc_number','cvc_no','creditcart_exp_month','creditcart_exp_year','practice_name','leagal_business_name','kind_of_practice','account_type','traveling_specialist','deposit_account_no','deposit_routing_no','deposit_acc_holder_name','traveling_type'
        ))->from('doctor_details')->where(array('doc_id'=>$docid));   
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
    return $result;
    } 
 public function insertLocationDetails($docid)
  {
   $sql =new Sql($this->adapter);
   $select= $sql->select()->from('location_info');
   $statement = $sql->prepareStatementForSqlObject($select);
   $result=$this->resultSetPrototype->initialize($statement->execute())->toArray(); 
  return $result;
  }
  public function addProviderInfo($docid,$fname,$lname,$dentist,$licensearray,$npi,$locid,$sex)
  { 
   $select ="insert into provider_info (doc_id, location_info_id, first_name, last_name, gender, licence_no, npi_no,dentist_type) values('$docid','$locid','$fname','$lname','$sex','$licensearray','$npi','$dentist')" ;  
    $result=$this->adapter->query($select,Adapter::QUERY_MODE_EXECUTE);
    return $result; 
  }
 public function insertDocLocation($doc_id,$data,$traveltype)
    {  
        $dq= "delete from location_info where doc_id=$doc_id";
        $detele=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE);
        foreach ($data['address'] as $key => $value) {
        $city= $data['city']->$key;
        $state= $data['state']->$key;
        $timing= $data['timing']->$key;
        $zip= $data['zip']->$key;
        $phone= $data['phone']->$key;
        $email= $data['email']->$key;
        $insertLocArray=array();
       $qry = "insert into location_info(doc_id, address, city, state, zip, phone, practice_email,doc_travel_type,timing) values('$doc_id','$value','$city','$state','$zip','$phone','$email','$traveltype','$timing')";
        $result=$this->adapter->query($qry, Adapter::QUERY_MODE_EXECUTE); 
      }
      return $insertLocArray;
   }
/*            shashi end                  */
 public function DoctorPlansService($doc_id){
      $sql = new Sql ($this->adapter);
      $select = $sql->select()
                 ->from('plans')->where(array('doc_id'=>$doc_id));
     $statement = $sql->prepareStatementForSqlObject ($select);
     $result = $this->resultSetPrototype->initialize ($statement->execute ())->toArray();
     foreach($result as $key=>$val){
			  $sql1 = new Sql ($this->adapter);
			  $select1 = $sql1->select()
						 ->from('plan_services')->where(array('plan_id'=>$val['plan_id']));
			 $statements = $sql->prepareStatementForSqlObject ($select1);
			 $results = $this->resultSetPrototype->initialize ($statements->execute ())->toArray();
			 $result[$key][$val['plan_name']]=$results;
	 }
    return $result;
    } 
    public function insertTermsAndCondPat($data){
	
	    $pat_id= $data['pat_id'];
        $plan_id= $data['plan_id'];
      
        $agree= $data['agree'];
        $understand= $data['understand'];
        $fname= $data['fname'];
        $lname= $data['lname'];
        $doc_id= $data['doc_id'];
        $insertLocArray=array();
        $qry = "insert into patient_term_condition(pat_id, plan_id, agree, understand, fname, lname, doc_id) values('$pat_id','$plan_id','$agree','$understand','$fname','$lname','$doc_id')";
        $result=$this->adapter->query($qry, Adapter::QUERY_MODE_EXECUTE); 
        return $result;
	
	}
	
  public function  prividerById($pro_id)
  {
   $query="select * from provider_info where id=".$pro_id;
   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
   return $result;   
  }
  public function editProviderAvatar($pro_id,$pro_avatar){
      $query = "update provider_info set `pro_avatar`='".$pro_avatar."' where `id`='".$pro_id."'";
		    $results = $this->adapter->query($query, Adapter::QUERY_MODE_EXECUTE);		
		  return $result;
    } 
   public function savedoctorbio($id,$bio){
		$update_qry = "update provider_info set bio='$bio' where id=$id"; 
		$result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE); 
		return $result;
    }
   public function listPatientsdetailsById($patid)
    {
     $sql = new Sql($this->adapter);
     $select=$sql->select()->from('patients')->where(array('patient_id'=>$patid));
     $statement = $sql->prepareStatementForSqlObject($select);
     $result=$this->resultSetPrototype->initialize($statement->execute())->toArray();
     return $result;
   } 

/*18/06/2016*/
	public function  prividerByDocId($docid)
	  {
	   $query="select id from provider_info where doc_id=$docid";
	   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
	   return $result;   
	  }
	  public function  isrefer($prov)
	  {
		$result = '';
		if($prov){  
	   $query="select count(*) as total from refer_patient where refer_to in($prov) and status=0";
	   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
		}
	   return $result;   
	  }
	  public function  refer($prov)
	  {
		$result = '';
		if($prov){  
	   //$query="select * from refer_patient where refer_to in($prov) and status=0";
	   $query="select r.*, p.patient_firstname,p.patient_lastname from refer_patient r join patients p on r.patient_id=p.patient_id where refer_to in($prov) and status=0";
	   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
		}
	   return $result;   
	  }
	  public function  referAccept($prov,$procedure)
	  {
		$result = '';
		if($prov){  
			$query="update refer_patient set status=1, procedure_code='$procedure' where ref_id=$prov";
			$result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE);			
		}
	   return;   
	  }
	  public function  referDecline($prov)
	  {
		$result = '';
		if($prov){  
			$query="update refer_patient set status=2 where ref_id=$prov";
			$result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE);
		}
	   return;   
	  }
	  public function insertAcceptreferral(){
	   $qry = "insert into patient_term_condition(refer_patient_id,) values('$pat_id','$plan_id','$agree','$understand','$fname','$lname','$doc_id')";
			$result2=$this->adapter->query($qry, Adapter::QUERY_MODE_EXECUTE); 
	  
	  } 
	  public function findRefer(){
		$query="select * from refer_patient where status=0";
	    $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
	    return $result;
	  }
	  public function insertalternate1($data){
		  $refby = $data['refer_by'];
		  $pat = $data['patient_id'];
		  $comments = $data['comments'];
		  $referto = $data['alternate1'];
		  $status = $data['status'];
		  $datetime = date('Y-m-d H:i:s');
		  $alternate1 = 0;
		  $alternate2 = $data['alternate2'];
		  $aptdate = $data['appointment_date'];
		  $apttime = $data['appointment_time'];
		  
		 $query="insert into refer_patient(patient_id,refer_by,comments,refer_to,status,refer_time,alternate1,alternate2,appointment_date,appointment_time) values('$pat','$refby','$comments','$referto','$status','$datetime','$alternate1','$alternate2','$aptdate','$apttime')";
	    $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE);
	    return $result;
	  }
	  public function insertalternate2($data){
		  $refby = $data['refer_by'];
		  $pat = $data['patient_id'];
		  $comments = $data['comments'];
		  $referto = $data['alternate2'];
		  $status = $data['status'];
		  $datetime = date('Y-m-d H:i:s');
		  $alternate1 = 0;
		  $alternate2 = 0;
		  $aptdate = $data['appointment_date'];
		  $apttime = $data['appointment_time'];
		$query="insert into refer_patient(patient_id,refer_by,comments,refer_to,status,refer_time,alternate1,alternate2,appointment_date,appointment_time) values('$pat','$refby','$comments','$referto','$status','$datetime','$alternate1','$alternate2','$aptdate','$apttime')";
	    $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE);
	    return $result;
	  }
	  
	  
	  public function changeReferStatusMissed($prov){
		$query="update refer_patient set status=4 where ref_id=$prov";
			$result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE);
	  } 
	  public function  referCall($prov)
	  {
		$result = '';
		if($prov){  
			$query="update refer_patient set status=3 where ref_id=$prov";
			$result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE);			
		}
	   return;   
	  }
	   public function  referByCall($prov)
	  {
		$result = '';
		if($prov){  
			
			$query="select r.*, p.patient_firstname,p.patient_lastname from refer_patient r join patients p on r.patient_id=p.patient_id where refer_to in($prov) and status=3";
	   //$query="select * from refer_patient where refer_to in($prov) and status=3";
	   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
		}
	   return $result;   
	  }
	  public function  isReferCall($prov)
	  {
		$result = '';
		if($prov){  
	   $query="select count(*) as total from refer_patient where refer_to in($prov) and status=3";
	   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
		}
	   return $result;   
	  }
	  public function  referAcceptByCall($prov,$procedure,$aptData,$apttimes)
	  {
		$result = '';
		if($prov){  
			$query="update refer_patient set status=1, procedure_code='$procedure', appointment_date='$aptData', appointment_time='$apttimes' where ref_id=$prov";
			$result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE);			
		}
	   return;   
	  }
    public function referralListingoForSpecilist($docid)
  {
$querystring="SELECT date_reffered, comments, appointment_date,appointment_time,status FROM refer_patient WHERE refer_to IN (SELECT id FROM provider_info WHERE doc_id = $docid);";
    $result =$this->adapter->query($querystring,Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
  }
   public function totalReferalCountToDoctor($docid)
  {
    $querystring="SELECT count(*) as total_receive FROM refer_patient WHERE refer_to IN (SELECT id FROM provider_info
WHERE doc_id = $docid)" ;
    $result =$this->adapter->query($querystring,Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
  } 
   public function totalReferalAcceptToDoctor($docid)
  {
    $querystring="SELECT count(*) as total_accept FROM refer_patient WHERE refer_to IN (SELECT id FROM provider_info
WHERE doc_id = $docid) and status=1" ;
    $result =$this->adapter->query($querystring,Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
  } 
   public function totalReferalMissedToDoctor($docid)
  {
    $querystring="SELECT count(*) as total_missed FROM refer_patient WHERE refer_to IN (SELECT id FROM provider_info
WHERE doc_id = $docid) and status=4" ;
    $result =$this->adapter->query($querystring,Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
  } 
   public function totalReferalDeclineToDoctor($docid)
  {
    $querystring="SELECT count(*) as total_decline FROM refer_patient WHERE refer_to IN (SELECT id FROM provider_info
WHERE doc_id = $docid) and status=2" ;
    $result =$this->adapter->query($querystring,Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
  } 
  public function totalReferalPendingToDoctor($docid)
  {
    $querystring="SELECT count(*) as total_pending FROM refer_patient WHERE refer_to IN (SELECT id FROM provider_info
WHERE doc_id = $docid) and status=0" ;
    $result =$this->adapter->query($querystring,Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
  } 
 public function deleteLocationByDocId($docid){
    $dq= "delete from location_info where doc_id=$docid";
    $detele=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE);
  }
  public function selectProviderDetailByDocIdLocZero($docid){
    $dq= "select * from provider_info where doc_id=$docid and location_info_id=0;";
    $result=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
    }
  public function getadalist(){
	 $query="select * from ada_list";
	$result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
	return $result;
  }
 public function ReferralsPatientListing($docid){
    $update_qry = "select p.patient_firstname,p.patient_lastname,r.* from patients p join refer_patient r on p.patient_id=r.patient_id
          where r.refer_to in(select id from provider_info where doc_id=$docid) and r.status=1";
          
    $result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE)->toArray();
   
    return $result;
  }
 public function updateDoctorLoginStepComplete($docid)
    {
    $dq= "update doctor_details set login_step_complete='1' where doc_id=$docid";
    $result=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE);
    return $result;
    }
  public function updateDocLocation($doc_id,$data,$traveltype)
    {  
      //  $dq= "delete from location_info where doc_id=$doc_id";
      //  $detele=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE);
        foreach ($data['address'] as $key => $value) {
        if($data['loc_id']->$key){
        $loc_id= $data['loc_id']->$key;
        $city= $data['city']->$key;
        $state= $data['state']->$key;
        $timing= $data['timing']->$key;
        $zip= $data['zip']->$key;
        $phone= $data['phone']->$key;
        $email= $data['email']->$key;
        $addr= $data['address']->$key;
        $insertLocArray=array();
        $qry = "update location_info set city='$city', state='$state', address='$addr' ,zip='$zip' ,phone='$phone' ,timing='$timing', practice_email='$email'  where id=$loc_id";
        }else{
        $city= $data['city']->$key;
        $state= $data['state']->$key;
        $timing= $data['timing']->$key;
        $zip= $data['zip']->$key;
        $phone= $data['phone']->$key;
        $email= $data['email']->$key;
        $insertLocArray=array();
        $qry = "insert into location_info(doc_id, address, city, state, zip, phone, practice_email,doc_travel_type,timing) values('$doc_id','$value','$city','$state','$zip','$phone','$email','$traveltype','$timing')";
        }
        $result=$this->adapter->query($qry, Adapter::QUERY_MODE_EXECUTE); 
      }

      return $insertLocArray;
   }
    public function deleteLocAndProviderByLocId($locid){
    $dq= "delete from provider_info where location_info_id=$locid";
    $detele=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE);
     $dq= "delete from location_info where id=$locid";
    $detele=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE);
  }
public function selectLocById($loc_id){
   $query="select * from location_info where id=".$loc_id ;
   $result =$this->adapter->query($query,Adapter::QUERY_MODE_EXECUTE)->toArray();
   return $result;
 }
 public function savepataddress($pat_id,$savepataddress){
		$update_qry = "update patients set patient_address='$savepataddress' where patient_id=$pat_id"; 
		$result = $this->adapter->query($update_qry, Adapter::QUERY_MODE_EXECUTE); 
		return $result;
    }
 public function selectProviderDocId($docid){
    $dq= "select * from provider_info where doc_id=$docid ";
    $result=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE)->toArray();
    return $result;
    }
 public function patientUpdateByDoc($data,$patid){
		$sql = new Sql($this->adapter);
		$select = $sql->update('patients')->set($data)->where(array('patient_id'=>$patid));
		$statement = $sql->prepareStatementForSqlObject($select);
		//echo $patid.'##';print_r($patid);exit;
		$result=$this->resultSetPrototype->initialize($statement->execute());
		return $result;
	
	}
 public function deleteReferPatient($refer_id){
    $dq= "delete from refer_patient where ref_id=$refer_id";
    $detele=$this->adapter->query($dq, Adapter::QUERY_MODE_EXECUTE);
  }	
}


