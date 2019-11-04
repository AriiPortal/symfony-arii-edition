<?php

namespace Arii\JIDBundle\Controller\API\Components;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ReposController extends Controller
{
    public function listAction($outputFormat, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        // Peu importe la configuration, on ne récupère que les informations fonctionnelle
        $portal = $this->container->get('arii_core.portal');
        $Databases = $portal->getDatabases();
        $Repos = [];
        foreach ($Databases as $k=>$v) {
            if (substr($k,0,6)=='ojs_db') {
                array_push($Repos, [
                    'id' => $v['id'],
                    'name' => $v['name'],
                    'title' => $v['title'],
                    'description' => $v['description']
                ]);
            }
        }

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlx':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Repos,'alarmTime,alarm,jobName,stateGrid,status,theUser,eventComment,nb,stateTime,state,firstTime,description','state');        
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Repos, 'xml');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Repos, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
}