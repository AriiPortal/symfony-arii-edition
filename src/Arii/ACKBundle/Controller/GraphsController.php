<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class GraphsController extends Controller{

    public function gridAction()
    {
        $Graphs = $this->getDoctrine()->getRepository('AriiACKBundle:Graph')->findAll();
        $Grid = [];
        foreach ($Graphs as $Graph) {
            array_push($Grid, [
                'id'     => $Graph->getId(),
                'name'   => $Graph->getName(),
                'title'  => $Graph->getTitle()
            ]);
        } 
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Grid,'name,title');
    }

    
}
?>
