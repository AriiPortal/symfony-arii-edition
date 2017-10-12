<?php
namespace Arii\CoreBundle\Service;

class AriiExec {
    
    protected $portal;
    protected $workspace;
    protected $status; # Status du noeud
    
    public function __construct(
            \Arii\CoreBundle\Service\AriiPortal $portal
    ) {
        $this->portal = $portal;
        $this->workspace = $portal->getWorkspace();

        set_include_path('../vendor/phpseclib' . PATH_SEPARATOR . get_include_path());
        include('Net/SSH2.php');
        define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
        include('Crypt/RSA.php');
        
    }
    
    public function ExecNodeId($id,$command,$stdin='') {
                
        // Tableau des connections
        $sql = $this->sql;        
        $qry = $sql->Select(array('N.ID','C.INTERFACE as host','C.LOGIN as user','C.PASSWORD as password','C.KEY as key'))
                .$sql->From(array('ARII_NODE N'))
                .$sql->LeftJoin('ARII_CONNECTION C',array('SHELL_ID','C.ID'))
                .$sql->Where(array('N.ID' => $id));
        
        $data = $this->db->Connector("data");                        
        $res = $data->sql->query( $qry );
        $shell = $data->sql->get_next($res);
        if (!$shell) {
            print "$id ?!";
            exit();
        }               
        
        if (($shell['password']=='') and ($shell['key']!='')) {
            $keyfile = $this->workspace."/Site/Keys/".$shell['key'];
            $shell['key'] = file_get_contents($keyfile);
        }
        
        $this->status = '!UNKNOWN';
        $result = $this->Exec($shell,$command,$stdin=''); 
        
        // On en profite pour mettre à jour le heartbeat
        $data->sql->query( "update ARII_NODE set HEARTBEAT=".time().",STATUS=\"".$this->status."\" where ID=$id" );
             
        return $result;
    }
    
    public function Exec($shell,$command,$stdin='') {
        return $this->SSH($shell,$command,$stdin);
    }
    
    public function SSH($shell,$command,$stdin='') {
        
        $host = $shell['host'];
        $user = $shell['user'];

        $ssh = new \Net_SSH2($host);
        if (isset($shell['key'])) {
            $key = new \Crypt_RSA();
            $ret = $key->loadKey($shell['key']);
            if (!$ret) {
                $this->status = '!KEY';
                throw new \Exception($ssh->getLog());
            }
        }
        elseif (isset($shell['password'])) {
            $key = $shell['password'];
        }
        else {
            $key = ''; // ?! possible ?
        }

        if (!@$ssh->login($shell['user'], $key)) {
            $this->status = '!LOGIN';
            throw new \Exception($ssh->getLog());
        }

        $this->status = 'RUNNING';
        if ($stdin=='')
            return [0, $ssh->exec("$command")];
        
        return [1,'?'];
    }
    
    
}
?>
