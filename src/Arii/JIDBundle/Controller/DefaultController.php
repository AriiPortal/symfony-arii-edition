<?php

namespace Arii\JIDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class DefaultController extends Controller
{
    protected $images;
    protected $TZLocal;
    protected $TZSpooler;
    protected $TZOffset;
    protected $CurrentDate;

    public function __construct( )
    {
        $request = Request::createFromGlobals();
        $this->images = $request->getUriForPath('/../arii/images/wa');

        $this->CurrentDate = date('Y-m-d');
    }

    public function indexAction()
    {
        $portal = $this->container->get('arii_core.portal');
        return $this->render('AriiJIDBundle:Default:index.html.twig', [ 'db' => $portal->getDatabase() ]);
    }

    public function swaggerAction()
    {
        $portal = $this->container->get('arii_core.portal');
        return $this->render('AriiJIDBundle:Default:swagger.html.twig', [ 'db' => $portal->getDatabase() ]);
    }
    
    public function summaryAction($db)
    {
        $portal = $this->container->get('arii_core.portal');
        $Module = $portal->getModule('JID');
        
        // On recupère les requetes
        $yaml = new Parser();
        $lang = $this->getRequest()->getLocale();

        $basedir = $this->getBaseDir();
        $Requests = array();
        if ($dh = @opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file,-4) == '.yml') {
                    $content = file_get_contents("$basedir/$file");
                    $v = $yaml->parse($content);
                    $v['id']=substr($file,0,strlen($file)-4);
                    if (!isset($v['icon'])) $v['icon']='cross';
                    if (!isset($v['title'])) $v['title']='?';
                    array_push($Requests, $v);
                }
            }
        }
        
        // Base de donnees courante ?
        // passée en variable        
/*
        $request = Request::createFromGlobals();
            $portal = $this->container->get('arii_core.portal');
        if ($request->get('db')!='')
            $portal->setDatabaseByName($request->get('db'));
        if ($request->get('db_id')!='')
            $portal->setDatabaseById($request->get('db_id'));

        // on reteste (c'est en session)
        $Database = $portal->getDatabase();
        if (isset($Database['name']))
            $db = $Database['name'];
        else
            $db = "?";
*/
        // base de données
        $Nodes=$portal->getNodesBy('vendor', 'ojs');
  
        // on recrée un arbre avec référentiel 
        $db_active = '';
        $Repositories = array();
        foreach ($Nodes as $Node) {
            foreach ($Node['Connections'] as $Connection) {
                if ($Connection['domain']!='database') continue;
                $name = $Connection['name'];
                if (!isset($Repositories[$name])) {
                    $Repositories[$name] = $Connection;
                    // si la base est dans la liste, elle est validée
                    if ($Connection['name']==$db) $db_active=$db;                
                }
            }
        }
        // si la base n'est pas dans la liste, on reset ?
        return $this->render('AriiJIDBundle:Default:summary.html.twig',array('module' => $Module, 'Repositories' => $Repositories, 'db_active' => $db_active ));
    }

    public function nodesAction()
    {        
        // On recupere la liste des spoolers, donc des nodes 'OJS'
        $portal = $this->container->get('arii_core.portal');
        $Nodes=$portal->getNodesBy('vendor', 'ojs');
  
        $Result = array();
        foreach ($Nodes as $Node) {
            foreach ($Node['Connections'] as $Connection) {
                if ($Connection['domain']!='database') continue;
                $name = $Node['title'];
                $Result[$name] = array(
                    'id'=> $Node['id'], 
                    'db' => $Connection['title'], 
                    'db_id' => $Connection['id'],
                    'db_name' => $Connection['name'] );
            }
        }
        asort($Result);
        // on recrée un arbre avec référentiel 
        $data = "<?xml version='1.0' encoding='utf-8' ?>";
        $data .= "<rows>";
        foreach ($Result as $name => $Info) {
            $data .= '<row id="'.$Info['id'].'">';
            $data .= '<cell>'.$name.'</cell>';
            $data .= '<cell>'.$Info['db'].'</cell>';
            $data .= '<cell>'.$Info['db_id'].'</cell>';
            $data .= "</row>";
        }
        $data .= "</rows>";
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $data );
        return $response;
    }
    
    public function treeAction()
    {        
        // On recupere la liste des spoolers, donc des nodes 'OJS'
        $portal = $this->container->get('arii_core.portal');
        $Nodes=$portal->getNodesBy('vendor', 'ojs');
  
        // on recrée un arbre avec référentiel     
        $Databases = array();
        foreach ($Nodes as $Node) {
            foreach ($Node['Connections'] as $Connection) {
                if ($Connection['domain']!='database') continue;  
                
                // Sinon c'est une base de données
                $title = $Connection['title'];                
                if (!isset($Databases[$title])) {
                    // On cree la nouvelle entree de base
                    $Databases[$title] = [
                        'name' => $Connection['name'],
                        'spoolers' => []  
                    ];
                }
                array_push($Databases[$title]['spoolers'], array( 
                    'name'  => $Node['name'],
                    'title' => $Node['title']) 
                );
            }
        }
        ksort($Databases);
        $tree = '<tree id="0">';
        foreach ($Databases as $db=>$DB) {
            $tree .= '<item id="'.$DB['name'].'" text="'.$db.'" im0="database.png" im1="database.png" im2="database.png" open="1">'; 
            foreach ($DB['spoolers'] as $k => $Node ) {
                $tree .= '<item id="s.'.$Node['name'].'" text="'.$Node['title'].'" im0="spooler.png"/>';          
            }
            $tree .= '</item>';
        }        
        $tree .= '</tree>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $tree );
        return $response;        
    }

    public function ribbonAction()
    {
        // On recupère les requetes
        $yaml = new Parser();
        $lang = $this->getRequest()->getLocale();
        $basedir = $this->getBaseDir();

        $Requests = array();
        if ($dh = @opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file,-4) == '.yml') {
                    $content = file_get_contents("$basedir/$file");
                    $v = $yaml->parse($content);
                    $v['id']=substr($file,0,strlen($file)-4);
                    if (!isset($v['icon'])) $v['icon']='cross';
                    if (!isset($v['title'])) $v['title']='?';
                    array_push($Requests, $v);
                }
            }
        }

        // Ajout des filtres
        $portal = $this->container->get('arii_core.portal');
        $Filters = $portal->getUserFilters();
        $MyFilters = [];
        foreach ($Filters as $k=>$Filter) {
            $id = $Filter['id'];
            $title = $Filter['title'];
            $MyFilters[$id] = $title;
        }
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');        
        return $this->render('AriiJIDBundle:Default:ribbon.json.twig',array( 'Requests' => $Requests, 'Filters' => $MyFilters ), $response );
    }

    public function readmeAction()
    {
        return $this->render('AriiJIDBundle:Default:readme.html.twig');
    }

    public function lastAction()
    {
        return $this->render('AriiJIDBundle:Default:activities.html.twig' );
    }
    
    public function historyAction()
    {
        return $this->render('AriiJIDBundle:Default:history.html.twig' );
    }

    public function history_pieAction()
    {
        return $this->render('AriiJIDBundle:Default:history_pie.html.twig' );
    }

    public function history_timelineAction()
    {
        return $this->render('AriiJIDBundle:Default:history_timeline.html.twig');
    }

    public function pie_chartAction()
    {
        $request = $here = $this->getRequest()->getPathInfo();
        if (strpos($request,"/orders"))
            return $this->render('AriiJIDBundle:Sidebar:pie_chart_orders.html.twig');
        return $this->render('AriiJIDBundle:Sidebar:pie_chart.html.twig');
    }

    public function job_infoAction()
    {
        $request = $here = $this->getRequest()->getPathInfo();
        if (strpos($request,"/orders"))
            return $this->render('AriiJIDBundle:Sidebar:job_info_orders.html.twig');
        return $this->render('AriiJIDBundle:Sidebar:job_info.html.twig');
    }

    /* Donne le répertoire de travail en fonction de la langue et de l'utilisiation */
    private function getBaseDir() {
        $lang = $this->getRequest()->getLocale();
        $portal = $this->container->get('arii_core.portal');
        return $portal->getWorkspace().'/JID/Requests/'.$lang;        
    }    
}
