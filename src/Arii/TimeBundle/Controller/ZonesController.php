<?php

namespace Arii\TimeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ZonesController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiTimeBundle:Zones:index.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiTimeBundle:Zones:toolbar.xml.twig',array(), $response );
    }
    
    public function selectAction() {        
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $request->getLocale();
        $Zones = $em->getRepository("AriiTimeBundle:Zones")->findZones();        
        
        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->select($Zones,'name,title');        
    }
    
    public function formAction() {
        $request = $this->getRequest();
        $id = $request->query->get( 'id' );
        
        $em = $this->getDoctrine()->getManager();
        $Zone = $em->getRepository("AriiTimeBundle:Zones")->find($id);
        $Country = $Zone->getCountry();
        if ($Country)
            $country_id = $Country->getId();
        else 
            $country_id = '';
        $Form = [
            'id' => $Zone->getId(),
            'name' => $Zone->getName(),
            'title' => $Zone->getTitle(),
            'description' => $Zone->getDescription(),
            'country_id' => $country_id,
            'latitude' => $Zone->getLatitude(),
            'longitude' => $Zone->getLongitude()
        ];
                
        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->form($Form);        
    }
    
    public function treeAction() {
        
        $portal = $this->container->get('arii_core.portal');
        
        $Zones = $this->getDoctrine()->getRepository('AriiTimeBundle:Zones')->findAll();
        $Tree = [];
        foreach ($Zones as $Zone) {           
            $Country = $Zone->getCountry();
            $country_code = $Country->getName();
            $country_title = $Country->getTitle();
            if (!isset($TreeCountry[$country_title])) {
                $Tree[$country_title]['code'] = $country_code;
                $Tree[$country_title]['zones'] = [];
            }
            array_push($Tree[$country_title]['zones'], [
                'id' =>    $Zone->getId(),
                'title' => ($Zone->getTitle()!=''?$Zone->getTitle():$Zone->getName())
            ]);
        }
        ksort($Tree);        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $xml = '<tree id="0">';        
        foreach ($Tree as $k=>$Country) {
            $xml .= '<item id="c:'.$Country['code'].'" text="'.$k.'" open="1">';
            $Zones = $Country['zones'];
            ksort($Zones);
            foreach ($Zones as $z=>$Zone) {
                $xml .= '<item id="'.$Zone['id'].'" text="'.$Zone['title'].'"/>';            
            }            
            $xml .= '</item>';            
        }
        $xml .= '</tree>';
        $response->setContent( $xml );
        return $response;        
    }

    public function saveAction() {
        $request = $this->getRequest();
        $id =$request->get( 'id' );
        
        // connexion base de données
        $em = $this->getDoctrine()->getManager();
        
        $zones = new \Arii\TimeBundle\Entity\Zones();
        if ($id!="") {
            $zones = $em->getRepository("AriiTimeBundle:Zones")->find($id);
        }
        
        $zones->setName ( $request->get( 'name' ) );
        $zones->setTitle( $request->get( 'title' ) );
        $zones->setDescription( $request->get( 'description' ) );
        
        // Le pays
        $country_id = $request->get( 'country_id' );
        $Country = $em->getRepository("AriiCoreBundle:Country")->find($country_id);
        $latitude = $request->get( 'latitude' );
        $longitude = $request->get( 'longitude' );
        if ($Country) {                        
            $zones->setCountry( $Country );
            if ($latitude=='')
                $latitude = $Country->getLatitude();
            if ($longitude=='')
                $longitude = $Country->getLongitude();
        }

        $zones->setLatitude( $latitude );
        $zones->setLongitude( $longitude );
        
        $em->persist($zones);
        $em->flush();
        
        print $this->get('translator')->trans('Zone saved');
        exit();
    }
    
    public function countriesAction()
    {
        $portal = $this->container->get('arii_core.portal');        
        $Countries = $portal->getCountries();

        // Construction de la liste (a mettre dans le Portal ?)
        $Select = [];
        foreach ($Countries as $k=>$Country)  {
            $name = $this->get('translator')->trans($k,[],'countries');
            $Select[$name]['title'] = $name;
            $Select[$name]['id'] = $Country['id'];
        }            
        ksort($Select);
        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->select($Select);
    }
    
}
