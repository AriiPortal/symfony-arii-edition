<?php

namespace Arii\JIDBundle\Controller\Scheduler;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{
    protected $images;
    protected $Done;
    
    public function indexAction()
    {
        // database courante
        $portal = $this->container->get('arii_core.portal');       
        return $this->render('AriiJIDBundle:Scheduler\Orders:index.html.twig', [ 'db' =>$portal->getDatabase() ]);
    }

    public function menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJIDBundle:Scheduler\Orders:menu.xml.twig',array(), $response );
    }
    
    public function treeAction() {
        // en attendant le cache
        $request = Request::createFromGlobals();
        $stopped = $request->get('stopped');
        
        $folder = 'live';
        // $this->syncAction($folder);
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');
   /* On prend l'historique */
        $Fields = array (
           '{spooler}'    => 'SPOOLER_ID', 
            '{job_chain}'   => 'JOB_CHAIN',
            '{start_time}' => 'START_TIME',
            'ORDER_ID'   => '%.%' );

        $sql = $this->container->get('arii_core.sql');
        $tools = $this->container->get('arii_core.tools');

        $qry = $sql->Select(array('ORDER_ID','HISTORY_ID','SPOOLER_ID','JOB_CHAIN','START_TIME','END_TIME','STATE' ))
                .$sql->From(array('SCHEDULER_ORDER_HISTORY'))
                .$sql->Where($Fields)
                .$sql->OrderBy(array('SPOOLER_ID','JOB_CHAIN','START_TIME desc'));  
   
        $res = $data->sql->query( $qry );
        $Chains = $Orders = array();
        
        while ( $line = $data->sql->get_next($res) ) {

            $id  =  $line['HISTORY_ID'];
            // La chaine est le prefix de l'ordre
            list($job_chain,$order) = explode('.',$line['ORDER_ID']);
            $chain = "/".$line['SPOOLER_ID'].'/'.dirname($line['JOB_CHAIN']).'/'.$job_chain;
            
            $dir = $chain.'/'.$order;
            
            if (!isset($Chains[$chain])) {
                $key_files[$chain] = $chain;
                $Chains[$chain]=1; 
            }
            
            if (isset($Orders[$dir])) continue;
            $Orders[$dir] = $line; 
            
            // On ccompte les erreurs
            $key_files[$dir] = $dir;
        }

        // Prend on en compte les suspended ?
            $Fields = array (
                '{spooler}'    => 'SPOOLER_ID', 
                '{job_chain}'   => 'PATH',
                'STOPPED'   => 1 );
            $qry = $sql->Select(array('SPOOLER_ID','PATH' ))
                    .$sql->From(array('SCHEDULER_JOB_CHAINS'))
                    .$sql->Where($Fields);  

              $res = $data->sql->query( $qry );
              while ( $line = $data->sql->get_next($res) ) {
                $dir = '/'.$line['SPOOLER_ID'].'/'.$line['PATH'];
                $Chains[$dir]='STOPPED';
            }
        
        $tools = $this->container->get('arii_core.tools');
        $tree = $tools->explodeTree($key_files, "/");
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<tree id='0'>\n";
        
        $list .= $this->Folder2XML( $tree, '', $Chains, $Orders );
        $list .= "</tree>\n";
        $response->setContent( $list );
        return $response;
    }
 
   function Folder2XML( $leaf, $id = '', $Chains, $Orders ) {
            $return = '';
            if (is_array($leaf)) {
                    foreach (array_keys($leaf) as $k) {
                            $Ids = explode('/',$k);
                            $here = array_pop($Ids);
                            $i  = "$id/$k";
                            # On ne prend que l'historique
                            // Chains ?
                            if (isset($Chains[$i])) {
                                if ($Chains[$i]=='STOPPED')
                                    $return .= '<item style="background-color: red;" id="'.$i.'" text="'.basename($i).'" im1="job_chain.png" im0="job_chain.png"  open="1">';
                                else 
                                    $return .= '<item id="'.$i.'" text="'.basename($i).'" im1="job_chain.png" im0="job_chain.png"  open="1">';
                                    
                            }
                            elseif (isset($Orders[$i])) {
                                $detail = ' ('.$Orders[$i]['STATE'].')';
                                if (substr($Orders[$i]['STATE'],0,1)=='!') {
                                    $style =  ' style="background-color: red;"';
                                }
                                else {
                                    // Statut
                                    switch ($Orders[$i]['STATE']) {
                                        case 'SUCCESS':
                                            $style =  ' style="background-color: #ccebc5;"';
                                            break;
                                        case 'FAILURE':
                                            $style =  ' style="background-color: #fbb4ae;"';
                                            break;
                                        default:
                                            $style = '';
                                            break;
                                    }
                                    $detail = '';
                                }
                                $return .= '<item'.$style.' id="O:'.$Orders[$i]['HISTORY_ID'].'" text="'.basename($i).$detail.'" im0="order.png">';
                            }
                            elseif ($id == '' ) {                                
                                $return .= '<item id="'.$i.'" text="'.$id.basename($i).'" im0="cog.png" im1="cog.png"  open="1">';
                            }
                            else {
                                $return .=  '<item id="'.$i.'" text="'.basename($i).'" im0="folderClosed.gif">';
                            }
                           $return .= $this->Folder2XML( $leaf[$k], $id.'/'.$k, $Chains, $Orders);
                           $return .= '</item>';
                    }
            }
            return $return;
    }


}
