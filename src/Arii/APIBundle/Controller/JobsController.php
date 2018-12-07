<?php
namespace Arii\APIBundle\Controller;

use \Arii\JOBBundle\Entity\Job;

use Arii\CoreBundle\Exception\ResourceValidationException;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as View;
use FOS\RestBundle\Controller\FOSRestController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TimeInc\SwaggerBundle\Swagger\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

use JMS\Serializer\SerializationContext;

class JobsController extends FOSRestController
{

    public function listAction()
    {
        $Jobs = $this->getDoctrine()->getRepository('AriiJOBBundle:Job')->findAll();
        
        $data = $this->get('jms_serializer')->serialize($Jobs, 'json', SerializationContext::create()->setGroups(array('list')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

 
    public function createAction(Request $request)
    {  
        $data = new Job;
        $name = $request->get('name');
        if(empty($name))
        {
          return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE); 
        } 
       $data->setName($name);
       $em = $this->getDoctrine()->getManager();
       $em->persist($data);
       $em->flush();
        return new View("User Added Successfully", Response::HTTP_OK);
    }
    
    public function updateAction($id)
    {
        $data = new Job;
        $name = $request->get('name');
        $sn = $this->getDoctrine()->getManager();
        
        $user = $this->getDoctrine()->getRepository('AriiJOBBundle:Job')->find($id);
        if (empty($user)) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        } 
        if(!empty($name)) {
         $user->setName($name);
         $sn->flush();
         return new View("User Name Updated Successfully", Response::HTTP_OK); 
        }
        
        return new View("User name or role cannot be empty", Response::HTTP_NOT_ACCEPTABLE);     
    }


    public function getAction($id)
    {
        $singleresult = $this->getDoctrine()->getRepository('AriiJOBBundle:Job')->find($id);
         if ($singleresult === null) {
         return new View("job not found", Response::HTTP_NOT_FOUND);
         }
       return $singleresult;
    }

    public function deleteAction($id)
    {
        $Alarm = $this->getDoctrine()->getRepository("AriiJOBBundle:Job")->find($id);
        return $Alarm;        
    }
        
}
