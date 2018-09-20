<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LinksController extends Controller{

    public function indexAction()
    {
        return $this->render('AriiACKBundle:Links:index.html.twig');
    }

    public function ContainsGridAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $Contains = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->findLinks($id,'CONTAINS');
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Contains,'name,description');
    }

    public function DependsGridAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $Depends = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->findLinks($id,'DEPENDS');
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Depends,'name,description');
    }
    
}

?>
