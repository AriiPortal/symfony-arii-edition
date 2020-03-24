<?php
namespace Arii\PRTBundle\Service;

class AriiFiles
{
    private $rootpath;
    
    public function __construct ( ) {
        $this->rootpath = 'U:/aiaprd/ServeurImpression';
    }

    public function Files($dir,$Filter,$Files=[]) {
        
        // a mettre en service
        // test du contenu du repertoire 
        if ($handle = opendir($this->rootpath.'/'.$dir)) {            
            /* Ceci est la façon correcte de traverser un dossier. */
            while (false !== ($entry = readdir($handle))) {
                if (substr($entry,0,1)=='.')
                        continue;
                $file = $dir.'/'.$entry;

                // Filtre sur le nom
                $path_parts = pathinfo($entry );
                $stat = stat($this->rootpath.'/'.$file);
                // Filtre sur les propriétés
                if (isset($Filter['fromDate']) and (strtotime($Filter['fromDate'])>=$stat['ctime']))
                    continue;
                
                if (isset($Filter['toDate']) and (strtotime($Filter['toDate'])<=$stat['ctime']))
                    continue;

                // Repertoire ?
                if (is_dir($this->rootpath.'/'.$file)) {
                    array_push($Files,
                        $this->Files($file,$Filter,$Files)
                    );
                }
                
                // Nomn de fichier utilisable ?                
                $parts = explode('__',basename($entry));
                if (count($parts)<3)
                    continue;
                
                $spooler = array_pop($parts);
                $id = implode('_',$parts);
                
                $Infos = [
                    'id' => $id,
                    'spooler'=> $spooler,
                    'created'=>@date('Y-m-d H:i:s',$stat['ctime']),
                    'dirName' => $dir,
                    'fileName' => $entry
                ];
                
                if (isset($Filter['getContent']) and ($Filter['getContent']=='true'))
                    $Infos['content'] = utf8_encode(file_get_contents($this->rootpath.'/'.$file));
                array_push($Files,$Infos);
            }
            closedir($handle);
        }
        else {
            return;
        }
        return $Files;
    }
}
