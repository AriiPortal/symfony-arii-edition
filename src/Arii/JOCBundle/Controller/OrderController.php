<?php

namespace Arii\JOCBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    protected $images;
    protected $request;
    
    public function __construct( )
    {
          $this->request = Request::createFromGlobals();
          $this->images = $this->request->getUriForPath('/../arii/images/wa');
    }

/* NOUVELLE VERSION ORM */
    public function detailAction($id=-1)
    {
        if ($id<0)
            $id = $this->request->get('id');

        $JOC = $this->container->get('arii.joc');
        $Order = $JOC->getOrder($id);
        
        return $this->render('AriiJOCBundle:Order:detail.html.twig', $Order);
    }    

    public function diagAction($id=-1)
    {
        if ($id<0)
            $id = $this->request->get('id');

        $JOC = $this->container->get('arii.joc');
        $Order = $JOC->getOrder($id);
        $Spooler = $JOC->getSpooler($Order['SPOOLER_ID']);
        return $this->render('AriiJOCBundle:Order:diag.html.twig', array_merge($Order,$Spooler));
    }    
    
    public function formAction($id=-1)
    {
        if ($id<0)
            $id = $this->request->get('id');

        $JOC = $this->container->get('arii.joc');
        $Order = $JOC->getOrder($id);

        $Render = $this->container->get('arii_core.render');
        return $Render->Form($Order);
    }

    public function graphAction($id=-1)
    {
        if ($id<0)
            $id = $this->request->get('id');

        $JOC = $this->container->get('arii.joc');
        $Order = $JOC->getOrder($id);
        
        // On recupere la chaine
        $Chain = $JOC->getJobChain($Order['JOB_CHAIN_ID']);
        
        // On recupere tous les noeuds de la chaine
        $Nodes =  $Chain = $em->getRepository("AriiJOCBundle:JobChains")->findBy( [ 'job_chain_id' ]);  
                
        exit();
    }    
    
    public function historyAction($id)
    {
        if ($id<0)
            $id = $this->request->get('id');
        
        $JOC = $this->container->get('arii.joc');
        $Order = $JOC->getOrder($id);
        
        if ($Order['HISTORY_ID']!='') {
            $JID = $this->container->get('arii.jid');
            $Data = $JID->getOrderLog($Data['HISTORY_ID']);
            print_r($Data);
        }
        
        exit();
        // Detail de l'ordre
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('data');
        $sql = $this->container->get('arii_core.sql');
        $qry = $sql->Select(array('fo.name as ORDER_ID','fo.TITLE','fo.STATE','fo.STATE_TEXT',
                    'fo.SUSPENDED','fo.START_TIME',
                    'fjc.NAME as JOB_CHAIN'))
               .$sql->From(array('JOC_ORDERS fo'))
               .$sql->LeftJoin('JOC_JOB_CHAINS fjc',array('fo.job_chain_id','fjc.id'))
               .$sql->Where(array('fo.id'=>substr($id,2)));
        try {
            $res = $data->sql->query( $qry );
        } catch (Exception $exc) {
            exit();
        }
        $Infos = $data->sql->get_next($res);
        if ($Infos['SUSPENDED']>0) {
            $Infos['STATUS'] = 'SUSPENDED';
        }
        else {
            $Infos['STATUS'] = 'ACTIVE';
        }
        return $this->render('AriiJOCBundle:Order:detail.html.twig', $Infos);
    }    

/* ANCIEN SYSTEME */
    
    public function indexAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $state = $this->container->get('arii_joc.state');
        $Orders = $state->Orders(1,0,'last',$id);
        if (isset($Orders[$id])) {
            $Infos['id'] = $id;
            $Infos['spooler'] = $Orders[$id]['SPOOLER_ID'];
            $Infos['order'] = $Orders[$id]['ORDER'];
            $Infos['chain'] = $Orders[$id]['JOB_CHAIN'];
        }
        else {
            print "order $id ?";
        }
        return $this->render('AriiJOCBundle:Order:index.html.twig', $Infos);
    }

/*******************************************/
// supprimé du JID
/*
    public function paramsAction() {
        // recherche des infos
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        // Recherche des informations dans la base de données
        // Nécessaire pour la securité, inutile de faire un accès direct !
        $sos = $this->container->get('arii_jid.sos');
        list($spooler_id, $order_id, $job_chain) = $sos->getOrderInfos($id);

        // On recupere les informations du job chain
        $cmd = '<show_order order="' . $order_id . '" job_chain="' . $job_chain . '" what="payload"/>';
        $result = $sos->Command($spooler_id, $cmd);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        if (isset($result['spooler']['answer']['order']['payload']['params']['param'])) {
            if (!isset($result['spooler']['answer']['order']['payload']['params']['param'][0])) {
                $result['spooler']['answer']['order']['payload']['params']['param'][0] = $result['spooler']['answer']['order']['payload']['params']['param'];
            }
            $n = 0;
            while (isset($result['spooler']['answer']['order']['payload']['params']['param'][$n])) {
                if (isset($result['spooler']['answer']['order']['payload']['params']['param'][$n]['attr'])) {
                    $param = $result['spooler']['answer']['order']['payload']['params']['param'][$n]['attr'];
                    $list .= "<row><cell>".$param['name']."</cell><cell>".$param['value']."</cell></row>";
                }
                $n++;
            }
        }
        $list .= "</rows>\n";
        $response->setContent($list);
        return $response;
    }
*/
    
    public function paramsAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->query->get( 'id' );
        $sql = $this->container->get('arii_core.sql');
        $qry = $sql->Select(array( 'id','name','value'))
                .$sql->From(array('JOC_PAYLOADS'))
                .$sql->Where(array('order_id'=>$id))
                .$sql->OrderBy(array('name'));
        $db = $this->container->get('arii_core.db');
        $grid = $db->Connector('grid');
        $grid->event->attach("beforeRender",array($this,"params_render"));
        $grid->render_sql($qry,'id','name,value');
    }

    function params_render ($data){
        $value = $data->get_value('value');
        if (is_object($value)) {
            $data->set_value('value',$value->load());
        }
    }
    
    private function ColorNode($state,$error,$endtime) {
        if ($endtime=='') {
            $color='#ffffcc';
        }
        elseif ($error) {
            if (substr($state,0,1)=='!') {
                $color = 'red';
            }
            else {
                $color='#fbb4ae';
            }
        }
        else {
            $color = "#ccebc5";        
        }
        return $color;
    }
    
    private function Node($Infos=array()) {
        $res = '"'.$Infos['STATE'].'" '; 
        if (isset($Infos['ACTION']) and ($Infos['ACTION']=='stop')) {
            $color = 'red';
        }
        else {
            $color = $this->ColorNode($Infos['STATE'],$Infos['ERROR'],$Infos['END_TIME']);
        }
        $res .= '[id="\N";label=<<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0" COLOR="grey" BGCOLOR="'.$color.'">';
        $res .= '<TR><TD align="left" colspan="3">'.$Infos['STATE'].'</TD></TR>';
        if ($Infos['ERROR']>0) {
            $res .= '<TR><TD><IMG SRC="'.$this->images_path.'/error.png"/></TD><TD align="left" COLSPAN="2">'.substr($Infos['ERROR_TEXT'],15).'</TD></TR>';
        }
        if (isset($Infos['JOB_NAME'])) {
            $res .= '<TR><TD><IMG SRC="'.$this->images_path.'/cog.png"/></TD><TD align="left" colspan="2">'.$Infos['JOB_NAME'].'</TD></TR>';
        }
        if (isset($Infos['START_TIME'])) {
            $res .= '<TR><TD><IMG SRC="'.$this->images_path.'/time.png"/></TD><TD align="left" >'.$Infos['START_TIME'].'</TD><TD align="left" >'.$Infos['END_TIME'].'</TD></TR>';
        }
        $res .= "</TABLE>> URL=\"javascript:parent.JobDetail();\"]";
        return "$res\n";
    }
    
    private function Order($Infos=array()) {
        $res = '"'.$Infos['NAME'].'" '; 
        if ($Infos['END_TIME']=='') {
            $color='#ffffcc';
        }
        elseif ($Infos['ERROR']) {
            $color='#fbb4ae';
        }
        else {
            $color = "#ccebc5";        
        }
        $res .= '[id="\N";label=<<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0" COLOR="grey" BGCOLOR="'.$color.'">';
        $res .= '<TR><TD><IMG SRC="'.$this->images_path.'/lightning.png"/></TD><TD align="left">'.$Infos['NAME'].'</TD></TR>';
        if ($Infos['TITLE']!='') {
                $res .= '<TR><TD><IMG SRC="'.$this->images_path.'/comment.png"/></TD><TD align="left">'.$Infos['TITLE'].'</TD></TR>';
        }
        if ($Infos['ERROR']>0) {
            $res .= '<TR><TD><IMG SRC="'.$this->images_path.'/error.png"/></TD><TD align="left">'.$Infos['ERROR_TEXT'].'</TD></TR>';
        }
        $res .= '</TABLE>>]';
        return "$res\n";
    }

}
