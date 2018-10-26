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

    public function DependsGridAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $Grid = $this->Links($id,1);

        $render = $this->container->get('arii_core.render');     
        return $render->grid($Grid,'name,description');
    }
    
    public function ContainsGridAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $Grid = $this->Links($id,2);
        
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Grid,'name,description');
    }

    private function Links($id,$type=1)
    {   
        $Links = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->findBy(
            [
                "obj_from" => $id,
                "link_type" => $type
            ]
        );
        $Grid = [];
        foreach ($Links as $Link) {
            $Object = $Link->getObjTo();
            array_push($Grid,
                    [
                        "id"   => $Object->getId(),
                        "name" => $Object->getName(),
                        "desc" => $Object->getDescription()
                    ]
            );
        }
        return $Grid;
    }
}

?>
