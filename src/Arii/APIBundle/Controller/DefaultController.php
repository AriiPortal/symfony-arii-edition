<?php

namespace Arii\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class DefaultController extends Controller
{

    public function __construct( )
    {
    }

    public function indexAction($db)
    {
        return $this->render('AriiAPIBundle:Default:index.html.twig', [ 'db' => $db ]);
    }

    public function swaggerAction()
    {
        $portal = $this->container->get('arii_core.portal');
        return $this->render('AriiAPIBundle:Default:swagger.html.twig', [ 'db' => $portal->getDatabase() ]);
    }
    
    public function summaryAction($db)
    {
        $portal = $this->container->get('arii_core.portal');
        $Module = $portal->getModule('API');
        
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
        return $this->render('AriiAPIBundle:Default:summary.html.twig',array('module' => $Module, 'Repositories' => $Repositories, 'db_active' => $db_active ));
    }

 }
