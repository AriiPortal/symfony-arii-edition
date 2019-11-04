<?php
// src/Sdz/BlogBundle/Service/SdzAntispam.php
 
namespace Arii\CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class AriiPlantuml
{
    private $container;
    protected $java;
    protected $dot;
    protected $plantuml;
    
    public function __construct( ContainerInterface $container, \Arii\CoreBundle\Service\AriiPortal $portal )
    {   
        $this->container = $container;
        $this->java =  $portal->getParameter('java');
        $this->java = 'java';
        $this->dot =  $portal->getParameter('graphviz_dot');
        $this->plantuml =  $portal->getParameter('plantuml');
    }

    public function graph( $graph, $Options=array() ) {
        set_time_limit(30);        
        $output =   (isset($Options['output'])  ?$Options['output'] :'svg'); 
     
        $root = $this->container->get('kernel')->getRootDir();
        $cmd = $this->java.' -jar "'.$this->plantuml.'"'
                .' -charset UTF-8'
                .' -graphvizdot "'.$this->dot.'"'
                .' -pipe '
                .' -t'.$output; 

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cmd = str_replace('/','\\',$cmd);
        } else {
            $cmd = str_replace('\\','/',$cmd);
        }
        
//        $cmd = "java -jar";
        $out = $this->Exec($cmd, $graph );

        // les erreurs devraient être indiquées au format de sortie (ex: erreur en png si output=svg)
        $response = new Response();       
        if ($output == 'svg') {
            $response->headers->set('Content-Type', 'image/svg+xml');
            return $response->setContent( $out );
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
    
    private function Exec($cmd,$graph) {
        $descriptorspec = array(
            0 => array("pipe", "r"),  // // stdin est un pipe où le processus va lire
            1 => array("pipe", "w"),  // stdout est un pipe où le processus va écrire
            2 => array("pipe", "w") // stderr est un fichier
         );

        $process = proc_open($cmd, $descriptorspec, $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], $graph );
            fclose($pipes[0]);

            $out = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $err = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $return_value = proc_close($process);

        if ($return_value != 0) {
                print "[exit $return_value]<br/>";
                print "$out<br/>";
                print "<font color='red'>$err</font>";
                print "<hr/>";
                print "<pre>$graph</pre>";
                print "<hr/>";
                print $cmd;
                exit();
            }
        }  
        else {
            print "Ressource !";
            exit();
        }

        return $out;
    }
}
