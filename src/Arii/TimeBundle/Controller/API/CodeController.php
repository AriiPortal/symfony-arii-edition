<?php

namespace Arii\TimeBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;

class CodeController extends Controller
{

    public function listAction() {

        $code = $this->container->get('arii_time.code');        
        $Days = $code->Calendar('BM+2D');

        $data = $this->get('jms_serializer')->serialize($Days, 'json', SerializationContext::create()->setGroups(array('list')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;    
    }
}