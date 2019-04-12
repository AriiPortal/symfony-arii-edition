<?php

namespace Arii\CoreBundle\Controller\API;

use Arii\CoreBundle\Entity\Repo;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use JMS\Serializer\SerializationContext;

class ReposController extends Controller
{
    public function listAction(Request $request)
    {
        // Filtre par type ?
        if ($request->get('type')) 
            $Repos = $this->getDoctrine()->getRepository('AriiCoreBundle:Repo')->findBy([ "type" => $request->get('type')], [ 'title' => 'ASC']);
        else 
            $Repos = $this->getDoctrine()->getRepository('AriiCoreBundle:Repo')->findBy([], [ 'title' => 'ASC']);
            
        $data = $this->get('jms_serializer')->serialize($Repos, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;    
    }

    public function getAction($repoId, Request $request)
    {
        // Filtre par type ?
        $DB = $this->getDoctrine()->getRepository('AriiCoreBundle:Repo')->findOneBy(['name' => $repoId ]);
        $Repo = [];
        if ($DB)
            $Repo['name'] = $DB->getName();
        else
            $Repo['name'] = $repoId;
        
        // On complete avec les infos 
        $em = $this->getDoctrine()->getManager($repoId);
        $Params = $em->getConnection()->getParams();
        foreach (['driver','host','port','user','dbname','service','servicename','charset'] as $k) {
            if (isset($Params[$k])) 
                $Repo[$k] = $Params[$k];
        } 
        $data = $this->get('jms_serializer')->serialize($Repo, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;    
    }
    
    /**
     * @Rest\Post("/repos")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("Repo", class="Arii\CoreBundle\Entity\Repo", converter="fos_rest.request_body")
     */    
    public function createAction(Repo $Repo)
    {      
        $em = $this->getDoctrine()->getManager();

        $em->persist($Repo);
        $em->flush();

        return $Repo;
    }

    public function updateAction($repoId, Request $request)
    {      
        $em = $this->getDoctrine()->getManager();
        
        if (is_int($repoId))
            $Repo = $this->getDoctrine()->getRepository('AriiCoreBundle:Repo')->find($repoId);
        else 
            $Repo =$this->getDoctrine()->getRepository('AriiCoreBundle:Repo')->findOneBy([ "name" => $repoId ]);
        if (!$Repo) {
            $response = new Response();
            $response->setStatusCode( '404' );
            return $response;
        }
  
        // pour un patch ou un put
        if ($request->get('name'))
            $Repo->setName        ($request->get('name'));
        if ($request->get('title'))
            $Repo->setTitle       ($request->get('title'));
        if ($request->get('description'))
            $Repo->setDescription ($request->get('description'));
        if ($request->get('type'))
            $Repo->setType        ($request->get('type'));
                
        $em->persist($Repo);
        $em->flush();
        
        $response = new Response();
        $response->setStatusCode( '204' );
        return $response;
    }    
}