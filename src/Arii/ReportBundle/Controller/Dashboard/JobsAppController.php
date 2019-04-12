<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobsAppController extends Controller
{
    public function indexAction()
    {
        $request = Request::createFromGlobals();
        $filter = $this->container->get('report.filter');
        list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get( 'env' ),
            $request->query->get( 'app' ),
            $request->query->get( 'dayPast' ),
            $request->query->get( 'day' ),
            $request->query->get( 'month' ),
            $request->query->get( 'year' )
        );
        
        return $this->render('AriiReportBundle:Dashboard\Jobs:app.html.twig', 
            array( 
                'appl' => $app,
                'env' => $env,
                'month' => $month,
                'day' => $day,
                'year' => $year,
                'dayPast' => $dayPast
                ) 
            );
    }


    public function gridAction($app='*')
    {    
        $request = Request::createFromGlobals();
        $filter = $this->container->get('report.filter');
        list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get( 'env' ),
            $request->query->get( 'app' ),
            -30,
            $request->query->get( 'day' ), 
            $request->query->get( 'month' ),
            $request->query->get( 'year' )
        );     

        $em = $this->getDoctrine()->getManager();
        if (($env == '*') and ($app=='*'))
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findAll();
        elseif ($app=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'env' => $env) , ['job_name' => 'ASC']);
        elseif ($env=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app) , ['job_name' => 'ASC'] );
        else 
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app, 'env' => $env) , ['job_name' => 'ASC'] );
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><rows>";
        foreach ($Jobs as $job) {
            
            $created = $job->getLastChange();
            if (!$created)
                $created = $job->getCreated();
            $status = 0;
            if ($created->format('m')==$month and $created->format('Y')==$year) {
                $status+=1;
            }
            $deleted = $job->getDeleted();
            if ($deleted and $deleted->format('m')==$month and $deleted->format('Y')==$year) {
                $status+=2;
            }
            switch ($status) {
                case '1':
                    $color='#ccebc5';
                    break;
                case '2':
                    $color='#fbb4ae';
                    break;
                case '3':
                    $color='#ffffcc';  
                    break;
            }
            if ($status>0) {
                $xml .= '<row id="'.$job->getId().'" style="background-color: '.$color.';">';
                $xml .= '<cell>'.$job->getJobName().'</cell>';
                $xml .= '<cell>'.$job->getEnv().'</cell>';
                $xml .= '<cell>'.$job->getJobType().'</cell>';
                $xml .= '<cell>'.$job->getJobClass().'</cell>';
                if ($job->getCreated())
                    $xml .= '<cell>'.$job->getCreated()->format('Y-m-d').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getLastChange())
                    $xml .= '<cell>'.$job->getLastChange()->format('Y-m-d').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getFirstExecution())
                    $xml .= '<cell>'.$job->getFirstExecution()->format('Y-m-d H:i:s').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getLastExecution())
                    $xml .= '<cell>'.$job->getLastExecution()->format('Y-m-d H:i:s').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getDeleted())
                    $xml .= '<cell>'.$job->getDeleted()->format('Y-m-d').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getUpdated())
                    $xml .= '<cell>'.$job->getUpdated()->format('Y-m-d').'</cell>';
                else
                    $xml .= '<cell/>';
                $xml .= '</row>';
            }
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
}

