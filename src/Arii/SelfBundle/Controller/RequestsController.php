<?php

namespace Arii\SelfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class RequestsController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiSelfBundle:Requests:index.html.twig');
    }

    public function menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiSelfBundle:Requests:menu.xml.twig',[], $response);
    }
    
    public function gridAction()
    {
        $request = Request::createFromGlobals();
        $req = $request->get('request');
                
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<rows>';
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        
        if ($req=='')
            $Requests = $this->getDoctrine()->getRepository("AriiSelfBundle:Request")->findBy(
            [ 'processed' => null ]);
        else 
            $Requests = $this->getDoctrine()->getRepository("AriiSelfBundle:Request")->findBy(
            [   'name' => $req,
                'processed' => null ]);
            

        $portal = $this->container->get('arii_core.portal');
        $ColorStatus = $portal->getColors();
        
        $Host = [];
        foreach ($Requests as $Request) { 
            
            $processed = $Request->getProcessed();
            $status = $Request->getReqStatus();

            $list .= '<row id="'.$Request->getId().'" style="background-color: '.$ColorStatus[$status]['bgcolor'].';">';
            $list .= '<cell>'.$Request->getTitle().'</cell>';
            $list .= '<cell>'.$Request->getReference().'</cell>';
            $Parameters = $Request->getParameters();
            $P = [];
            foreach($Parameters as $k=>$v) {
                array_push($P,"$k=$v");
            }
            $list .= '<cell>'.implode(',',array_values($P)).'</cell>';
            $list .= '<cell>'.$status.'</cell>';
            $list .= '<cell>'.($Request->getCreated()?$Request->getCreated()->format("Y-m-d H:i:s"):'').'</cell>';
            $list .= '<cell>'.($Request->getPlanned()?$Request->getPlanned()->format("Y-m-d H:i:s"):'').'</cell>';
            $list .= '<cell>'.($Request->getProcessed()?$Request->getProcessed()->format("Y-m-d H:i:s"):'').'</cell>';
            $list .= '<cell>'.$Request->getUsername().'</cell>';
            
            $list .= '</row>';
        }
        $list .= '</rows>';

        $response->setContent( $list );
        return $response;        
    }

    public function treeAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        
        $Requests = $this->getDoctrine()->getRepository("AriiSelfBundle:Request")->findToDo();
      
        $portal = $this->container->get('arii_core.portal');
        $ColorStatus = $portal->getColors();
        
        $Queue = [];
        foreach ($Requests as $Request) { 
            $name = $Request->getName();
            if (!isset($Queue[$name])) {
                $Queue[$name]['name']=$name;
                $nb=0;
            }
            else {
                $nb = $Queue[$name]['nb']+1;
            }
            $Queue[$name]['nb'] = $nb;
            $Queue[$name]['request'][$nb]['id']   = $Request->getId();
            $Queue[$name]['request'][$nb]['created']   = $Request->getCreated();
            $Queue[$name]['request'][$nb]['reference'] = $Request->getReference();
        }

        $yaml = new Parser();
        $lang = $this->getRequest()->getLocale();

        $basedir = $portal->getWorkspace().'/Self/'.$lang;    
        
        $Tree = [];
        if ($dh = opendir($basedir)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file,-4) != '.yml') continue;
                
                $content = file_get_contents("$basedir/$file");
                $v = $yaml->parse($content);
                $name = $v['name'];
                $title = $v['title'];
                if (($p=strpos($title,'/'))>0) {
                    $group = substr($title,0,$p);
                    $title=substr($title,$p+1);
                }
                else {
                    $group = $v['name'];
                }
                if (!isset($Tree[$group])) {
                    $Tree[$group]['group']=$group;
                    $nb=0;
                }
                else {
                    $nb = $Tree[$group]['nb']+1;
                }
                $Tree[$group]['nb'] = $nb;
                $Tree[$group]['template'][$nb]['file']  = substr($file,0,strlen($file)-4);
                $Tree[$group]['template'][$nb]['title'] = $title;
            }
        }
        sort($Tree);

        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<tree id="0">';
        foreach ($Tree as $Group) {
                $list .= '<item id="G'.$Group['group'].'" text="'.$Group['group'].'" open="1" im0="group.png" im1="group.png" im2="group_add.png">';
                foreach ($Group['template'] as $Temp) {
                    $file = $Temp['file'];
                    $list .= '<item id="T'.$file.'" text="'.$Temp['title'].'" open="1" im0="form.png" im1="form.png" im2="form_add.png">';
                    if (isset($Queue[$file])) {
                        foreach ($Queue[$file]['request'] as $Q) {
                            $list .= '<item id="R'.$Q['id'].'" text="'.$Q['reference'].'" im0="bullet_black.png" im1="bullet_black.png" im2="bullet_black.png" />';
                        }
                    }
                    $list .= '</item>';
                }
                $list .= '</item>';
        }
        $list .= '</tree>';

        $response->setContent( $list );
        return $response;        
    }

    public function countAction()
    {
        $request = Request::createFromGlobals();        
        $status = $request->get('status');
        return new Response( '{ count: "'.$this->getDoctrine()->getRepository('AriiSelfBundle:Request')->countStatus($status).'" }' );
    }
    
}
