<?php
namespace ZF2AuthAcl\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZF2AuthAcl\Form\LoginForm;
use ZF2AuthAcl\Form\ForgetpasswrdForm;
use ZF2AuthAcl\Form\Filter\ForgetpasswrdFilter;
use ZF2AuthAcl\Form\Filter\LoginFilter;
use ZF2AuthAcl\Utility\UserPassword;
use Zend\Session\Container;
use Zend\Mail\Message;
use Zend\Mime;
use Zend\Mail\Transport\Sendmail ;
class IndexController extends AbstractActionController
{
    public function indexAction()
    {   
    $frontcheckid = $this->params()->fromRoute('id', 0); 
      if($frontcheckid==1){    /* for doctor login                               */
         $session = new Container('User'); 
         if ($session->offsetExists('userId'))
           {
                $this->redirect()->toRoute('logout');
           }
        $request = $this->getRequest(); 
        if($request->isPost()) {
          $post_date = file_get_contents("php://input");
          $data1 = json_decode($post_date);
          $data = (array) $data1;
           $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);
                $authService = $this->getServiceLocator()->get('AuthService');
                $userdetail  =  $this->getServiceLocator()->get("UserTable")->authenticateDoctor($data['userName'] ,$encyptPass);
                $resultid = $userdetail['0']['doc_id'] ;
                if (isset($resultid)) {
                    $userid = $userdetail[0]['doc_id'] ;
                    $session = new Container('User');
                    $session->offsetSet('email', $data['userName']);
                    $session->offsetSet('userId', $userdetail[0]['doc_id']);
                    $session->offsetSet('doc_speciality', $userdetail[0]['doc_speciality']);
                    $session->offsetSet('doc_firstname', $userdetail[0]['doc_firstname']);
                    $session->offsetSet('doc_lastname', $userdetail[0]['doc_lastname']);
                    $session->offsetSet('userdetail', $userdetail[0]);
                    if($userdetail[0]['doc_speciality']==3){
                    echo '3';exit;   // here 3 for doctor specility redirect.
                    }
                    echo '1';exit;   // here 1 for doctor details redirect.
                   // return $this->redirect()->toUrl('/dashboardfront/welcome');
                    // Redirect to page after successful login
            } else {
                    echo '0';exit;
                    $this->flashMessenger()->addMessage(array(
                        'error' => 'Sorry, an incorrect username and/or password has been entered.'
                  ));
                    // Redirect to page after login failure
                }
                return $this->redirect()->tourl('/admin/1');
                // Logic for login authentication
        }
        
        return new ViewModel(array('loginForm'=>$loginForm,'frontcheckid'=>$frontcheckid));
      }else{    /* else start here */
         $session = new Container('User'); 
         if ($session->offsetExists('userId'))
           {
                $this->redirect()->toRoute('logout');
           }
        $request = $this->getRequest();
        if($request->isPost()) {
            $data = $request->getPost();
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($data['password']);
                $authService = $this->getServiceLocator()->get('AuthService');
                $result  =  $this->getServiceLocator()->get("UserTable")->authenticate($data['email'] ,$encyptPass);
                $resultid = $result['0']['user_id'] ;
                if (isset($resultid)) {
                     $userDetails = $this->_getUserDetails(array(
                        'email' => $data['email']
                    ), array(
                        'user_id'
                    ));
                $userdetail =  $this->getServiceLocator()->get("UserTable")->getUserDetailByEmail($data['email']);
                    $userid = $userdetail[0]['user_id'] ;
                    $session = new Container('User');
                    $session->offsetSet('email', $data['email']);
                    $session->offsetSet('userId', $userdetail[0]['user_id']);
                    $session->offsetSet('roleId', $userDetails[0]['role_id']);
                    $session->offsetSet('fname', $userdetail[0]['fname']);
                    $session->offsetSet('lname', $userdetail[0]['lname']);
                    $session->offsetSet('roleName', $userDetails[0]['role_name']);
                   // die('here');
                    //  $category =  $this->getServiceLocator()->get("report_category")->getRolePermissionsuser($userid);
                  //  return $this->redirect()->toRoute('dashboard');
                  return $this->redirect()->toRoute('dashboard');
                    // Redirect to page after successful login
            } else {
                    $this->flashMessenger()->addMessage(array(
                        'error' => 'Sorry, an incorrect username and/or password has been entered.'
                  ));
                    // Redirect to page after login failure
                }
                return $this->redirect()->tourl('/admin');
                // Logic for login authentication
        }
     return new ViewModel(array('loginForm'=>$loginForm,'frontcheckid'=>$frontcheckid));
      }   /* else close here */
    }
  
  
    public function forgetpassAction(){
      $request = $this->getRequest();
            if($request->isPost()){
             $postdetail  = $request->getPost('reminder-email');
             $userdetail =  $this->getServiceLocator()->get("Dashboardfront\Model\DashboardfrontTable")->checkUseridByEmail($postdetail); // find doctor detail by email
            if($postdetail=''){
              $err = 1;  
            }else if(!isset($userdetail[0]['doc_email'])){
              $err = 2;
            }else{
                $newpass = substr(md5(substr(time(),5)),0,6);
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($newpass);  
                $updateDocPassword  =  $this->getServiceLocator()->get("UserTable")->updateDocPassword($userdetail[0]['doc_email'],$encyptPass); 
                $email = $userdetail[0]['doc_email'];
               $maillink = 'Hi '.$userdetail[0]['doc_firstname'].' '.$userdetail[0]['doc_lastname'].',<br />Your Password is reset and your new password is: '.$newpass.' <br />You can login by <a href="'.$_SERVER['SERVER_NAME'].'/admin/1" target="_blank">click here</a>';

                $config = $this->getServiceLocator()->get('config');
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
                        $message->setSubject("Your password is reset for dentalplansoftware.com");
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
                        $err = 3;
                     }
               } 
            return array('form' => $forgetpasswrdForm,'err'=>$err);
  }
   public function adminforgetpassAction(){
      $request = $this->getRequest();
            if($request->isPost()){
             $postdetail  = $request->getPost('reminder-email');
             $userdetail =  $this->getServiceLocator()->get("Dashboardfront\Model\DashboardfrontTable")->checkAdminUseridByEmail($postdetail); // find doctor detail by email
            if($postdetail=''){
              $err = 1;  
            }else if(!isset($userdetail[0]['email'])){
              $err = 2;
            }else{
                $newpass = substr(md5(substr(time(),5)),0,6);
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($newpass);  
                $updateDocPassword  =  $this->getServiceLocator()->get("UserTable")->updatePassword($userdetail[0]['email'],$encyptPass); 
                $email = $userdetail[0]['email'];
                $maillink = 'Hi Admin user,<br />Your Password is reset and your new password is: '.$newpass.' <br />You can login by <a href="'.$_SERVER['SERVER_NAME'].'/admin" target="_blank">click here</a>';
                       
                        /* code for mail start   */
                        $config = $this->getServiceLocator()->get('config');
                        $footer = "";
                        $html = new Mime\Part($maillink);
                        $html->type = Mime\Mime::TYPE_HTML;
                        $text = new Mime\Part($footer);
                        $text->type = Mime\Mime::TYPE_TEXT;
                        $maillink = new Mime\Message();
                        $maillink->setParts(array($html, $text));
                        $message = new \Zend\Mail\Message();
                        $message->setBody($maillink);
                        $message->setFrom($config['email_sender']['email']);
                        $message->setSubject("Your password is reset for dentalplansoftware.com");
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
                        $err = 3;
                     return $this->redirect()->toUrl('/adminforgetpass');
                     }
               } 
            return array('form' => $forgetpasswrdForm,'err'=>$err);
  }
   
    public function changeadminpassAction(){
      $request = $this->getRequest();
            if($request->isPost()){
             $postdetail  = $request->getPost('reminder-email');
             $userdetail =  $this->getServiceLocator()->get("Dashboardfront\Model\DashboardfrontTable")->checkAdminUseridByEmail($postdetail); // find doctor detail by email
            if($postdetail=''){
              $err = 1;  
            }else if(!isset($userdetail[0]['email'])){
              $err = 2;
            }else{
                $newpass = substr(md5(substr(time(),5)),0,6);
                $userPassword = new UserPassword();
                $encyptPass = $userPassword->create($newpass);  
                $updateDocPassword  =  $this->getServiceLocator()->get("UserTable")->updatePassword($userdetail[0]['email'],$encyptPass); 
                $email = 'admin@dentalplansoftware.com';
                $maillink = 'Hi Admin user,<br />Your Password is reset and your new password is: '.$newpass.' <br />You can login by <a href="/admin" target="_blank">click here</a>';
                   
                        /* code for mail start   */
                        $footer = "";
                        $html = new Mime\Part($maillink);
                        $html->type = Mime\Mime::TYPE_HTML;
                        $text = new Mime\Part($footer);
                        $text->type = Mime\Mime::TYPE_TEXT;
                        $maillink = new Mime\Message();
                        $maillink->setParts(array($html, $text));
                        $message = new \Zend\Mail\Message();
                        $message->setBody($maillink);
                        $message->setFrom($userdetail[0]['email']);
                        $message->setSubject("Your password is reset for dentalplansoftware.com");
                        $message->addTo($email) ;
                        $SmtpOptions =  new \Zend\Mail\Transport\SmtpOptions();
                        $SmtpOptions->setHost('smtp.gmail.com') 
                              ->setConnectionClass('login')
                              ->setName('smtp.gmail.com')
                              ->setConnectionConfig(array(
                               'username'=>'shashikant@tactionsoftware.com',
                               'password'=>'skant1990it',
                               'ssl'     =>'tls',
                        ));
                        $transport  =  new \Zend\Mail\Transport\Smtp($SmtpOptions) ;
                        $transport->send($message);
                        /* code for mail start   */
                        $err = 3;
                     return $this->redirect()->toUrl('/adminforgetpass');
                     }
               } 
            return array('form' => $forgetpasswrdForm,'err'=>$err);
  }
 public function registerAction()
  {     
         $request= $this->getRequest();
         if($request->isPost()){
          $tinno = $request->getPost('tinnoenter');
          if($tinno){
            return new ViewModel(array('tinno'=>$tinno));
          }else{
            $postdetail = $request->getPost();  
            $doc_username= $postdetail['login'];  
            $doc_speciality = $postdetail['registertype'];  
            $conf_passwrd = $postdetail['password'];
            $sequrity_code = $postdetail['securityval'];  
            $sequritycode_conf = $postdetail['sequritycode_conf'];
            $tinnumber = $postdetail['tinnumberatregistrstion']; 
            $userdetail =  $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->checkUserid($doc_username);
                  if(!isset($userdetail[0]['doc_username']))
                  {  
                    $userdetail =  $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->saveDoctor($doc_id,$doc_firstname,$doc_lastname,$doc_email,$doc_phone,$doc_zip,$doc_address,$doc_phone2,$doc_sex,$doc_speciality,$doc_license_no,$doc_contact_person,$doc_status,$doc_username,$conf_passwrd,$sequrity_code,$tinnumber);
                      setcookie('useridafterregistration', $userdetail) ;
                      if($doc_speciality==3)
                      return $this->redirect()->toUrl("/createspecilist");
                      else
                      return $this->redirect()->toUrl("/createaccount");
                  }else{ 
                     $exists=1 ;
                     return new ViewModel(array('exists'=>$exists));
                      } 
          }   // $tinno  else  close here. 
       }
         return new ViewModel(array('action'=>$action,'exists'=>$exists,'action1'=>$uuid));
  }
  public function addproviderinfoAction()
  {   
      $docid=$_COOKIE['useridafterregistration'];
      $post_date = file_get_contents("php://input");
      $data1 = json_decode($post_date);
      $postdetail = (array) $data1;
      $request = $this->getRequest();
      if ($request->isPost()) {
       $userdetail = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->deleteproviderByDocId($docid);
         foreach ($postdetail as $key => $value) {
          foreach ($value->first_name as $keys => $val) {
           $userdetail = $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->addProviderInfo($docid,$val,$value->last_name->$keys,$value->dentist->$keys,$value->license->$keys, $value->npi->$keys,$value->id,$value->group->$keys);
            }
          }
       }
      echo 'true';die; 
   }
  public function logoutAction()
   {
      $authService = $this->getServiceLocator()->get('AuthService');
      $session = new Container('User');
      $session->getManager()->destroy();
      $authService->clearIdentity();
      $this->flashMessenger()->addMessage(array(
                        'error' => 'You Logout Successfully.'));
      return $this->redirect()->toUrl('/');
    }
   public function tininsertAction()
    {
      return new ViewModel();
    }
   
  public function createaccountAction()
    {
      $States=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->showStates();
          //$arr = (object) array('id'=>$val);
          //$loclistfordoctor[$key]['options']= array($arr);
   
         $statejson=json_encode($States);
      return new ViewModel(array('statelist'=>$statejson));
    }
    public function submitBillingAction()
    {
      $post_date = file_get_contents("php://input");
      $data1 = json_decode($post_date);
      $data = (array) $data1;
      $docid=$_COOKIE['useridafterregistration']; 
      $result=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDocBilling($docid,$data); 
      return new ViewModel();
    }
    public function submitlocationAction()
    {
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
                           
      $kind_of_practice = $generalofficestatus.",".$specialityofficestatus.",".$generaldentalwithdiffspl ;
      $docid=$_COOKIE['useridafterregistration']; 
      if($addressobj){
      $cityobj   = $data['city']; 
      $zipobj   = $data['zip']; 
      $phoneobj   = $data['phone'];  
      $emailobj   = isset($data['email'])?$data['email']:'';
      $userdetail =  $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->updateDoctorDetailsLocation($docid,$legalbusinessname,$practice,$kind_of_practice);
      
      $resultinsertlocdetails= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->insertDocLocation($docid,$data);

      $loclistfordoctor= $this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->selectDocLocation($docid);
      }
      
      
        $val=1;
      if($loclistfordoctor){
        foreach ($loclistfordoctor as $key => $value) {
          $arr = (object) array('id'=>$val);
          $loclistfordoctor[$key]['options']= array($arr);
          # code...
        }
     /* '[{"id":"105","doc_id":"33","address":"addr1","city":"city1","state":"","zip":"221005","phone":"12121"
,"practice_email":"skant@gmail.com","options":[{"id":2},{"id":3}] }]' for one location */ 

  /* '[{"id":"115","doc_id":"33","address":"adr1","city":"city1","state":"","zip":"221005","phone":"212121","practice_email":"skant@gmail.com","options":[{"id":2},{"id":3}]},{"id":"116","doc_id":"33","address":"arr2","city":"city2","state":"","zip":"2232","phone":"3232","practice_email":"saknt@gmail.com","options":{"id":2}}]';  for multiple location */
      echo json_encode($loclistfordoctor);
       }else{
       //echo json_encode(array('id'=>1));
        echo '0';
       }
       die;
      return new ViewModel(array('loclistfordoctor'=>$loclistfordoctor)); 
    }
    private function _getUserDetails($where, $columns)
    {
        $userTable = $this->getServiceLocator()->get("UserTable");
        $users = $userTable->getUsers($where, $columns);
        return $users;
    }
  public function createspecilistAction()
    {
      $States=$this->getServiceLocator()->get('Dashboardfront\Model\DashboardfrontTable')->showStates();
          //$arr = (object) array('id'=>$val);
          //$loclistfordoctor[$key]['options']= array($arr);
   
         $statejson=json_encode($States);
      return new ViewModel(array('statelist'=>$statejson));
    }
}
