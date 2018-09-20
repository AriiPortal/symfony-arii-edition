<?php

namespace Arii\GraphvizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GraphvizController extends Controller
{
    public function generateAction($action = 'oss2gvz')
    {
        $request = Request::createFromGlobals();
        $return = 0;

        set_time_limit(120);

        // Localisation des images 
        $root = $this->get('kernel')->getRootDir();
        $images = '/bundles/ariigraphviz/images/silk';
        $images_path =  str_replace('/',DIRECTORY_SEPARATOR,$root.'/../web'.$images);
        $images_url = $this->container->get('templating.helper.assets')->getUrl($images);        
        
        $osj = $this->container->get('arii_gvz.osj');
        $config = $osj->getConfigPath();        
        
        $path = '.*';
        if ($request->query->get( 'path' ))
            $path = $request->query->get( 'path' );
        if ($request->query->get( 'action' ))
            $action = $request->query->get( 'action' );
        
        // Astuce
        // Si c'est un xml on utilise arii_graph sinon oss2gvz
        if (substr($path,-3)=='xml') {
            $action = 'arii_graph';
        }
        else {
            $action = 'oss2gvz';
        }

        $file = '.*';
        $rankdir = 'TB';
        $splines = 'polyline';
        $show_params = 'n';
        $show_events = 'n';
        $show_jobs = 'n';
        $show_chains = 'n';
        $show_config = 'n';

        # Options graphviz
        $Options = array();
        if ($request->query->get( 'output' ) !='') 
            $Options['output'] = $request->query->get( 'output' );
        
        if ($request->query->get( 'level' ) !='') 
            $level = $request->query->get( 'level' );
        if ($request->query->get( 'fields' ) !='') 
            $Show = explode(',',$request->query->get( 'fields' ));               
        
        # Options scripts Perl
        if ($request->query->get( 'splines' ))
            $splines = $request->query->get( 'splines' );
        if ($request->query->get( 'show_params' ))
            $show_params = $request->query->get( 'show_params' );
        if ($show_params == 'true')
            $show_params = 'y';
        else            
            $show_params = 'n';

        if ($request->query->get( 'show_events' ))
            $show_events = $request->query->get( 'show_events' );
        if ($show_events == 'true')
            $show_events = 'y';
        else           
            $show_events = 'n';

        if ($request->query->get( 'show_chains' ))
            $show_chains = $request->query->get( 'show_chains' );
        if ($show_chains == 'true')
            $show_chains = 'y';
        else           
            $show_chains = 'n';

        if ($request->query->get( 'show_jobs' ))
            $show_jobs = $request->query->get( 'show_jobs' );
        if ($show_jobs == 'true')
            $show_jobs = 'y';
        else           
            $show_jobs = 'n';

        if ($request->query->get( 'show_config' ))
            $show_config = $request->query->get( 'show_config' );
        if ($show_config == 'true')
            $show_config = 'y';
        else           
            $show_config = 'n';
        
        if ($request->query->get( 'output' ))
            $output = $request->query->get( 'output' );
        else {
            $output = "svg";        
        }

        // Si le path est un xml, l'objet devient le filtre   
        $P = explode('/',$path);
        $hotfolder = array_shift($P);
        $f = array_pop($P);
        $filtre = '';
        if (substr($f,-4)=='.xml') {
            $IF = explode('.',$f);
            $ext = array_pop($IF);
            $type = array_pop($IF);            
            switch ($type) {
                case 'order':
                case 'job_chain':
                case 'job':
                    $filtre .= ' -'.$type.'="'.implode('.',$IF).'"';
                    break;
            }
            $path = '/'.implode('/',$P);
        }
        else {
            $filtre = ' -file=.*';
            $path = implode('/',$P);
            if ($f != '')
                $path .= '/'.$f;
        }

        $osj = $this->container->get('arii_gvz.osj');
        $perlscript = $osj->getPerlscript($action);
        $config = $osj->getConfigPath();
        
        // Parametre en fonction du script 
        if ($action == 'arii_graph') {
            $cmd = $perlscript.' -config="'.$config.'" -hotfolder="'.$hotfolder.'" -images="'.$images_path.'" -path="'.$path.'" '.$filtre.' -splines="'.$splines.'" -rankdir="'.$rankdir.'" -show_params="'.$show_params.'" -show_events="'.$show_events.'" -show_chains="'.$show_chains.'" -show_jobs="'.$show_jobs.'" -show_config="'.$show_config.'"';        
        }
        else {
            $cmd = $perlscript.' -config="'.$config.'" -images="'.$images_path.'" -path="'.$path.'" -splines="'.$splines.'" -rankdir="'.$rankdir.'" -show_params="'.$show_params.'" -show_events="'.$show_events.'" -show_chains="'.$show_chains.'" -show_jobs="'.$show_jobs.'" -show_config="'.$show_config.'"';        
        }
        $out = $this->Exec($cmd);

        $graphviz = $this->container->get('arii_core.graphviz');
        $Options = array(
            'digraph' => false,
            'output' => $output,
            'images' => $images
        );

        return $graphviz->dot($out,$Options);
    }

    private function Exec($gvz_cmd,$stdin='') {
        $descriptorspec = array(
            0 => array("pipe", "r"),  // // stdin est un pipe où le processus va lire
            1 => array("pipe", "w"),  // stdout est un pipe où le processus va écrire
            2 => array("pipe", "w") // stderr est un fichier
         );
        
        $gvz_cmd = escapeshellcmd($gvz_cmd);
	# bug ?
	$gvz_cmd = str_replace('\#','#',$gvz_cmd);

        $process = proc_open($gvz_cmd, $descriptorspec, $pipes);
        if (is_resource($process)) {      
            if ($stdin!='') {
                fwrite($pipes[0], $stdin );
                fclose($pipes[0]);
            }

            $out = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $err = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $return_value = proc_close($process);

            if ($return_value != 0) {
                $error = '';
                #$error = "CMD: $gvz_cmd<br/>";
                #if ($out!='')
                #    $error .= "OUT: $out<br/>";                 
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
    
    // NON TESTE !!! 
    public function configAction()
    {
        $request = Request::createFromGlobals();
        // system('C:/xampp/htdocs/Symfony/vendor/graphviz/config.cmd');
        $return = 0;
        $output = "svg";
        if ($request->query->get( 'output' ))
            $output = $request->query->get( 'output' );
        
        $gvz_cmd = $this->container->getParameter('graphviz_config_cmd');
        $config = "c:/arii/enterprises/sos-paris/spoolers";
        $cmd = $gvz_cmd.' "'.$config.'" "'.$output.'"';

        $base =  $this->container->getParameter('graphviz_base'); 
        if ($output == 'svg') {
            exec($cmd,$out,$return);
            header('Content-type: image/svg+xml');
            foreach ($out as $o) {
                $o = str_replace('xlink:href="../../web','xlink:href="'.$base.'',$o);
                print $o;
            }
        }
        elseif ($output == 'pdf') {
            header('Content-type: application/pdf');
            system($cmd);
        }
        else {
            header('Content-type: image/'.$output);
            system($cmd);
        }
        exit();
    }

    function CleanPath($path) {
        
        // bidouille en attendant la fin de l'étude
/*        if (substr($path,0,4)=='live') 
            $path = substr($path,4);
        elseif (substr($path,0,6)=='remote') 
            $path = substr($path,6);
        elseif (substr($path,0,5)=='cache') 
            $path = substr($path,5);
*/      
        $path = str_replace('/','.',$path);
        $path = str_replace('\\','.',$path);
        $path = str_replace('#','.',$path);
        
        // protection des | sur windows
        $path = str_replace('|','^|',$path);       
        
        return $path;
    }
}
