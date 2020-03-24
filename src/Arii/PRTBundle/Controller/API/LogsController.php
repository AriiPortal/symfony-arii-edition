<?php

namespace Arii\PRTBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class LogsController extends Controller
{
    public function listAction($hubName, $outputFormat, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        // a mettre en service
        // test du conteun du repertoire 
        $dir = 'U:\aiaprd\ServeurImpression\log';
        $files = scandir($dir);
        
        $Files = [];
        if ($handle = opendir($dir)) {            
            /* Ceci est la façon correcte de traverser un dossier. */
            while (false !== ($entry = readdir($handle))) {
                // Filtre sur le nom
                $path_parts = pathinfo($entry );
                if (isset($Filter['fileType']) and ($Filter['fileType']!=$path_parts['extension']))
                    continue;
                $stat = stat($dir.'/'.$entry);
                // Filtre sur les propriétés
                if (isset($Filter['fromDate']) and (strtotime($Filter['fromDate'])>=$stat['ctime']))
                    continue;
                
                if (isset($Filter['toDate']) and (strtotime($Filter['toDate'])<=$stat['ctime']))
                    continue;
               array_push($Files,
                    [
                        "name" => $path_parts['basename'],
/*                      'accessed'=>@date('Y-m-d H:i:s',$stat['atime']),
                        'modified'=>@date('Y-m-d H:i:s',$stat['mtime']),
*/                      'created'=>@date('Y-m-d H:i:s',$stat['ctime']),
                        'content' => utf8_encode(file_get_contents($dir.'/'.$entry))
                    ]
                );
            }
            closedir($handle);
        }

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Files,'alarmTime,alarm,jobName,stateGrid,status,theUser,eventComment,nb,stateTime,state,firstTime,description','state');        
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Files, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
}