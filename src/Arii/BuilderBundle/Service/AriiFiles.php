<?php
namespace Arii\BuilderBundle\Service;

use Symfony\Component\Yaml\Parser;

class AriiFiles
{   
    protected $workspace;
    
    public function __construct(\Arii\CoreBundle\Service\AriiPortal $portal) {
        $this->workspace = $portal->getWorkspace();
    }
    
 
    public function Domains() {
        $path = $this->workspace.'/Builder';
        $Domains = [];
        if ($dh = opendir($path)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file,0,1)== '.') continue;
                $sub = $this->workspace.'/Builder/'.$file;  
                if (is_dir($sub)) {
                    array_push($Domains, [ 
                        "name" => $file
                    ]);
                }
            }
            closedir($dh);
        }
        return $Domains;
    }    

    public function Templates($domainName) {
        $path = $this->workspace.'/Builder/'.$domainName;
        $Templates = [];
        if ($dh = opendir($path)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file,0,1)== '.') continue;
                if (substr($file,-3)== 'txt') {
                    array_push($Templates,substr($file,0,-4));
                }
            }
            closedir($dh);
        }
        return $Templates;
    }    

    public function Lists($domainName) {
        $path = $this->workspace.'/Builder/'.$domainName;
        $Lists = [];
        if ($dh = opendir($path)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file,0,1)== '.') continue;
                if (substr($file,-3)== 'yml') {
                    array_push($Lists,substr($file,0,-4));
                }
            }
            closedir($dh);
        }
        return $Lists;
    }    
    
}
