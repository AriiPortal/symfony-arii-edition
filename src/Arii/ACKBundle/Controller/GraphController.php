<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class GraphController extends Controller{

    private $images_path;
    private $ColorStatus;
    private $Icons = array(            
            'APP'           => 'application',
            'BOX'           => 'box',
            'COMMAND'       => 'shell',
            'DB'            => 'database_connect',
            'HOST'          => 'server',
            'JOB'           => 'job',
            'POOL'          => 'pool'
        );        
        // Couleur en fonction du status
    private $Color = array(
            'OK'      => '#ccebc5',
            'WARNING' => '#fed9a6',
            'ERROR'   => '#fbb4ae',
            'UNKNOWN' => '#bbbbbb'
        );
    // Plus doux pour les clusters
    private $CColor = array(
            'OK'      => '#cddcc6',
            'WARNING' => '#ffdaa7',
            'ERROR'   => '#fbb4ae',
            'UNKNOWN' => '#dddddd'
        );
    
    public function indexAction()
    {
        return $this->render('AriiACKBundle:Graphs:index.html.twig');
    }

    public function formAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $Graph = $this->getDoctrine()->getRepository("AriiACKBundle:Graph")->find($id);      
        $Grid = [
            'id' =>         $Graph->getId(),
            'name' =>       $Graph->getName(),
            'title' =>      $Graph->getTitle(),
            'description'=> $Graph->getDescription()
        ];
        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->form($Grid);        
    }

    // ajout/suppression d'une sonde
    public function probeAction()
    {   
        $request = Request::createFromGlobals();
        $graph_id = $request->get('graph_id');
        $probe_id = $request->get('probe_id');
        $action = $request->get('action');
        
        $em = $this->getDoctrine()->getManager();
        $Graph = $this->getDoctrine()->getRepository("AriiACKBundle:Graph")->find($graph_id);
        if (!$Graph) 
            new \Exception("Graph '$graph_id' unknown !");
        $Probe = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->find($probe_id);
        if (!$Probe) 
            new \Exception("Probe '$probe_id' unknown !");
        
        // Ca existe ?
        $GraphProbe = $this->getDoctrine()->getRepository("AriiACKBundle:GraphProbe")->findOneBy([
            'graph' => $Graph,
            'probe' => $Probe
        ]);
        
        if ($action=='add') {
            if (!$GraphProbe) {
                $GraphProbe = new \Arii\ACKBundle\Entity\GraphProbe();
                $GraphProbe->setTitle($Probe->getTitle());
                $GraphProbe->setDescription($Probe->getDescription());
            }
            $GraphProbe->setProbe($Probe);
            $GraphProbe->setGraph($Graph);

            $em->persist($GraphProbe);
            $em->flush();

            return new Response('added');
        }
        else {
            if ($GraphProbe) {
                $em->remove($GraphProbe);
                $em->flush();
                return new Response('deleted');
            }
        }
        return new Response('nothing');        
    }
    
    public function probesAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $GraphProbes = $this->getDoctrine()->getRepository('AriiACKBundle:GraphProbe')->findProbes($id);
        $Grid = [];
        foreach ($GraphProbes as $GraphProbe) {
            $Probe = $GraphProbe->getProbe();
            array_push($Grid, [
                'id' =>     $Probe->getId(),
                'name' =>   $Probe->getName(),
                'title' =>  $Probe->getTitle()
            ]);
        } 
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Grid,'name,title');
    }
    
    // Dessin d'un neoud
    public function linksAction() {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

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

        // Design
        $out = 'digraph ACK {
fontname=arial 
fontsize=10
node [shape=plaintext,fontname=arial,fontsize=10]
edge [fontname=arial,fontsize=8,decorate=true,compound=true]
bgcolor=white
imagescale=false
fixedsize=true
';
        $em = $this->getDoctrine()->getManager();
        $Probe = $this->getDoctrine()->getRepository('AriiACKBundle:Probe')->find($id);

        // On récupère les neouds du graph
        $Links = $this->getDoctrine()->getRepository('AriiACKBundle:Link')->listNodes($id);

        $Obj = [];
/* Attention on doit avoir un cluster maitre car l'arbre ne peut pas
  - avoir deux parents nuls
  - aucun parent nul
  On fait donc un cluster 0.
        $Obj[0] = [
            'id'       => 0,
            'parentId' => null,
            'name'     => '',
            'title'    => '',
            'status'   => 'OK',
            'objType'  => 0
        ];
FAUX!
*/              
        foreach($Links as $Link) {
            $From = $Link->getObjFrom();
            $To   = $Link->getObjTo();
            
            $type = $Link->getLinkType();
            
            $id = $From->getId();            
            if (!isset($Obj[$id]))
                $Obj[$id] = $this->extractInfo($From,$id);
            
            // Idem pour la cible
            $to = $To->getId();
            if (!isset($Obj[$to]))
                $Obj[$to] = $this->extractInfo($To,$to);
            
            if ($type==1) {
                $out .= $id.' -> '.$to." [style=dotted,color=lightgrey]\n";
                // on conserve les infos
                $Obj[$to]['parentId'] = $id;
            }
            elseif ($type==2) {
                // on peut traiter sans autre
                $out .= $to.' -> '.$id." [color=green]\n";
                // mais on conserve l'infos
                if (isset($Obj[$id]['depends']))
                    $Obj[$id]['depends'] .= ','.$to;
                else 
                    $Obj[$id]['depends'] = $to;
            }
        }
/*
        // On complete
        foreach ($Obj as $id => $Info) {            
            if (isset($Obj[$id]['id'])) 
                continue;
            
            $Probe = $this->getDoctrine()->getRepository('AriiACKBundle:Probe')->find($id);
            if (!$Probe)
                throw new \Exception("'$id' ?");

            $Obj[$id] = $this->extractInfo($Probe,$id,$Obj[$id]);
            
        }
*/
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
        
//        sort($Obj);
        $Tree = $this->buildTree($Obj);        
//        print_r($Obj);
//        print_r($Tree);
//        exit();
        $out .= $this->Cluster($Tree);
        

        $out .=" }\n";

        $graphviz = $this->container->get('arii_core.graphviz');
        $Options = array(
            'digraph' => false,
            'output' => $output,
            'output' => 'png',
            'images' => $images
        );

        return $graphviz->dot($out,$Options);
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

        // Design
        $out = 'digraph ACK {
fontname=arial 
fontsize=10
node [shape=plaintext,fontname=arial,fontsize=10]
edge [fontname=arial,fontsize=8,decorate=true,compound=true]
bgcolor=white
imagescale=false
';
        $em = $this->getDoctrine()->getManager();
        // On récupère les noeuds du graph
        $Graph = $this->getDoctrine()->getRepository('AriiACKBundle:Graph')->find($view);
        
        // On récupère les neouds du graph
        $GraphProbes = $this->getDoctrine()->getRepository('AriiACKBundle:GraphProbe')->findBy([
            "graph" => $Graph
        ]);
        
        $Obj = [];
        foreach($GraphProbes as $GraphProbe) {
            $Probe = $GraphProbe->getProbe();            
            $id = $Probe->getId();
            
            // Init
            if (!isset($Obj[$id]))
                $Obj[$id] = $this->extractInfo($Probe,$id);
            
            // on ajoute les liens            
            $Links = $this->getDoctrine()->getRepository('AriiACKBundle:Link')->findBy([
                "obj_from" => $Probe
            ]);
            foreach($Links as $Link) {
                $from = $Link->getObjFrom();
                $to = $Link->getObjTo();

                $type = $Link->getLinkType();
                if ($type==1) {
                    $inside = $to->getId();                    
                    $out .= $id.' -> '.$inside." [style=dotted,color=transparent]\n";
                    // on conserve les infos
                    $Obj[$inside]['parentId'] = $id;
                }
                elseif ($type==2) {
                    // on peut traiter sans autre
                    $out .= $to->getId().' -> '.$id." [color=green]\n";
                    // mais on conserve l'infos
                    if (isset($Obj[$id]['depends']))
                        $Obj[$id]['depends'] .= ','.$to->getId();
                    else 
                        $Obj[$id]['depends'] = $to->getId();
                }
            }            
        }
        
        // On complete
        foreach ($Obj as $id => $Info) {            
            if (isset($Obj[$id]['id'])) 
                continue;
            
            $Probe = $this->getDoctrine()->getRepository('AriiACKBundle:Probe')->find($id);
            if (!$Probe)
                throw new \Exception("'$id' ?");

            $Obj[$id] = $this->extractInfo($Probe,$id,$Obj[$id]);
            
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
        
        sort($Obj);
        $Tree = $this->buildTree($Obj);
//        print_r($Tree);
//        exit();
        $out .= $this->Cluster($Tree);
        

        $out .=" }\n";

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
    private function extractInfo($Probe,$id,$Obj=[]) {
        // Complement (quel niveau d'information ?
        if (!isset($Obj['id']))          $Obj['id']          = $id;
        if (!isset($Obj['objType']))     $Obj['objType']     = $Probe->getObjType();
        if (!isset($Obj['name']))        $Obj['name']        = $Probe->getName();
        if (!isset($Obj['title']))       $Obj['title']       = $Probe->getTitle();
        if (!isset($Obj['desc']))        $Obj['desc']        = $Probe->getDescription();
        if (!isset($Obj['parentId']))    $Obj['parentId']    = null; 
        if (!isset($Obj['state']))       $Obj['state']       = $Probe->getState();
        if (!isset($Obj['stateTime']))   $Obj['stateTime']   = $Probe->getStateTime();
        if (!isset($Obj['stateUser']))   $Obj['stateUser']   = $Probe->getStateUser();
        if (!isset($Obj['downtime']))    $Obj['downtime']      = $Probe->getDowntime();
        if (!isset($Obj['downtimeTime']))$Obj['downtimeTime']  = $Probe->getDowntimeTime();
        if (!isset($Obj['downtimeUser']))$Obj['downtimeUser']  = $Probe->getDowntimeUser();
        if (!isset($Obj['ack']))         $Obj['ack']         = $Probe->getAck();
        if (!isset($Obj['ackTime']))     $Obj['ackTime']     = $Probe->getAckTime();
        if (!isset($Obj['ackUser']))     $Obj['ackUser']     = $Probe->getAckUser();
        
        if (!isset($Obj['status']))      $Obj['status']      = $Probe->getStatus();
        if (!isset($Obj['statusTime']))  $Obj['statusTime']  = $Probe->getStatusTime();
        
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
            // On regarde si il y a des enfants
            // et on calcule le statut global
            if (count($Leaf['children'])>0) { 
                $result .= "subgraph cluster_$id {\n";
                $result .= "style=dashed\n";
                $result .= "bgcolor=\"".$this->Colorize($Leaf['children'])."\"\n";
                $result .= "label=\"".$Leaf['title']."\"\n";
                $result .= "$id\n";
                $result .= $this->Cluster($Leaf['children']);
                $result .= "}\n";  
            }
            else {
                $result .= "$id\n";
            }
        }
        return $result;
    }
    
    private function Colorize($Leaves) {
         $Color = [];
         $nb=0;
         foreach($Leaves as $Leaf) {
            $status = $Leaf['status'];
            if (isset($Color[$status]))
                $Color[$status]++;
            else
                $Color[$status]=1;
            $nb++;
        }
        // 100%
        foreach (['ERROR','OK','WARNING','UNKNOWN'] as $s) {
            if (isset($Color[$s]) and ($Color[$s]==$nb)) {
                return $this->CColor[$s];
            }
        }
        // Au moins un cas 
        foreach (['ERROR','WARNING'] as $s) {
            if (isset($Color[$s]) and ($Color[$s]>0)) {
                return $this->CColor['WARNING'];
            }
        }
        foreach (['UNKNOWN'] as $s) {
            if (isset($Color[$s]) and ($Color[$s]>0)) {
                return $this->CColor['UNKNOWN'];
            }
        }
        return 'black';
    }
    
    private function Node($ID,$Infos,$Fields=array(),$realtime=false) {
        
        $status = $Infos['status'];

        $label  = '<TABLE BORDER="1" CELLBORDER="0" COLOR="orange" CELLSPACING="0" BGCOLOR="'.$this->Color[$status].'">';
        if (isset($Infos['ack']) and ($Infos['ack'])) {
            $label .= '<TR><TD>'.$Infos['ackUser'].'</TD><TD><IMG SCALE="FALSE" SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/ack.png').'"/></TD><TD>'.$Infos['ackTime']->format("Y-m-d H:i:s").'</TD></TR>';
        }
        $label .= '<TR><TD ROWSPAN="3"><IMG SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/graph/'.$status).'.png"/></TD></TR>';
        if (isset($Infos['name'])) {
            $type = $Infos['objType'];
            $icon = (isset($this->Icons[$type])?'<IMG SCALE="FALSE" SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/'.$this->Icons[$type]).'.png"/>':'['.$type.']');
            $label .= '<TR><TD>'.$icon.'</TD><TD><FONT FACE="Arial-Bold">'.$Infos['name'].'</FONT></TD></TR>';
        }
        // Actif ou non ?
        if (isset($Infos['downtime']) and ($Infos['downtime'])) {
            $icon = 'off';
            $time = $Infos['downtimeTime']->format("Y-m-d H:i:s");
            $comment = '';
        }
        else {
            $icon = 'on';
            $time = '';
            if (isset($Infos['stateTime']))
                $time = $Infos['stateTime']->format("Y-m-d H:i:s");
            $comment = '';
        }
        $label .= '<TR><TD ><IMG SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/'.$icon.'.png').'"/></TD><TD>'.$time.'</TD><TD>'.$comment.'</TD></TR>';
        if (isset($Infos['desc'])) {
//            $label .= '<TR><TD ALIGN="LEFT" COLSPAN="4"><FONT FACE="Arial-Italic">'.str_replace("\x00","<br/>",$Infos['desc']).'</FONT></TD></TR>';
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
