<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends Controller
{

    public function updateAction()
    {
        $request = Request::createFromGlobals();
        $session = $this->container->get('arii_core.session');
        $portal = $this->container->get('arii_core.portal');

        if ($request->query->get( 'scheduler' ))
            $portal->setJobScheduler($request->query->get( 'scheduler' ));
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        if ($request->query->get( 'action' )== 'init') {
            $session->ForceInit();
           return $this->render('AriiCoreBundle:Session:result.html.twig',array(), $response );
        }
        
        # Date de reference
        if ($request->query->get( 'ref_date' )) {
            if ($request->query->get( 'ref_date' ) == 'now()') {
                  $Time = localtime(time(),true);
                  $session->setRefDate( sprintf("%04d-%02d-%02d %02d:%02d:%02d", $Time['tm_year']+1900, $Time['tm_mon']+1, $Time['tm_mday'], $Time['tm_hour'], $Time['tm_min'], $Time['tm_sec']) );       
            }
            else {
                $session->setRefDate( $request->query->get( 'ref_date' ) );                
            }
        }

        // Date
        if ($request->query->get( 'day' )) 
            $portal->setDay( $request->query->get( 'day' ) );
        if ($request->query->get( 'month' ))
            $portal->setMonth( $request->query->get( 'month' ) );
        if ($request->query->get( 'year' )) 
            $portal->setYear( $request->query->get( 'year' ) );

        if ($request->query->get( 'ref_past' )) 
            $portal->setRefPast( $request->query->get( 'ref_past' ) );

        if ($request->query->get( 'ref_future' )) 
            $portal->setRefFuture( $request->query->get( 'ref_future' ) );

        if ($request->query->get( 'refresh' ))
            $portal->setRefresh( $request->query->get( 'refresh' ) );

        // Attention 0 est un false
        if ($request->query->get( 'refresh_pause' )<>'')
            $portal->setRefreshPause( $request->query->get( 'refresh_pause' ) );

        if ($request->query->get( 'env' )) 
            $portal->setEnv( $request->query->get( 'env' ) );

        if ($request->query->get( 'app' )) 
            $portal->setApp( $request->query->get( 'app' ) );

        if ($request->query->get( 'job_class' )) 
            $portal->setJobClass( $request->query->get( 'job_class' ) );

        if ($request->query->get( 'tag' )) 
            $portal->setTag( $request->query->get( 'tag' ) );

        if ($request->query->get( 'category' )) 
            $portal->setCategory( $request->query->get( 'category' ) );
        
        if ($request->query->get( 'filter' )) {
            $portal->setUserFilterById( $request->query->get( 'filter' ) );
        }
        
        if ($request->query->get( 'database' )) {
            $db = $request->query->get( 'database' )-1;
            $Databases = $session->getDatabases();
            if (isset($Databases[$db])) {
                $session->setDatabase($Databases[$db]);
                throw new \Exception($Databases[$db]['name']);
            }
            else {
                throw new \Exception($db);
            }
        }
        
        if ($request->query->get( 'node' )) {
            $node = $request->query->get( 'node' );
            $portal->setNode($node);
        }
        
        if ($request->query->get( 'current_dir' )) {
            $session->set('current_dir',$request->query->get( 'current_dir' ));
        }

        if ($request->query->get( 'state' )) {
            list($page, $id, $state) = explode('::',$request->query->get( 'state' )); 
            $State = $session->get( 'state' );
            $State[$page][$id] = $state;
            $session->set( 'state', $State );
        }
        exit();
        return $this->render('AriiCoreBundle:Session:result.html.twig',array(), $response );
    }
    
     public function viewAction()
    {
        $session = $this->container->get('arii_core.session');
        //  print_r($session->get('database'));
        return $this->render('AriiCoreBundle:Session:view.html.twig' );
    }
   
}
