<?php
namespace Arii\JIDBundle\Controller\API\History;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

class JobsController extends Controller
{

    public function listAction($db='ojs_db', $limit=50)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        // par defaut 
        $start = $Filters['start'];
        $end   = $Filters['end'];
        $sort = 'DESC';
        // cas des archives sort=oldest
        if ($Filters['sort'] == 'oldest') {
            $start = null;            
            $sort  = 'ASC';         
        }
        elseif ($Filters['sort'] == 'latest') {
            $end = new \Datetime();
        }
        if (isset($Filters['retention'])) {
            $end = new \DateTime();
            $end->modify('- '.$Filters['retention'].' day');
        }        
        if (isset($Filters['limit'])) {
            $limit = $Filters['limit'];
        }
   
        $em = $this->getDoctrine()->getManager($db);        
        $history = $this->container->get('arii_jid.History');   
        $Runs = $history->Runs($em,$start,$end,$sort,$limit); 
        
        $data = $this->get('jms_serializer')->serialize($Runs, 'json', SerializationContext::create()->setGroups(array('list')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
}