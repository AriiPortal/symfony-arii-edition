<?php

namespace Arii\ATSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

use JMS\Serializer\SerializationContext;

class ReportController extends Controller
{
    public function indexAction($db)
    {
        // La db est en parametre ?
        $request = Request::createFromGlobals();
            $portal = $this->container->get('arii_core.portal');
        if ($request->get('db')!='')
            $portal->setDatabaseByName($request->get('db'));
        if ($request->get('db_id')!='')
            $portal->setDatabaseById($request->get('db_id'));
        return $this->render('AriiATSBundle:Report:index.html.twig', [ 'db' => $db ]);
    }
    
    public function pivotAction($db)
    {
        // La db est en parametre ?
        $request = Request::createFromGlobals();
            $portal = $this->container->get('arii_core.portal');
        if ($request->get('db')!='')
            $portal->setDatabaseByName($request->get('db'));
        if ($request->get('db_id')!='')
            $portal->setDatabaseById($request->get('db_id'));
        
        return $this->render('AriiATSBundle:Report:pivot.html.twig', [ 'db' => $db ]);
    }

    public function jsonAction($db)
    {
        $em = $this->getDoctrine()->getManager($db);        
        $Status = $em->getRepository("AriiATSBundle:UjoJobst")->synchroJobst(0);

        // Complement
        $autosys = $this->container->get('arii_ats.autosys');
        foreach ($Status as $key => $data) {
            $Status[$key]['status']     = $autosys->Status($Status[$key]['status']);
            $Status[$key]['jobType']    = $autosys->jobType($Status[$key]['jobType']);
            $Status[$key]['lastStart']  = new \DateTime('@'.$Status[$key]['lastStart']);
            $Status[$key]['lastEnd']    = new \DateTime('@'.$Status[$key]['lastEnd']);
            $Status[$key]['nextStart']  = new \DateTime('@'.$Status[$key]['nextStart']);
            $Status[$key]['statusTime'] = new \DateTime('@'.$Status[$key]['statusTime']);
/*
            $Status[$key]['day'] = $Status[$key]['statusTime']->format('d');
            $Status[$key]['month'] = $Status[$key]['statusTime']->format('m');
            $Status[$key]['year'] = $Status[$key]['statusTime']->format('Y');
            $Status[$key]['hour'] = $Status[$key]['statusTime']->format('H');
            $Status[$key]['minute'] = $Status[$key]['statusTime']->format('i');
 */
        }
        $data = $this->get('jms_serializer')->serialize($Status, 'json', SerializationContext::create()->setGroups(array('list')));
        
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
}
