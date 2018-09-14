<?php
namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class FilterController extends Controller
{
    public function indexAction()
    {
        $portal = $this->container->get('arii_core.portal');
        return $this->render('AriiCoreBundle:Filter:index.html.twig',array('Modules' => $portal->getModules()));        
    }

    public function ribbonAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        return $this->render('AriiCoreBundle:Filter:ribbon.json.twig',array(), $response );
    }

    public function listAction()
    {
        return $this->render('AriiCoreBundle:Filter:list.html.twig');
    }
    
    public function menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiCoreBundle:Filter:menu.xml.twig",array(),$response);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiCoreBundle:Filter:toolbar.xml.twig",array(),$response);
    }

    public function addAction()
    {
        return $this->render('AriiCoreBundle:Filter:add.html.twig');
    }

    public function gridAction()
    {
        $portal = $this->container->get('arii_core.portal');
        $Filters = $portal->getMyFilters();

        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->grid($Filters,'name,title,env,job_name,job_chain,trigger,status,type,owner');
    }
    
    public function saveAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        if ($id != '')
        {
            $filter = $this->getDoctrine()->getRepository("AriiCoreBundle:Filter")->find($id);
        } else
        {
            $filter = new \Arii\CoreBundle\Entity\Filter();
        }
        
        $filter->setName($request->get('name'));
        $filter->setTitle($request->get('title'));
        $filter->setDescription($request->get('description'));
        $filter->setSpooler($request->get('spooler'));
        $filter->setJobName($request->get('job'));
        $filter->setFolder($request->get('folder'));
        $filter->setJobChain($request->get('job_chain'));
        $filter->setTrigger($request->get('order_id'));
        $filter->setStatus($request->get('status'));
        
        // Appartenance forcée
        $filter->setFilterType(1);
        
        $portal = $this->container->get('arii_core.portal');
        
        $Owner = $this->getDoctrine()->getRepository("AriiUserBundle:User")->findOneBy( [ 'username' => $portal->getUsername() ]);
        if ($Owner)
            $filter->setOwner($Owner);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($filter);
        $em->flush();
        
        // Remise a zero de la session
        $portal = $this->container->get('arii_core.portal');        
        $portal->resetMyFilters();         
        
        return new Response("success");
    }
    
    public function deleteAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');   
        $filter = $this->getDoctrine()->getRepository("AriiCoreBundle:Filter")->find($id);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($filter);
        $em->flush();

        // Remise a zero de la session
        $portal = $this->container->get('arii_core.portal');        
        $portal->resetMyFilters(); 
        
        return new Response("success");
    }

    public function formAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $portal = $this->container->get('arii_core.portal');
        $Filter = $portal->getFilterById($id);
        if ($Filter['title']=='') $Filter['title']=$Filter['name'];
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<data>\n";
        foreach (array_keys($Filter) as $k) {
            $v = $Filter[$k];
            if ($v=='')
                $v='*';
            $list .= '<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
        }
        $list .= "</data>\n";
        $response->setContent( $list );
        return $response;        
    }

}


