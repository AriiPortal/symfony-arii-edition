<?php

namespace Arii\CoreBundle\Controller\API;

use Arii\CoreBundle\Entity\Oracle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use JMS\Serializer\SerializationContext;

use Doctrine\ORM\Query\ResultSetMapping;

class OracleController extends Controller
{
    public function listAction(Request $request)
    {   
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $fields = 'OSUSER,MACHINE,USERNAME,PROGRAM,PROCESS,STATUS,EVENT';
        $stmt = $this->getDoctrine()->getEntityManager($request->query->get('db'))
        ->getConnection()
        ->prepare('select '.$fields.',TO_CHAR(LOGON_TIME, \'YYYY-MM-DD HH24:MI:SS\') as LOGON_TIME from v$session order by LOGON_TIME desc');
        $stmt->execute();
        $Repos = $stmt->fetchAll();
        
        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $data = $this->container->get('arii_core.render'); 
                return $data->grid($Repos,"LOGON_TIME,$fields");        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Repos, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            case 'dhtmlxPie':
                // Aggregation
                $Agg = [];
                foreach ($Repos as $Repo) {
                    $username = $Repo['USERNAME'];
                    if ($username == '')
                        $username = 'oracle';
                    $id = $username;
                    if (!isset($Agg[$id])) {
                        $Agg[$id] = [
                            "id"    => $id,
                            "nb"    => 1
                        ];
                    }
                    else {
                        $Agg[$id]['nb']++;
                    }
                }
                $Pie = [];
                foreach ($Agg as $k=>$A ) {
                    array_push($Pie,$A);
                }
                sort($Pie);
                $data = $this->get('jms_serializer')->serialize($Pie, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
            default:
                $data = $this->get('jms_serializer')->serialize($Repos, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function getAction($repoId, Request $request)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $portal = $this->container->get('arii_core.portal'); 
        $Repo = $portal->getRepoByName($repoId);

        // Filtre par type ?
/*            
        $DB = $this->getDoctrine()->getRepository('AriiCoreBundle:Repo')->findOneBy(['name' => $repoId ]);
        $Repo = [];
        if ($DB)
            $Repo['name'] = $DB->getName();
        else
            $Repo['name'] = $repoId;
*/      
        // On complete avec les infos 
        $em = $this->getDoctrine()->getManager($repoId);
        $Params = $em->getConnection()->getParams();
        foreach (['driver','host','port','user','dbname','service','servicename','charset'] as $k) {
            if (isset($Params[$k])) 
                $Repo[$k] = $Params[$k];
        } 
        switch ($Filters['outputFormat']) {
            case 'dhtmlxForm':
                $data = $this->container->get('arii_core.render'); 
                return $data->form($Repo);        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Repo, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Repo, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
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