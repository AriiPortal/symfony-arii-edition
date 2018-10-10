<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class GraphsController extends Controller{

    private $images_path;
    private $ColorStatus;
    private $Icons = array(            
            'APP'           => 'application',
            'BOX_NAME'      => 'box',
            'COMMAND'       => 'shell',
            'DB'            => 'database_connect',
            'HOST'          => 'server',
            'JOB'           => 'job',
            'POOL'          => 'pool'
        );        
        // Couleur en fonction du status
    private $Color = array(
            'OK'      => '#ccebc5',
            'WARN'    => '#fed9a6',
            'ERROR'   => '#fbb4ae',
            'UNKNOWN' => '#bbbbbb'
        );
    // Plus doux pour les clusters
    private $CColor = array(
            'OK'      => '#cddcc6',
            'WARN'    => '#ffdaa7',
            'ERROR'   => '#fbb4ae',
            'UNKNOWN' => '#dddddd'
        );
    
    public function indexAction()
    {
        return $this->render('AriiACKBundle:Graphs:index.html.twig');
    }

    public function gridAction()
    {
        $Graphs = $this->getDoctrine()->getRepository('AriiACKBundle:Graph')->findAll();
        $Grid = [];
        foreach ($Graphs as $Graph) {
            array_push($Grid, [
                'id' =>          $Graph->getId(),
                'title' =>       $Graph->getTitle(),
                'description' => $Graph->getDescription()
            ]);
        } 
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Grid,'title,description');
    }
    
    public function generateAction()
    {
        $request = Request::createFromGlobals();
        $view = $request->get('view');

        $portal = $this->container->get('arii_core.portal');
        $this->ColorStatus = $portal->getColors();
                
        $output = "svg";
        if ($request->query->get( 'output' ))
            $output = $request->query->get( 'output' );

        // Localisation des images 
        $root = $this->get('kernel')->getRootDir();
        $images = '/images';
        $images_path =  str_replace('/',DIRECTORY_SEPARATOR,$root.'/../web'.$images);
        $this->images_path = $images_path;
        
        $images_url = $this->container->get('templating.helper.assets')->getUrl($images);        
/*
 * print $images_path;
 
print "<br/>";
print $images_url;
exit();
 * 
 */
        // Design
        $out = 'digraph ACK {
fontname=arial 
fontsize=10
node [shape=plaintext,fontname=arial,fontsize=10]
edge [fontname=arial,fontsize=8,decorate=true,compound=true]
bgcolor=white
';
        $em = $this->getDoctrine()->getManager();
        // On récupère les noeuds du graph
        $Graph = $this->getDoctrine()->getRepository('AriiACKBundle:Graph')->find($view);
        
        // On récupère les neouds du graph
        $GraphObjects = $this->getDoctrine()->getRepository('AriiACKBundle:GraphObject')->findBy([
            "graph" => $Graph
        ]);
        
        $Obj = [];
        foreach($GraphObjects as $GraphObject) {
            $Object = $GraphObject->getObject();            
            $id = $Object->getId();
            
            // Init
            if (!isset($Obj[$id]))
                $Obj[$id] = $this->extractInfo($Object,$id);
            
            // on ajoute les liens            
            $Links = $this->getDoctrine()->getRepository('AriiACKBundle:Link')->findBy([
                "obj_from" => $Object
            ]);
            foreach($Links as $Link) {
                $from = $Link->getObjFrom();
                $to = $Link->getObjTo();
                
                $type = $Link->getLinkType();
                if ($type=='DEPENDS') {
                    // on peut traiter sans autre
                    $out .= $id.' -> '.$to->getId()." [color=green]\n";
                    // mais on conserve l'infos
                    if (isset($Obj[$id]['depends']))
                        $Obj[$id]['depends'] .= ','.$to->getId();
                    else 
                        $Obj[$id]['depends'] = $to->getId();
                }
                elseif ($type=='CONTAINS') {
                    $inside = $to->getId();                    
                    $out .= $id.' -> '.$inside." [style=dotted,color=green]\n";
                    // on conserve les infos
                    $Obj[$inside]['parentId'] = $id;
                }
            }            
        }
        
        // On complete
        foreach ($Obj as $id => $Info) {            
            if (isset($Obj[$id]['id'])) 
                continue;
            
            $Object = $this->getDoctrine()->getRepository('AriiACKBundle:Object')->find($id);
            if (!$Object)
                throw new \Exception("'$id' ?");

            $Obj[$id] = $this->extractInfo($Object,$id,$Obj[$id]);
            
        }

        // Couleur des clusters
        foreach ($Obj as $id => $Info) {
            if (!isset($Obj[$id]['color'])) {
                $status = $Obj[$id]['status'];
                $Obj[$id]['color'] = '"'.$this->CColor[$status].'"';
            }
        }
        
        foreach ($Obj as $id => $Info) {
            // Dessin des noeuds
            $out .= $this->Node( $id, $Info);
        }
        
        
//        print_r($Obj);
//        exit();
        
        $Tree = $this->buildTree($Obj);
        $out .= $this->Cluster($Tree);
        
        // print_r($Tree);
        
        $out .=" }\n";
        // print $out;
        // exit();
        $graphviz = $this->container->get('arii_core.graphviz');
        $Options = array(
            'digraph' => false,
            'output' => $output,
            'output' => 'png',
            'images' => $images
        );

        return $graphviz->dot($out,$Options);
    }
    
    // complete l'objet en cours
    private function extractInfo($Object,$id,$Obj=[]) {
        // Complement (quel niveau d'information ?
        if (!isset($Obj['id']))          $Obj['id']          = $id;
        if (!isset($Obj['objType']))     $Obj['objType']     = $Object->getObjType();
        if (!isset($Obj['name']))        $Obj['name']        = $Object->getName();
        if (!isset($Obj['desc']))        $Obj['desc']        = $Object->getDescription();
        if (!isset($Obj['parentId']))    $Obj['parentId']    = null;
        if (!isset($Obj['state']))       $Obj['state']       = $Object->getState();
        if (!isset($Obj['stateTime']))   $Obj['stateTime']   = $Object->getStateTime();
        if (!isset($Obj['stateUser']))   $Obj['stateUser']   = $Object->getStateUser();
        if (!isset($Obj['downtime']))    $Obj['downtime']      = $Object->getActive();
        if (!isset($Obj['downtimeTime']))$Obj['downtimeTime']  = $Object->getActiveTime();
        if (!isset($Obj['downtimeUser']))$Obj['downtimeUser']  = $Object->getActiveUser();
        if (!isset($Obj['ack']))         $Obj['ack']         = $Object->getAck();
        if (!isset($Obj['ackTime']))     $Obj['ackTime']     = $Object->getAckTime();
        if (!isset($Obj['ackUser']))     $Obj['ackUser']     = $Object->getAckUser();
        
        if (!isset($Obj['status']))      $Obj['status']      = $Object->getStatus();
        if (!isset($Obj['statusTime']))  $Obj['statusTime']  = $Object->getStatusTime();
        
        // changement ?
        

        return $Obj;
    }

    function buildTree(array &$dataset) {
        $tree = array();

            /* Most datasets in the wild are enumerative arrays and we need associative array
               where the same ID used for addressing parents is used. We make associative
               array on the fly */
        $references = array();
        foreach ($dataset as $id => &$node) {
            // Add the node to our associative array using it's ID as key
            $references[$node['id']] = &$node;

            // Add empty placeholder for children
            $node['children'] = array();

                    // It it's a root node, we add it directly to the tree
            if (is_null($node['parentId'])) {
                $tree[$node['id']] = &$node;
            } else {
                    // It was not a root node, add this node as a reference in the parent.
                $references[$node['parentId']]['children'][$node['id']] = &$node;
            }
        }

        return $tree;
    }

    // Fonction recursive pour gerer les containers de containers
    private function Cluster($Tree) {
        $result='';
        foreach($Tree as $id => $Leaf) {
            $status = $Leaf['status'];
            $result .= "subgraph cluster_$id {\n";
            $result .= "style=filled\n";
            $result .= "bgcolor=".$Leaf['color']."\n";
            $result .= "$id\n";
            $result .= $this->Cluster($Leaf['children']);
            $result .= "}\n";            
        }
        return $result;
    }
    
    private function Node($ID,$Infos,$Fields=array(),$realtime=false) {
        
        $status = $Infos['status'];
                
        $label  = '<TABLE BORDER="1" CELLBORDER="0" COLOR="orange" CELLSPACING="0" BGCOLOR="'.$this->Color[$status].'">';
        if (isset($Infos['ack']) and ($Infos['ack'])) {
            $label .= '<TR><TD>'.$Infos['ackUser'].'</TD><TD><IMG SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/ack.png').'"/></TD><TD>'.$Infos['ackTime']->format("Y-m-d H:i:s").'</TD></TR>';
        }
        $label .= '<TR><TD ROWSPAN="3"><IMG SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/graph/'.$status).'.png"/></TD></TR>';
        if (isset($Infos['name'])) {
            $type = $Infos['objType'];
            $icon = (isset($this->Icons[$type])?'<IMG SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/'.$this->Icons[$type]).'.png"/>':'['.$type.']');
            $label .= '<TR><TD>'.$icon.'</TD><TD><FONT FACE="Arial-Bold">'.$Infos['name'].'</FONT></TD></TR>';
        }
        // Actif ou non ?
        if ($Infos['downtime']) {
            $icon = 'off';
            $time = $Infos['downtimeTime']->format("Y-m-d H:i:s");
            $comment = '';
        }
        else {
            $icon = 'on';
            $time = $Infos['stateTime']->format("Y-m-d H:i:s");
            $comment = '';
        }
        $label .= '<TR><TD><IMG SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/'.$icon.'.png').'"/></TD><TD>'.$time.'</TD><TD>'.$comment.'</TD></TR>';
        if (isset($Infos['desc'])) {
            $label .= '<TR><TD ALIGN="LEFT" COLSPAN="4"><FONT FACE="Arial-Italic">'.$Infos['desc'].'</FONT></TD></TR>';
        }
        
        // Complement
/*            
        foreach ($Fields as $k) {
            if (isset($Infos[$k]) and (trim($Infos[$k])!='')) {
                $label .= '<TR><TD>';
                if (isset($Icons[$k])) {
                    $label .= '<IMG SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path).'/'.$Icons[$k].'.png"/>';            
                }
                $label .= '</TD><TD ALIGN="LEFT">'. htmlentities($Infos[$k]).'</TD></TR>';            }
        }
*/        
        $label .= '</TABLE>';
//        return $ID.' [label=<<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0" COLOR="grey" BGCOLOR="red"><TR><TD>TEST</TD></TR></TABLE>>]';
//        return $ID.' [label="TEST",image="abort.png",fillcolor=red]'."\n";
        return $ID.' [label=<'.$label.'>]'."\n";        
    }
}
?>
