<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarsController extends Controller
{
    public function indexAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        
        return $this->render('AriiReportBundle:Dashboard\Calendars:index.html.twig', $Filters);
    }
 
    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiReportBundle:Dashboard\Calendars:toolbar.xml.twig',array(), $response);
    }
    
    public function daysAction() {
        $request = Request::createFromGlobals();
        $cals = $request->get( 'cals' );

        $em = $this->getDoctrine()->getManager(); 
        $Days = $em->getRepository("AriiReportBundle:CAL")->findAll();

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<data>\n";
        $Cals = array();
        // plus rapide que le in_array
        foreach (explode(",",$cals) as $c) {
            $Cals[$c]=1;
        }        
        foreach ($Days as $Day)  {
            $name = $Day->getName();
            if (($cals=='*') or (isset($Cals[$name])))            
                $list .= '<event>
        <text><![CDATA['.$name.']]></text>
        <start_date>'.$Day->getDay()->format('Y-m-d 00:00').'</start_date>
        <end_date>'.$Day->getDay()->format('Y-m-d 23:59').'</end_date>
    </event>';
        }
        $list .= "</data>\n";
        $response->setContent( $list );
        return $response;        
    }    

    public function gridAction() {
        $request = Request::createFromGlobals();
        $cals = $request->query->get( 'cals' );

        $em = $this->getDoctrine()->getManager(); 
        $now =  new \DateTime();
        $now->setTime(0,0,0);
        
        $Days = $em->getRepository("AriiReportBundle:CAL")->findNextStarts($now);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        $Cals = array();
        // plus rapide que le in_array
        foreach (explode(",",$cals) as $c) {
            $Cals[$c]=1;
        }
        
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";        
        foreach ($Days as $Day)  {
            $name = $Day['name'];
            if (($cals=='*') or (isset($Cals[$name])))
                $list .= '<row id="'.$name.'"><cell><![CDATA['.$name.']]></cell><cell><![CDATA['.substr($Day['next'],0,10).']]></cell></row>';
        }
        $list .= "</rows>\n";
        $response->setContent( $list );
        return $response;        
    }        
}

