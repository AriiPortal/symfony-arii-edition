<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class GraphsController extends Controller{

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
                
        $output = "svg";
        if ($request->query->get( 'output' ))
            $output = $request->query->get( 'output' );

        // Localisation des images 
        $root = $this->get('kernel')->getRootDir();
        $images = '/bundles/ariiack/images/silk';
        $images_path =  str_replace('/',DIRECTORY_SEPARATOR,$root.'/../web'.$images);
        $images_url = $this->container->get('templating.helper.assets')->getUrl($images);        

        // Design
        $out = 'digraph ACK {
fontname=arial 
fontsize=10
node [shape=plaintext,fontname=arial,fontsize=8,style="filled,rounded"]
edge [fontname=arial,fontsize=8,decorate=true,compound=true]
bgcolor=white
';
                // On récupère les neouds du graph
        $Graph = $this->getDoctrine()->getRepository('AriiACKBundle:Graph')->find($view);
        
        // On récupère les neouds du graph
        $Objects = $this->getDoctrine()->getRepository('AriiACKBundle:GraphObject')->findBy([
            "graph" => $Graph
        ]);
        foreach($Objects as $Object) {
            $id = $Object->getObject()->getId();
            $out .= $this->Node( $id, 
                [ 
                    'NAME' => $Object->getTitle(),
                    'DESC' => $Object->getTitle() 
                ]);
        }
        
        // Puis les liens
        foreach($Objects as $Object) {
            $Links = $this->getDoctrine()->getRepository('AriiACKBundle:Link')->findBy([
                "obj_from" => $Object->getObject()
            ]);
            foreach($Links as $Link) {
                $out .= $Object->getObject()->getId().' -> '.$Link->getObjTo()->getId()."\n";
            }
        }

        $out .=" }\n";
        // containers
        
        $graphviz = $this->container->get('arii_core.graphviz');
        $Options = array(
            'digraph' => false,
            'output' => $output,
            'output' => 'svg',
            'images' => $images
        );

        return $graphviz->dot($out,$Options);
    }
    
    private function Container() {
        
    }
    
    private function Node($ID,$Infos,$Fields=array(),$realtime=false) {
        $Icons = array(            
            'BOX_NAME'      => 'box',
            'COMMAND'       => 'shell'
        );        
        // Couleur en fonction du status
        $Color = array(
            'OK'    => 'green',
            'WARN'  => 'orange',
            'ERROR' => 'red'
        );
        
        $label  = '<TABLE BORDER="1" CELLBORDER="0" CELLSPACING="0">';
        if (isset($Infos['NAME']))
            $label .= '<TR><TD><b>((TEST '.$Infos['NAME'].'</b></TD></TR>';
        else 
            $label .= '<TR><TD><b>'.$ID.'</b></TD></TR>';
        if (isset($Infos['DESC']))
            $label .= '<TR><TD ALIGN="LEFT">'.$Infos['DESC'].'</TD></TR>';
        // Complement
        foreach ($Fields as $k) {
            if (isset($Infos[$k]) and (trim($Infos[$k])!='')) {
                $label .= '<TR><TD>';
                if (isset($Icons[$k])) {
                    $label .= '<IMG SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path).'/'.$Icons[$k].'.png"/>';            
                }
                $label .= '</TD><TD ALIGN="LEFT">'. htmlentities($Infos[$k]).'</TD></TR>';            }
        }
        
        $label .= '</TABLE>';        
        return $ID.' [label=<'.$label.'>,fillcolor=red]'."\n";        
    }
}
?>
