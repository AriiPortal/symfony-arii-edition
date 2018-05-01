<?php

namespace Arii\JOBBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiJOBBundle:Default:index.html.twig');
    }

    public function welcomeAction()
    {
        return $this->render('AriiJOBBundle:Default:welcome.html.twig');
    }
    
    public function readmeAction()
    {
        return $this->render('AriiJOBBundle:Default:readme.html.twig');
    }
    
    public function ribbonAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $this->render('AriiJOBBundle:Default:ribbon.json.twig',array(), $response);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJOBBundle:Default:toolbar.xml.twig', array(), $response );
    }

    public function fetchAllAction() {
        $workspace= $this->container->getParameter('workspace');
        $dir = $workspace."/Doc";
        $Docs = array();
        if ($dh = opendir($dir)) {
            while (($folder = readdir($dh)) !== false) {
                if (($p=strpos($folder,']'))>0) {
                    $Docs[$folder]['text'] = substr($folder,$p+1); 
                    $Docs[$folder]['icon'] = strtolower(substr($folder,1,$p-1)).'.png';
                }
            }
            closedir($dh);
            asort($Docs);
        }        
    }
    
    public function treeAction($folder='live',$filter=0) {
        $request = Request::createFromGlobals();
        if ($request->get('folder')!='') {
            $folder = $request->get('folder');
        }
        
        $sql = $this->container->get('arii_core.sql');                  
        $qry = $sql->Select(array('ID','FOLDER','PATH','FILE','TYPE'))
                .$sql->From(array('JOE_FILE'))
                .$sql->Where(array('FOLDER'=>$folder))
                .$sql->OrderBy(array('PATH','FILE'));
        
        $db = $this->container->get('arii_core.db');        
        $data = $db->Connector('grid');
        $res = $data->sql->query( $qry );
        $Info = $key_files = array();
        while ( $line = $data->sql->get_next($res) ) {
            $file = $line['PATH'].'/'.$line['FILE'];
            $Info[$file] = $line;
            $key_files[$file] = $file;
        }
        
        $tools = $this->container->get('arii_core.tools');
        $tree = $tools->explodeTree($key_files, "/");
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<tree id='0'>\n";
        $list .= $this->Folder2XML( $tree, '', $Info );
        $list .= "</tree>\n";
        $response->setContent( $list );
        return $response;
    }
    
}
