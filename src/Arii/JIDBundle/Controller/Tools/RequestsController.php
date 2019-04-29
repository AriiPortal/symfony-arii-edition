<?php

namespace Arii\JIDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class RequestsController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiJIDBundle:Requests:index.html.twig');
    }
    
    public function treeAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<tree id="0">
                    <item id="runtimes" text="Runtimes"/>
                 </tree>';

        $response->setContent( $list );
        return $response;        
    }

    public function listAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<rows>';
        
        $yaml = new Parser();
        $basedir = $this->getBaseDir();
        
        if ($dh = @opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file,-4) == '.yml') {
                    $content = file_get_contents("$basedir/$file");
                    $v = $yaml->parse($content);
                    $list .= '<row id="'.substr($file,0,strlen($file)-4).'"><cell>'.$v['title'].'</cell></row>';
                }
            }
        }
        $list .= '</rows>';

        $response->setContent( $list );
        return $response;        
    }
    
    // Temps d'exécution trop long
    public function summaryAction()
    {
        $basedir = $this->getBaseDir();

        $yaml = new Parser();
        $value['title'] = $this->get('translator')->trans('Summary');
        $value['description'] = $this->get('translator')->trans('List of requests');
        $value['columns'] = array(
            $this->get('translator')->trans('Title'),
            $this->get('translator')->trans('Description') );
        
        $nb=0;
        if ($dh = @opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file,-4)=='.yml') {
                    $content = file_get_contents("$basedir/$file");
                    $v = $yaml->parse($content);
                    $nb++;
                    $value['line'][$nb] = array($v['title'],$v['description']);
                }
            }
        }
        $value['count'] = $nb;
        return $this->render('AriiJIDBundle:Requests:bootstrap.html.twig', array('result' => $value));
    }
    
    public function resultAction($output='html',$req='',$db='')
    {
        $lang = $this->getRequest()->getLocale();
        $request = Request::createFromGlobals();
        if ($request->query->get( 'request' )!='')
            $req = $request->query->get( 'request');
        
        // cas de l'appel direct
        if ($request->query->get( 'db' )!='') {
            $db=$request->query->get( 'db');
        }
        
        if ($db!='') {
            $portal = $this->container->get('arii_core.portal');
            $portal->setDatabaseByName($db);            
        }
        
        if (!isset($req)) return $this->summaryAction();
        
        $page = $this->getBaseDir().'/'.$req.'.yml';
        $content = file_get_contents($page);
        
        $yaml = new Parser();
        try {
            $value = $yaml->parse($content);
            
        } catch (ParseException $e) {
            $error = array( 'text' =>  "Unable to parse the YAML string: %s<br/>".$e->getMessage() );
            return $this->render('AriiJIDBundle:Requests:ERROR.html.twig', array('error' => $error));
        }

        $sql = $this->container->get('arii_core.sql');
        
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');

        # on prend la base par défaut 
        $portal = $this->container->get('arii_core.portal');     
        $db = $portal->getDatabase();
        switch($db['driver']) {
            case 'postgre':
            case 'postgres':
                $driver = 'postgres';
                break;
            case 'mysql':
            case 'mysqli':
                $driver = 'mysql';
                break;
            case 'oci8':
            case 'oracle':
            case 'pdo_oci':
                $driver = 'oracle';
                break;
            default:
                $driver = $db['driver'];
        }
                
        if (!isset($value['sql'][$driver]))
            throw new \Exception('JID',7);
        
        $res = $data->sql->query($value['sql'][$driver]);
        $date = $this->container->get('arii_core.date');
        $nb=0;
        // On cree le tableau des consoles et des formats
        $value['columns'] = $Format = array();
        foreach (explode(',',$value['header']) as $c) {
            if (($p = strpos($c,'('))>0) {
                $h = substr($c,0,$p);
                $Format[$h] = substr($c,$p+1,strpos($c,')',$p)-$p-1);
                $c = $h;
            }
            array_push($value['columns'],$c);
        }
        // bibliothèques
        $date = $this->container->get('arii_core.date');   
        while ($line = $data->sql->get_next($res))
        {
            $r = array();
            $status = 'unknown';
            foreach ($value['columns'] as $h) {
                if (isset($line[$h])) {
                    // format special
                    $value['status'] = '';
                    if (isset($Format[$h])) {
                        switch ($Format[$h]) {
                            case 'localtime':
                                $dt = new \DateTime($line[$h], new \DateTimeZone('UTC'));
                                $dt->setTimezone(new \DateTimeZone('Europe/Paris')); // a integrer au portail
                                $val = $dt->format('Y-m-d H:i:s');
                                break;
                            case 'timestamp':
                                $val = $date->Time2Local($line[$h]);
                                break;
                            case 'duration':
                                $val = $date->FormatTime($line[$h]);
                                break;
                            case 'br':
                                $val = str_replace(array("\t","\n"),array("     ","<br/>"),$line[$h]);
                                break;
                            case 'log':
                                if (gettype($line[$h])=='object') {
                                    switch ($driver) {
                                        case 'oracle':
                                            $Res = gzinflate ( substr( $line[$h]->load(), 10, -8) );
                                            break;
                                    }
                                    // on ne prend que le batch
                                    $New = [];
                                    foreach (explode("\n",$Res) as $line) {
                                        if (!preg_match('/\d\d\d\d-.*?\[(.*?)\]   \(.*?\) /',$line,$Matches)) continue;
                                        if ($Matches[1]=='ERROR') {
                                            $before = '<font color="red">';
                                            $after = '</font>';
                                        }
                                        elseif ($Matches[1]=='WARN') {
                                            $before = '<font color="orange">';
                                            $after = '</font>';
                                        }
                                        else {
                                            $before = $after = '';
                                        }
                                        array_push($New,$before.preg_replace('/\d\d\d\d-.*?\[(.*?)\]   \(.*?\) /','',$line).$after);                                    
                                    }
                                    $val = '<pre>'.implode("<br/>",$New).'</pre>';
                                }
                                else {
                                    $val = $line[$h];
                                }
                                break;
                            default:
                                $val = $line[$h].'('.$Format[$h].')';
                                break;
                        }
                    }
                    else {
                        $val = $line[$h];
                    }
                }
                else  $val = '';
                array_push($r,$val);
            }
            $nb++;
            $value['lines'][$nb]['cells'] = $r;
            $value['lines'][$nb]['status'] = $status;
         }
        $value['count'] = $nb;
        return $this->render('AriiJIDBundle:Requests:bootstrap.html.twig', array('result' => $value ));
    }

    /* Donne le répertoire de travail en fonction de la langue et de l'utilisiation */
    private function getBaseDir() {
        $lang = $this->getRequest()->getLocale();
        $portal = $this->container->get('arii_core.portal');
        return $portal->getWorkspace().'/JID/Requests/'.$lang;        
    }    
    
}