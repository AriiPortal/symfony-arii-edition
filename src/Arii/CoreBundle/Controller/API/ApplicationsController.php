<?php

namespace Arii\CoreBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

use FOS\RestBundle\Controller\Annotations as Rest;
use Arii\CoreBundle\Entity\Application;

class ApplicationsController extends Controller
{

    public function listAction($outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $database = $this->container->get('arii_core.CRUD');
        $Applications = $database->Applications($Filter); 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Applications,'spoolerId,clusterMemberId,path,status,nextStartTime','status');
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Applications, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Applications, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

     /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/applications")
     */
    public function newAction(Request $request)
    {
        $Application = new Application();
        $Application
                ->setName($request->get('name'))
                ->setTitle($request->get('title'))
                ->setDescription($request->get('description'))
                ->setContact($request->get('contact'))
                ->setActive($request->get('isActive'));
                
        $em = $this->getDoctrine()->getManager();   
        $em->persist($Application);
        $em->flush();

        return $Application;   
    }

     /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Get("/applications/{id}")
     */
    public function getAction($appName, Request $request)
    {
        $em = $this->getDoctrine()->getManager();   
        
        $Application = $em->getRepository('AriiCoreBundle:Application')
                ->findBy([ 'name' => $appName ]);
        
        return $Application;   
    }
    
    public function updateAction(Request $request)
    {
        $Application = new Application();
        $Application
                ->setName($request->get('name'))
                ->setTitle($request->get('title'))
                ->setDescription($request->get('description'))
                ->setContact($request->get('contact'))
                ->setActive($request->get('isActive'));
                
        $em = $this->getDoctrine()->getManager();   
        $em->persist($Application);
        $em->flush();

        return $Application;   
    }

     /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/applications/{id}")
     */
    public function deleteAction($appId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();   
        
        $Application = $em->getRepository('AriiCoreBundle:Application')
                ->find($appId);
        $em->remove($Application);
        $em->flush();

        return $Application;   
    }
    
}