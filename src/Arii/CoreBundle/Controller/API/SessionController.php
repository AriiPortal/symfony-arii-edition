<?php
namespace Arii\CoreBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as View;
use FOS\RestBundle\Controller\FOSRestController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use JMS\Serializer\SerializationContext;

class SessionController extends FOSRestController
{

    public function listAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $portal = $this->container->get('arii_core.portal'); 
        $Session = $portal->getUserInterface();
        
        switch ($Filters['outputFormat']) {
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Session, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Session, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function getAction($parameter)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $portal = $this->container->get('arii_core.portal'); 
        $Session = $portal->getUserInterface();
        
        if (!isset($Session[$parameter])) {
            $response = new Response();
            $response->setStatusCode(404);
            return $response;
        }
        
        switch ($Filters['outputFormat']) {
            case 'xml':
                $data = $this->get('jms_serializer')->serialize( $Session[$parameter], 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Session[$parameter], 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function updateAction(Request $request)
    {        
        $params = array();
        $content = $this->get("request")->getContent();
        
        if (empty($content)) {
            $response = new Response();        
            $response->setStatusCode(204);
            return $response;        
        }

        $params = json_decode($content, true); // 2nd param to get as array

        $portal = $this->container->get('arii_core.portal'); 
        $session = $portal->getUserInterface();

        if (isset($params['scheduler']))
            $portal->setJobScheduler($params['scheduler']);
        
        # Date de reference
        if (isset($params['refDate'])) {
            if ($params['refDate'] == 'now()') {
                  $Time = localtime(time(),true);
                  $session->setRefDate( sprintf("%04d-%02d-%02d %02d:%02d:%02d", $Time['tm_year']+1900, $Time['tm_mon']+1, $Time['tm_mday'], $Time['tm_hour'], $Time['tm_min'], $Time['tm_sec']) );       
            }
            else {
                $session->setRefDate($params['refDate']);                
            }
        }

        // Date
        if (isset($params['day'])) 
            $portal->setDay($params['day']);
        if (isset($params['month']))
            $portal->setMonth($params['month']);
        if (isset($params['year'])) 
            $portal->setYear($params['year']);

        if (isset($params['refPast'])) 
            $portal->setRefPast($params['refPast']);

        if (isset($params['refFuture'])) 
            $portal->setRefFuture($params['refFuture']);

        if (isset($params['refresh']))
            $portal->setRefresh($params['refresh']);

        // Attention 0 est un false
        if (isset($params['refreshPause']))
            $portal->setRefreshPause($params['refreshPause']);

        if (isset($params['env'])) 
            $portal->setEnv($params['env']);

        if (isset($params['app'])) 
            $portal->setApp($params['app']);

        if (isset($params['jobClass']))
            $portal->setJobClass($params['jobClass']);

        if (isset($params['tag'])) 
            $portal->setTag($params['tag']);

        if (isset($params['category'])) 
            $portal->setCategory($params['category']);
        
        if (isset($params['filter'])) {
            $portal->setUserFilterById($params['filter']);
        }
        
        if (isset($params['database'])) {
            $portal->setDatabase($params['database']);
        }
        
        if (isset($params['node'])) {
            $portal->setNode($params['node']);
        }
        
        if (isset($params['currentDir'])) {
            $session->set('currentDir',$params['currentDir']);
        }

        if (isset($params['state'])) {
            list($page, $id, $state) = explode('::',$params['state']); 
            $State = $params['state'];
            $State[$page][$id] = $state;
            $session->set( 'state', $State );
        }

        $currentRoute = $request->attributes->get('_route');
        $currentUrl = $this->get('router')->generate($currentRoute);
    
        $data = $this->get('jms_serializer')->serialize($params, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');        
        $response->headers->set('location', $currentUrl);        
        $response->setStatusCode(200);        
        return $response;        
    }

    public function initAction()
    {
        $portal = $this->container->get('arii_core.portal');         
        $portal->getUserInterface(true);

        $response = new Response();
        $response->setStatusCode(202);
        return $response;        
    }
}
