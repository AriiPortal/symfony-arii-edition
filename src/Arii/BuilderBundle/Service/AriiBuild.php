<?php
namespace Arii\BuilderBundle\Service;

use Symfony\Component\Yaml\Parser;

class AriiBuild
{   
    protected $workspace;
    
    public function __construct(\Arii\CoreBundle\Service\AriiPortal $portal) {
        $this->workspace = $portal->getWorkspace();
    }
    
    
    // Une execution autosys est toujours sur le serveur
    public function Build($domainName, $templateName, $listName, $parameters) {
        
        /* Chargement des valeurs */
        $content = file_get_contents($this->workspace.'/Builder/'.$domainName.'/'.$listName.'.yml');
        # On parse le fichier 
        $yaml = new Parser();
        try {
            $Config = $yaml->parse($content);            
        } catch (ParseException $e) {
            $error = array( 'text' =>  "Unable to parse the YAML string: %s<br/>".$e->getMessage() );
            return $this->render('AriiATSBundle:Requests:ERROR.html.twig', array('error' => $error));
        }
        $template = file_get_contents($this->workspace.'/Builder/'.$domainName.'/'.$templateName.'.txt');

        $data = '';
        foreach ($Config['parameters'] as $var=>$val) {
            $template = str_replace('{{'.$var.'}}',$val,$template);
        }
        // on complete avec les paramètres internes
        $parameters['domainName'] = $domainName;
        $parameters['templateName'] = $templateName;
        $parameters['listName'] = $listName;
        foreach ($parameters as $var=>$val) {
            $template = str_replace('{{'.$var.'}}',$val,$template);
        }
        // Il en reste ?
        // on distingue generate et check ? ou check en parametre
        $n=1;
        foreach (explode("\n",$template) as $line) {
            if (($p=strpos($line,"{{"))>0) {
               $data .= "$n   $line\n";
            }
            $n++;
        }
        $data .= "\n";
        $data .= "$template";
                
        return $data;
    }    
    
}
