<?php
namespace Arii\ReportBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SnapshotsController extends Controller{

    public function indexAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();                
        return $this->render('AriiReportBundle:Snapshots:index.html.twig', $Filters);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiReportBundle:Snapshots:toolbar.xml.twig", array(), $response);
    }

    public function treeAction($path='Dashboards')
    {
        $portal = $this->container->get('arii_core.portal');
        $workspace = $portal->getWorkspace().'/Report';
        
        $folder = $this->container->get('arii_core.folder');
        
        $xml = "<?xml version='1.0' encoding='utf-8'?>";                
        $xml .= '<tree id="0">'; 
        $xml .= '<item id="'.$path.'" text="'.$path.'" im0="folder.gif" open="1">';    
        $xml .= $folder->TreeXML( $workspace, $path );
        $xml .= '</item>';
        $xml .= '</tree>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent($xml);
        return $response;
    }
    
    public function gridAction($path='Dashboards')
    {
        $request = Request::createFromGlobals();        
        if ($request->get('path') != '') {
            $path = $request->get('path');
        }

        $portal = $this->container->get('arii_core.portal');
        $workspace = $portal->getWorkspace().'/Report';

        // on garde en session
        $portal->setSessionVar('path',$path);
                
        $xml = "<?xml version='1.0' encoding='utf-8'?>";   
        $xml .= "<rows>\n";
        $xml .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';        
        $date = $this->container->get('arii_core.date');
        if ($dh = opendir($workspace.'/'.$path)) {
            while (($file = readdir($dh)) !== false) {                
                if (substr($file,0,1)=='.') continue;
                $ext = substr($file,-3);
                if ($ext != 'jpg') continue;
                $filename = $workspace.'/'.$path.'/'.$file;
                if (is_dir($filename)) continue;
                if (!$this->isUtf8($file))
                    $file = utf8_encode($file);
                $xml .= '<row id="'.$file.'">';
                $Info = stat($filename);
                $xml .= '<cell>'.substr($file,0,-4).'</cell>';
                $xml .= '<cell>'.$ext.'</cell>';
                $Time = localtime($Info[9],true);
                $xml .= '<cell>'.sprintf("%04d-%02d-%02d %02d:%02d:%02d",$Time['tm_year']+1900,$Time['tm_mon']+1,$Time['tm_mday'],$Time['tm_hour'],$Time['tm_min'],$Time['tm_sec']).'</cell>';
                $xml .= '<cell>'.$Info[7].'</cell>';
                $xml .= '</row>';
            }
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent($xml);
        return $response;
    }

    // a mutualiser dans un service
    public function viewAction()
    {
        $request = Request::createFromGlobals();
        $file = $request->query->get( 'file' );

        $portal = $this->container->get('arii_core.portal');
        $workspace = $portal->getWorkspace().'/Report';
        $path = $portal->getSessionVar('path');
        
        $filename = str_replace('//','/',$workspace.'/'.$path.'/'.$file );
        if ($this->isUtf8($filename))
            $filename = utf8_decode($filename);
        
        if (!($content = file_get_contents($filename)))
            throw new \Exception('File not found: '.$file);
        
        $response = new Response();
        $ext = strtolower(substr($file,-3));
        switch ($ext) {
            case 'png':
            case 'jpg':
                $type = 'image/'.$ext;
                break;
//            case 'xml':
//                $type = 'text/'.$ext;
//                break;
            case 'xls':
                if (!$this->isUtf8($content))
                    $content = utf8_encode($content);
                $response->headers->set('Content-type', 'application/vnd.ms-excel; charset=utf-8');
                $response->headers->set("Content-disposition", "attachment; filename=$file"); 
                $response->setContent($content);
                return $response;
                break;            
            default:
                if (!$this->isUtf8($content))
                    $content = utf8_encode($content);
                $type = 'text/plain';
        }
        $response->headers->set('Content-Type', $type);
        $response->setContent($content);
        return $response;
    }

    // A mettre en service
    function isUtf8($string) {
        if (function_exists("mb_check_encoding") && is_callable("mb_check_encoding")) {
            return mb_check_encoding($string, 'UTF8');
        }

        return preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )*$%xs', $string);

    }     
    
}
?>