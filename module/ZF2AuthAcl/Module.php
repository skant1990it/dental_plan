<?php
namespace ZF2AuthAcl;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\Adapter\DbTable as DbAuthAdapter;
use Zend\Session\Container;
use ZF2AuthAcl\Model\User;
use ZF2AuthAcl\Model\UserRole;
use ZF2AuthAcl\Model\PermissionTable;
use ZF2AuthAcl\Model\ResourceTable;
use ZF2AuthAcl\Model\RolePermissionTable;
use Zend\Authentication\AuthenticationService;
use ZF2AuthAcl\Model\Role;
use ZF2AuthAcl\Utility\Acl;

class Module
{

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'boforeDispatch'
        ), 100);
    }

    function boforeDispatch(MvcEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $target = $event->getTarget();
        
        $whiteList = array(
            'ZF2AuthAcl\Controller\Index-index',
            'ZF2AuthAcl\Controller\Index-logout',
            'ZF2AuthAcl\Controller\Index-forgetpass',
            'ZF2AuthAcl\Controller\Index-passwordenter',
            'ZF2AuthAcl\Controller\Index-ragistrationpassword',   
            'ZF2AuthAcl\Controller\Index-adminforgetpass',
            'ZF2AuthAcl\Controller\Index-register',
            'ZF2AuthAcl\Controller\Index-submitbilling',
            'ZF2AuthAcl\Controller\Index-createaccount',
            'ZF2AuthAcl\Controller\Index-createspecilist',
            'ZF2AuthAcl\Controller\Index-submitlocation',
            'ZF2AuthAcl\Controller\Index-addproviderinfo',
            'Dashboardfront\Controller\Dashboardfront-index',   
            'Dashboardfront\Controller\Dashboardfront-aboutus',
            'Dashboardfront\Controller\Dashboardfront-features',
            'Dashboardfront\Controller\Dashboardfront-pricing',
            'Dashboardfront\Controller\Dashboardfront-contact',  
            'Dashboardfront\Controller\Dashboardfront-thanks', 
            'Dashboardfront\Controller\Dashboardfront-suscribe',
            'Dashboardfront\Controller\Dashboardfront-privacypolicy', 
            'Dashboardfront\Controller\Dashboardfront-termscondition',
            'Dashboardfront\Controller\Dashboardfront-riskreferrals',
            'Dashboardfront\Controller\Dashboardfront-doctorccpay',
            'Dashboardfront\Controller\Dashboardfront-doctordashboard', 
            'ZF2AuthAcl\Controller\Index-tininsert',   
            'Dashboardfront\Controller\Dashboardfront-workwithus',
	    'ZF2AuthAcl\Controller\Index-searchzip',
	    'Dashboardfront\Controller\Dashboardfront-referralalert',                                           
        );
        
        $requestUri = $request->getRequestUri();
        $controller = $event->getRouteMatch()->getParam('controller');
        
        $action = $event->getRouteMatch()->getParam('action');
        
        $requestedResourse = $controller . "-" . $action;
        
        $session = new Container('User');
        
        if ($session->offsetExists('email')) {
            if ($requestedResourse == 'ZF2AuthAcl\Controller\Index-index' || in_array($requestedResourse, $whiteList)) {
                $url = '/';
                $response->setHeaders($response->getHeaders()
                    ->addHeaderLine('Location', $url));
                $response->setStatusCode(302);
            } else {
                $serviceManager = $event->getApplication()->getServiceManager();
                $userRole = $session->offsetGet('roleName');
                $acl = $serviceManager->get('Acl');
                $acl->initAcl();
                $status = $acl->isAccessAllowed($userRole, $controller, $action);
                if (! $status) {
                    die('Permission denied');
                }
            }
        } else {
            if ($requestedResourse != 'ZF2AuthAcl\Controller\Index-index' && ! in_array($requestedResourse, $whiteList)) {
                $url = '/logout';
                $response->setHeaders($response->getHeaders()
                    ->addHeaderLine('Location', $url));
                $response->setStatusCode(302);
            }
            if ($requestedResourse != 'Dashboardfront\Controller\Dashboardfront-index' && ! in_array($requestedResourse, $whiteList)) {
                $url = '/logout';
                $response->setHeaders($response->getHeaders()
                    ->addHeaderLine('Location', $url));
                $response->setStatusCode(302);
            }
            $response->sendHeaders();
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'AuthService' => function ($serviceManager)
                {
                    $adapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $dbAuthAdapter = new DbAuthAdapter($adapter, 'users', 'email', 'password');
                    $auth = new AuthenticationService();
                    $auth->setAdapter($dbAuthAdapter);
                    return $auth;
                },
                'Acl' => function ($serviceManager)
                {
                    return new Acl();
                },
                'UserTable' => function ($serviceManager)
                {
                    return new User($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'RoleTable' => function ($serviceManager)
                {
                    return new Role($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'UserRoleTable' => function ($serviceManager)
                {
                    return new UserRole($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'PermissionTable' => function ($serviceManager)
                {
                    return new PermissionTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'ResourceTable' => function ($serviceManager)
                {
                    return new ResourceTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'RolePermissionTable' => function ($serviceManager)
                {
                    return new RolePermissionTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                }
            )
        );
    }
}
