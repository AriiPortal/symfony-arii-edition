<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/*
 * Liens
 * 0: Aucune lien (lien existant mais désactivé)
 * 1: from CONTAINS to (to est dans from)
 * 2: from DEPENDS on (from depend de to)
 * 3: from IS IN to (from est dans to)
 * 4: from IMPACTS to (to depend de from)
 * 
 */
class LinksController extends Controller{

    public function indexAction()
    {
        return $this->render('AriiACKBundle:Links:index.html.twig',['probe' => 0]);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Links:toolbar.xml.twig", array(), $response);
    }
    
    public function formAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $Probe = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->find($id);
        
        $Obj['id']          = $Probe->getId();        
        $Obj['name']          = $Probe->getName();        
        $Obj['title']         = $Probe->getTitle();        
        $Obj['description']   = $Probe->getDescription();        
        $Obj['link_type']     = $Probe->getLinkType();
        $Obj['link_strength'] = $Probe->getLinkStrength();
        $Obj['updated']       = $Probe->getUpdated();        

        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->form($Obj);        
    }
    
    // Relie 2 sondes
    public function probesAction()
    {
        $request = Request::createFromGlobals();
        $source_id = $request->get('source_id');
        $target_id = $request->get('target_id');
        $action = $request->get('action');
        $type = $request->get('type');
        
        // une sonde ne peut pas etre liee a elle meme
        if ($source_id==$target_id)
            return new Response('n/a');
        
        $em = $this->getDoctrine()->getManager();
        $Source = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->find($source_id);
        if (!$Source) 
            new \Exception("Graph '$source_id' unknown !");
        $Target = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->find($target_id);
        if (!$target_id) 
            new \Exception("Graph '$source_id' unknown !");
        
        // Ca existe ?
        $Link = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->findOneBy([
            'obj_from' => $Source,
            'obj_to' => $Target
        ]);

        if ($action=='add') {
            if (!$Link) {
                $Link = new \Arii\ACKBundle\Entity\Link();
                $Link->setName($Source->getName().'#'.$Target->getName());
                if ($type==1)
                    $Link->setTitle($Source->getTitle().' ['.$Target->getTitle().']');
                else
                    $Link->setTitle($Source->getTitle().' -> '.$Target->getTitle());
                $Link->setDescription($Target->getDescription());
            }
            $Link->setObjFrom($Source);
            $Link->setObjTo($Target);
            $Link->setLinkType($type);
            $Link->setLinkStrength(1);
            
            $Link->setUpdated(new \DateTime());
            
            $em->persist($Link);
            $em->flush();

            return new Response('added');
        }
        else {
            if ($Link) {
                $em->remove($Link);
                $em->flush();
                return new Response('deleted');
            }
        }
        return new Response('nothing');        
    }
    
    public function gridAction()
    {
        $Links = $this->getDoctrine()->getRepository('AriiACKBundle:Link')->findAll();
        $Grid = [];
        foreach($Links as $Obj) {
            array_push($Grid, [
                'id'          => $Obj->getId(),
                'name'        => $Obj->getName(),
                'from'        => ($Obj->getObjFrom()!==null?$Obj->getObjFrom()->getTitle():''),
                'to'          => ($Obj->getObjTo()!==null?$Obj->getObjTo()->getTitle():''),
                'type'        => $this->ImgType($Obj->getLinkType())                
            ]);
        }
        $render = $this->container->get('arii_core.render');
        return $render->grid($Grid,'name,from,type,to');
    }

    private function ImgType($type) {
        switch($type) {
            case 1:
                return '/images/contains.png';
            case 2:
                return '/images/depends.png';
            default:
                return '/images/unknown.png';
        }
    }
    
    public function linksAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        $type = $request->get('type');

        $Grid = $this->Links($id,$type);

        $render = $this->container->get('arii_core.render');     
        return $render->grid($Grid,'name,description');
    }
    
    private function Links($id,$type=1)
    {   
        $Probe = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->find($id);
        if (!$Probe)
            throw new \Exception("Probe '$id' unknown !");
        switch ($type) {
            case 1:
                $Links = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->findBy(
                    [
                        "obj_from" => $Probe,
                        "link_type" => 1
                    ]
                );
                break;
            case 2:
                $Links = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->findBy(
                    [
                        "obj_from" => $Probe,
                        "link_type" => 2
                    ]
                );
                break;
            case 3:
                $Links = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->findBy(
                    [
                        "obj_to" => $Probe,
                        "link_type" => 1
                    ]
                );
                break;
            case 4:
                $Links = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->findBy(
                    [
                        "obj_to" => $Probe,
                        "link_type" => 2
                    ]
                );
                break;
            default:
                throw new \Exception("Type '$type' unknown !");
        }
        
        $Grid = [];
        foreach ($Links as $Link) {
            if ($type>2)
                $Object = $Link->getObjFrom();
            else
                $Object = $Link->getObjTo();
            array_push($Grid,
                    [
                        "id"   => $Object->getId(),
                        "name" => $Object->getName(),
                        "description" => $Object->getDescription()
                    ]
            );
        }
        return $Grid;
    }
    
    public function deleteAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $Link = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->find($id);      
        if ($Link) {
            $em = $this->getDoctrine()->getManager();       
            $em->remove($Link);
            $em->flush();
            return new Response("success");            
        }
        
        return new Response("?!");            
    }
    
    public function saveAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $Link = new \Arii\ACKBundle\Entity\Link();
        if($id!="" )
            $Link = $this->getDoctrine()->getRepository("AriiACKBundle:Link")->find($id);      
        
        $Link->setName($request->get('name'));
        $Link->setTitle($request->get('title'));
        $Link->setDescription($request->get('description'));

        $Link->setLinkType($request->get('link_type'));
        $Link->setLinkStrength($request->get('link_strength'));
        $Link->setUpdated(new \DateTime());
       
        // On ne change pas les noeuds
        $Link->setObjFrom($Link->getObjFrom());
        $Link->setObjTo($Link->getObjTo('link_type'));
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($Link);
        $em->flush();        

        return new Response("success");
    }
   
    public function stateAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        $state = $request->get('state');

        $event = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->find($id);      
        if( !$event )
            return new Response("$id ?");
        
        $event->setState($state);
        $event->setStateTime(new \DateTime());
        
        $em = $this->getDoctrine()->getManager();        
        $em->persist($event);
        $em->flush();        

        return new Response("success");
    }
    
}

?>
