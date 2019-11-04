<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class BoxController extends Controller
{

    public function statusAction($repoId='ats_db',$jobId,$outputFormat )
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        // pour les urls sans accept
        if ($outputFormat!='')
            $Filters['outputFormat'] = substr($outputFormat,1);

        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state2');        
        $Status = $state->BoxStatus($em,$jobId,$Filters);
        
        switch ($Filters['outputFormat']) {
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Status,'alarmTime,alarm,jobName,stateGrid,status,theUser,eventComment,nb,stateTime,state,firstTime,description','state');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Status, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Status, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

/*  En javascript
 *  =============
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Task ID');
      data.addColumn('string', 'Task Name');
      data.addColumn('date', 'Start Date');
      data.addColumn('date', 'End Date');
      data.addColumn('number', 'Duration');
      data.addColumn('number', 'Percent Complete');
      data.addColumn('string', 'Dependencies');

      data.addRows([
        ['Research', 'Find sources',
         new Date(2015, 0, 1), new Date(2015, 0, 5), null,  100,  null],
        ['Write', 'Write paper',
         null, new Date(2015, 0, 9), daysToMilliseconds(3), 25, 'Research,Outline'],
        ['Cite', 'Create bibliography',
         null, new Date(2015, 0, 7), daysToMilliseconds(1), 20, 'Research'],
        ['Complete', 'Hand in paper',
         null, new Date(2015, 0, 10), daysToMilliseconds(1), 0, 'Cite,Write'],
        ['Outline', 'Outline paper',
         null, new Date(2015, 0, 6), daysToMilliseconds(1), 100, 'Research']
      ]);

 */    
    public function googleGanttAction($repoId='ats_db',$jobId,$outputFormat )
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        // pour les urls sans accept
        if ($outputFormat!='')
            $Filters['outputFormat'] = substr($outputFormat,1);

//        $em = $this->getDoctrine()->getManager($repoId);        
//        $state = $this->container->get('arii_ats.state2');        
// 
//       $Status = $state->BoxStatus($em,$jobId,$Filters);
        
        $Status = '{
  "cols": [
        {"id":"","label":"Task ID","pattern":"","type":"string"},
        {"id":"","label":"Task Name","pattern":"","type":"string"},
        {"id":"","label":"Status","pattern":"","type":"string"},
        {"id":"","label":"Start Date","pattern":"","type":"date"},
        {"id":"","label":"End Date","pattern":"","type":"date"},
        {"id":"","label":"Duration","pattern":"","type":"number"},
        {"id":"","label":"Percent Complete","pattern":"","type":"number"},
        {"id":"","label":"Dependencies","pattern":"","type":"string"}
      ],
  "rows": [
        {"c":[ {"v":"1"}, {"v":"eric"},{"v":"SUCCESS"}, {"v": "Date(2019, 0, 1, 10, 15)"},{"v":null},{"v":30000},{"v":100},{"v":null}]},
        {"c":[ {"v":"2"}, {"v":"eric"},{"v":"SUCCESS"}, {"v": "Date(2019, 0, 1, 10, 20)"},{"v":null},{"v":30000},{"v":50},{"v":"1"}]},
        {"c":[ {"v":"3"}, {"v":"eric"},{"v":"FAILURE"}, {"v": "Date(2019, 0, 1, 10, 30)"},{"v":null},{"v":30000},{"v":50},{"v":"2"}]}        
      ]
}';
        $response = new Response($Status);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
}