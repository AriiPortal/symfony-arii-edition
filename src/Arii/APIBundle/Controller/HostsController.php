<?php
namespace Arii\APIBundle\Controller;

use \Arii\ACKBundle\Entity\Host;

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

class HostsController extends FOSRestController
{

    public function listAction()
    {
        $Hosts = $this->getDoctrine()->getRepository('AriiACKBundle:Host')->findAll();
        return $Hosts;
    }

    public function createAction(Request $request)
    {  
        $data = new Host();

        if(!empty($request->get('name')))
            $data->setName($request->get('name')); 
        else
            return new View("NULL VALUES ARE NOT ALLOWED FOR NAME", Response::HTTP_NOT_ACCEPTABLE); 
        if(!empty($request->get('title')))
            $data->setTitle($request->get('title')); 
        else
            return new View("NULL VALUES ARE NOT ALLOWED FOR TITLE", Response::HTTP_NOT_ACCEPTABLE); 
       
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        return new View("Host added Successfully", Response::HTTP_OK);
    }
    
    public function updateAction($id, Request $request)
    {
        if (is_int($id)) {
            $host = $this->getDoctrine()->getRepository('AriiACKBundle:Host')->find($id);
        }
        else {
            $host = $this->getDoctrine()->getRepository('AriiACKBundle:Host')->findOneBy( [ 'name' => $id ]);
        }
        if ($host === null) {
            return new View("host '$id' not found", Response::HTTP_NOT_FOUND);
        }
        
        if(!empty($request->get('title')))
            $host->setTitle($request->get('title'));
        if(!empty($request->get('description')))
            $host->setDescription($request->get('description'));
        if(!empty($request->get('host')))
            $host->setHost($request->get('host'));
        if(!empty($request->get('state')))
            $host->setState($request->get('state'));
        if(!empty($request->get('state_time')))
            $host->setStateTime($request->get('state_time'));
        if(!empty($request->get('state_information')))
            $host->setStateInformation($request->get('state_information'));
        if(!empty($request->get('ip_adress')))
            $host->setIpAdress($request->get('ip_adress'));
        if(!empty($request->get('port')))
            $host->setPort($request->get('port'));
        if(!empty($request->get('acknowledged')))
            $host->setAcknowledged($request->get('acknowledged'));
        if(!empty($request->get('acknowledgement_type')))
            $host->setAcknowledgementType($request->get('acknowledgement_type'));
        if(!empty($request->get('downtimes')))
            $host->setDowntimes($request->get('downtimes'));
        if(!empty($request->get('downtimes_info')))
            $host->setDowntimesInfo($request->get('downtimes_info'));
        if(!empty($request->get('downtimes_user')))
            $host->setDowntimesUser($request->get('downtimes_user'));

        $sn = $this->getDoctrine()->getManager();
        $sn->persist($host);
        $sn->flush();
        return new View("Host '$id' updated Successfully", Response::HTTP_OK); 
    }

    public function getAction($id)
    {
        if ($id*1>0) {
            $host = $this->getDoctrine()->getRepository('AriiACKBundle:Host')->find($id);
        }
        else {
            $host = $this->getDoctrine()->getRepository('AriiACKBundle:Host')->findOneBy( [ 'name' => $id ]);
        }
        if ($host === null) {
            return new View("host '$id' not found", Response::HTTP_NOT_FOUND);
        }
       return $host;
    }

    public function deleteAction($id)
    {
        if ($id*1>0) {
            $host = $this->getDoctrine()->getRepository('AriiACKBundle:Host')->find($id);
        }
        else {
            $host = $this->getDoctrine()->getRepository('AriiACKBundle:Host')->findOneBy( [ 'name' => $id ]);
        }
        if ($host === null) {
            return new View("host '$id' not found", Response::HTTP_NOT_FOUND);
        }

        $sn = $this->getDoctrine()->getManager();
        $sn->remove($host);
        $sn->flush();        
        return new View("Host '$id' deleted Successfully", Response::HTTP_OK); 
    }
       
}
