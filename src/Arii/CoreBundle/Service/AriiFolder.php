<?php
namespace Arii\CoreBundle\Service;
use Symfony\Component\Translation\Translator;

class AriiFolder {
   
    protected $session; 
    
    public function __construct(AriiSession $session) {
        $this->session = $session;
    }

    public function FilesList($basedir,$dir='',$Ext=array(),$Files=array() ) {
        if ($dh = @opendir($basedir.'/'.$dir)) {
            while (($file = readdir($dh)) !== false) {
                if (substr($file,0,1) != '.') {
                    $sub = $basedir.'/'.$dir.'/'.$file;  
                    if (is_dir($sub)) {
                        $Files = $this->FilesList($basedir,"$dir/$file",$Ext,$Files);
                    }
                    else {
                        if ($this->CheckExt($file,$Ext)==1) {
                            array_push($Files, $dir.'/'.$file );
                        }
                    }
                }
            }
            closedir($dh);
        }
        return $Files;
    }

    private function CheckExt($file,$Ext) {
        if (count($Ext)==0)
            return true;
        $ok = 0;    
        foreach ($Ext as $e) {
            if ($e == '*') {
                $ok = 1;
            }
            else {
                $l = (strlen($e)+1)*-1;
                if (substr($e,0,1)!='!') {
                    if (substr($file,$l)=='.'.$e) {
                        $ok = 1;
                    }
                }
                else {                
                    $e = substr($e,1);
                    $l++;
                    if (substr($file,$l)=='.'.$e) {
                        $ok = 0;
                    };
                }
            }
        }
        return $ok;
    }
     
    public function Tree($basedir,$dir,$Files=array(),$Ext=array() ) {
        if ($dh = @opendir($basedir.'/'.$dir)) {
            while (($file = readdir($dh)) !== false) {
                if (($file != '.') and ($file != '..')) {
                    $sub = $basedir.'/'.$dir.'/'.$file;  
                    if (is_dir($sub)) {
                        $Files = array_merge($Files,$this->Tree($basedir,"$dir/$file",$Files,$Ext));
                    }
                    else {
                        if (count($Ext)>0) {
                            $ok = 0;
                            $ext = substr($file,-3);
                            foreach ($Ext as $e) {
                                if ($ext == $e) {
                                    $ok = 1;
                                } 
                            }
                            if ($ok) {
                                array_push($Files, $sub );
                            }
                        }
                        else {
                            array_push($Files, $sub ); 
                        }
                    }
                }
            }
            closedir($dh);
        }
        else {
            array_push($Files, "!!! $basedir/$dir!");           
        }
        return $Files;
    }

    public function TreeXML($basedir,$dir,$Ext=array('job','job_chain','order','lock','process_class'),$Img=array() ) {
        $xml ='';
        if ($dh = @opendir($basedir.'/'.$dir)) {
            $Dir = array();
            $Files = array();
            while (($file = readdir($dh)) !== false) {
                if (substr($file,0,1)=='.') continue;
                $sub = $basedir.'/'.$dir.'/'.$file;
                if (($file != '.') and ($file != '..')) {
                    if (is_dir($sub)) {
                        array_push($Dir, $file );
                    }
                    else {
                        array_push($Files, $file );                
                    }
                }
            }
            closedir($dh);
            
            sort($Dir);
            foreach ($Dir as $file) {
                $xml .= '<item id="'.utf8_encode("$dir/$file/").'" text="'.utf8_encode($file).'" im0="folder.gif">';
                $xml .= $this->TreeXML($basedir,"$dir/$file",$Ext,$Img);
                $xml .= '</item>';
            }
            
            sort($Files);
            foreach ($Files as $file) {
                // on ne s'intéresse qu'aux objets principaux
                $P = explode('.',$file);
                $type = array_pop($P);
                if (in_array($type,array('xml','txt','json'))) {
                    $obj = array_pop($P);
                    if (in_array($obj,$Ext)) {
                        $f = implode('.',$P);
                        $im = $obj;
                        if (isset($Img[$obj]))
                            $im = $Img[$obj];
                        $xml .= '<item id="'.utf8_encode("$dir/$file").'" text="'.utf8_encode($f).'" im0="'.$im.'.png"/>';
                    }
                }
            }
        }
        else {
            return;
        }
        return $xml;
    }

    public function Remotes($path) {

        $Dir = array();
        if ($dh = @opendir($path)) {
            while (($file = readdir($dh)) !== false) {
                if (($file != '_all') and (substr($file,0,1) != '.') and is_dir($path.'/'.$file)) {
                    array_push($Dir, str_replace('#',':',$file) );
                }
            }
            closedir($dh);
        }
        else {
            array_push($Dir,'empty !');
        }

        sort($Dir);
        return $Dir;
    }
    
}
?>