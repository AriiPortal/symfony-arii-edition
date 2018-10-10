<?php
// src/Sdz/BlogBundle/Service/SdzAntispam.php
 
namespace Arii\CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class AriiGraphviz
{
    private $container;
    protected $dot;

    public function __construct( ContainerInterface $container, \Arii\CoreBundle\Service\AriiPortal $portal )
    {   
        $this->container = $container;
        $this->dot =  $portal->getParameter('graphviz_dot');        
    }

    public function dot( $graph, $Options=array() ) {
        set_time_limit(30);
        
        $add_digraph =  (isset($Options['digraph']) ?$Options['digraph']:true);
        $output =   (isset($Options['output'])  ?$Options['output'] :'svg'); 
        $images =   (isset($Options['images'])  ?$Options['images'] :'/bundles/ariicore/images/silk');
        $name =     (isset($Options['name'])    ?$Options['name']   :'ARII');        
        $splines =  (isset($Options['splines']) ?$Options['splines']:'polyline');
        $rankdir =  (isset($Options['rankdir']) ?$Options['rankdir']:'TB');
        
        // Localisation des images 
        $root = $this->container->get('kernel')->getRootDir();
        $images_path = str_replace('/',DIRECTORY_SEPARATOR,$root.'/../web'.$images.'/');       
        $images_url = $this->container->get('templating.helper.assets')->getUrl($images).'/';        

        
        if ($add_digraph) {
            $digraph = "digraph $name {
fontname=arial 
fontsize=10
splines=$splines
randkir=$rankdir
node [shape=plaintext,fontname=arial,fontsize=8]
edge [shape=plaintext,fontname=arial,fontsize=8,decorate=true,compound=true]
bgcolor=white
";
            $digraph .= $graph;
            $digraph .= "}";
        }
        else {
            $digraph = $graph;
        }
                
        if (substr($this->dot,0,1)=='.')
            $this->dot = $root.'/'.$this->dot;
        $gvz_cmd = '"'.$this->dot.'" -T '.$output;      
        $out = $this->Exec($gvz_cmd, $digraph );
        
        // les erreurs devraient être indiquées au format de sortie (ex: erreur en png si output=svg)
        $response = new Response();       
        if ($output == 'svg') {
            $response->headers->set('Content-Type', 'image/svg+xml');
            // integration du script svgpan
            $head = strpos($out,'<g id="graph');
            if (!$head) {
                print $this->dot;
                print "!!!";
            }
            $xml = '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg style="width: 100%;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1">
<script xlink:href="'.$this->container->get('templating.helper.assets')->getUrl("bundles/ariicore/js/SVGPan.js").'"/>
<g id="viewport"';
            $xml .= substr($out,$head+14);
            $xml = str_replace('xlink:href="'.$images_path,'xlink:href="'.$images_url,$xml);            
            $xml = str_replace('"url(#mypath'.$images_path,'"url(#mypath'.$images_url,$xml);            
            return $response->setContent( $xml );
        }
        elseif ($output == 'pdf') {
            $response->headers->set('Content-Type', 'application/pdf');
            $response->setContent($out);
        }
        elseif ($output == 'dot') {
            $response->setContent(trim($digraph));
        }
        else {
            $response->headers->set('Content-Type', 'image/'.$output);
            $response->setContent($out);
        }
        
        return $response;
    }
    
    # A mutualiser
    private function Exec($gvz_cmd,$digraph) {
        $descriptorspec = array(
            0 => array("pipe", "r"),  // // stdin est un pipe où le processus va lire
            1 => array("pipe", "w"),  // stdout est un pipe où le processus va écrire
            2 => array("pipe", "w") // stderr est un fichier
         );

        $process = proc_open($gvz_cmd, $descriptorspec, $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], $digraph );
            fclose($pipes[0]);

            $out = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $err = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $return_value = proc_close($process);

            if ($return_value != 0) {
                # $error = "CMD: $gvz_cmd<br/>";
                $error = '';
                if ($out!='')
                    $error .= "OUT: $out<br/>"; 
                if ($err!='')
                    $error .= "$err<br/>"; 
                $error .= "EXIT: $return_value"; 
                throw new \Exception($error);
            }
        }  
        else {
            throw new \Exception("Ressource !");
        }

        return $out;
    }
}
