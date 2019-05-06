<?php
namespace Arii\CoreBundle\Service;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use FOS\UserBundle\Doctrine\UserManager;
        
class AriiPortal
{
    protected $session;
    protected $userManager;
    protected $context;
    protected $translator;
    protected $router;
    protected $parameters;
    protected $rootDir;
    
    public function __construct(    AriiSession $session, 
                                    UserManager $userManager, 
                                    EntityManager $em,
                                    TranslatorInterface $translator,
                                    $router,
                                    $kernel
                                    )
    {
        $this->session = $session;
        $this->userManager = $userManager;
        $this->translator = $translator;
        $this->router = $router;
        
        $this->em = $em;
        $this->parameters = $kernel->getContainer()->getParameterBag()->all();
        $this->rootDir = $kernel->getRootDir();
    }
    
    // appel de toutes les fonctions de population
    public function setDefaults() {
        $this->setConnections();
        $this->getDefaultConnections();

        $this->setNodes();
        $this->getDefaultNodes();
        return;
    }

    public function setSessionVar($var,$val) {
        return $this->session->set($var,$val);
    }
    public function getSessionVar($var) {
        return $this->session->get($var);
    }

    // pour le futur, il faudra passer le TZ du client
    public function getTimeZone() {      
        return new \DateTimeZone(ini_get('date.timezone'));
    }
    
    /**************************************
     * Utilisateur connecté
     * 
     **************************************/
    public function getUsername() {
        $user = $this->session->getUsername();
        if ($user!='')
            return $user;
        return 'anonymous';
    }

    public function getUserInfo()
    {
        if ($this->session->get('User')!='') 
           return $this->session->get('User');

        $name = $this->session->getUsername();
        $UserInfo = $this->em->getRepository("AriiUserBundle:User")->findOneBy(array('username'=> $name ));         
        if (!$UserInfo) { # Authentifié par LDAP ?
            
            $mail = $name;
            $result = dns_get_record("localhost");
            if (isset($result[0]['host']) and ($p = strpos($result[0]['host'],'.'))) {
                $mail .= '@'.substr($result[0]['host'],$p+1);                
            }
            $User = array(
                'username' => $name,
                'first_name' => $name,
                'last_name'  => '',
                'email' => '', # + domaine ?
                'roles' => 'ROLE_USER'
            );
        }
        else {
            $User = array(
                'id' => $UserInfo->getId(),
                'username' => $UserInfo->getUsername(),
                'first_name' => $UserInfo->getFirstName(),
                'last_name'  => $UserInfo->getLastName(),
                'email' => $UserInfo->getEmail(),
                'phone_number' => $UserInfo->getPhoneNumber(),
                'pushbullet_token' => $UserInfo->getPushbulletToken(),
                'pushbullet_devices' => $UserInfo->getPushbulletDevices(),
                'notify_info' => $UserInfo->getNotifyInfo(),
                'notify_warning' => $UserInfo->getNotifyWarning(),
                'notify_alert' => $UserInfo->getNotifyAlert(),
                'roles' => str_replace('ROLE_','',implode(',',$UserInfo->getRoles())),
                'record' => $UserInfo
            );
        }
        return $this->session->set('User', $User );
    }

    /**************************************
     * Messages internes
     **************************************/                
    public function getMessages()
    {
        $Me = $this->getUserInfo(); 
        if (!isset($Me['id'])) return [];
            
        $Messages = $this->em->getRepository("AriiCoreBundle:Message")->findBy([ 'msg_to' => $Me['id'] ],[ 'msg_sent'=>'ASC' ]);
        if (!$Messages) return [];
        
        $Msg = [];
        foreach ($Messages as $Message) {
            $Msg = [ 'title' => $Message->getTitle() ];
        }    
        return $Msg;
    }
    
    /**************************************
     * Gestion d'erreur
     **************************************/                
    public function ErrorLog($Message,$Code,$File,$Line,$Trace="")
    {        
        $errorlog = new \Arii\CoreBundle\Entity\ErrorLog();
        $username = $this->getUsername(); 
        $date = new \DateTime();

        $Bundle = '?';
        if (preg_match('/(\w*)Bundle/', $File, $Matches ))
            $Bundle = $Matches[1];
        
        # Code utilisateur ?
        if ($Code>0) {
            $code_text = sprintf("%s%03d",$Message,$Code);
            $Message = $this->translator->trans($code_text,[],'errors');
        }
        else {
            $code_text = "";
        }
        $errorlog->setLogtime($date);
        $errorlog->setUsername($username);
        $errorlog->setMessage($Message);
        $errorlog->setModule($Bundle);
        $errorlog->setCode($Code);
        $errorlog->setFileName($File);
        $errorlog->setLine($Line);
        $errorlog->setTrace($Trace);
        $errorlog->setIp($_SERVER['REMOTE_ADDR']);
        
        // reouverture si l'em est ferme
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        $this->em->persist($errorlog);
        $this->em->flush();
        
        $return = "<div align='left'><h1>";
        $return .= str_replace("\n","<br/>","$code_text $Message");
        $return .= "</h1><h2>$File [$Line]</h2><code>"
                .str_replace("\n","<br/>",$Trace)
                ."</code></div>";
        return $return;
    }

    public function AuditLog( $host,$action,$status,$module,$message )
    {   
        $name = $this->session->getUsername();

        $audit = new \Arii\CoreBundle\Entity\Audit();
        $audit->setLogtime(new \DateTime());
        $audit->setUsername($name);
        
        $audit->setIp($_SERVER['REMOTE_ADDR']);
        
        $audit->setAction($action);
        $audit->setStatus($status);
        $audit->setModule($module);
        $audit->setMessage($message);
        
        $this->em->persist($audit);
        $this->em->flush();

        return 0;        
    }
    
    /**************************************
     * Parameters
     **************************************/        
    public function getParameters($filter=[]) {
        
        if ($this->session->get('Parameters')!='') 
            return $this->session->get('Parameters');
        
        $Parameters = $this->em->getRepository("AriiCoreBundle:Parameter")->findBy($filter,[ 'name'=>'ASC' ]);
        if (!$Parameters) 
            $Parameters = $this->getDefaultParameters();
        
        $Result = array();
        foreach ($Parameters as $Parameter) {
            $name = $Parameter->getBundle().'.'.$Parameter->getName();
            $Result[$name] = array(
                'id' => $Parameter->getId(),
                'bundle' => $Parameter->getBundle(),
                'name' => $Parameter->getName(),
                'value' => $Parameter->getValue(),
                'domain' => $Parameter->getDomain()
            );
        }
        return $this->session->set('Parameters', $Result );
    }

    public function resetParameters($force=false) {                    
        $this->session->set('Parameters', '');
        if ($force) $this->getDefaultParameters();
        return $this->getParameters();
    }
    
    public function getDefaultParameters() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $charset = 'ISO-8859';
        } else {
            $charset = 'UTF-8';
        }
        // Parametrage standard
        $Default = array(
            array(
                'bundle' => 'Core',
                'name'   => 'modules',
                'value'  => 'JID(ROLE_USER),DS(ROLE_USER),JOC(ROLE_USER),GVZ(ROLE_USER),Report(ROLE_USER),Admin(ROLE_ADMIN)',
                'domain'   => 'value'
            ),
            array(
                'bundle' => 'Core',
                'name'   => 'java',
                'value'  => 'java',
                'domain'   => 'exe'
            ),
            array(
                'bundle' => 'Core',
                'name'   => 'graphviz_dot',
                'value'  => 'dot',
                'domain'   => 'exe'
            ),
            array(
                'bundle' => 'Core',
                'name'   => 'perl',
                'value'  => 'perl',
                'domain'   => 'exe'
            ),
            array(
                'bundle' => 'Core',
                'name'   => 'workspace',
                'value'  => '../workspace',
                'domain'   => 'path'
            ),
            array(
                'bundle' => 'Core',
                'name'   => 'ditaa',
                'value'  => '../java/ditaa/ditaa0_9.jar',
                'domain'   => 'jar'
            ),
            array(
                'bundle' => 'Core',
                'name'   => 'plantuml',
                'value'  => '../java/plantuml/plantuml.jar',
                'domain'   => 'jar'
            ),
            array(
                'bundle' => 'Core',
                'name'   => 'charset',
                'value'  => $charset,
                'domain'   => 'value'
            )            
        );

        $Parameters = array();
        foreach ($Default as $Param) {
            // le parametre existe ?
            $Parameter = $this->em->getRepository("AriiCoreBundle:Parameter")->findOneBy(
                [   'bundle' => $Param['bundle'], 
                    'name' => $Param['name'] ]
            );
            
            // On complète mais on ne modifie pas une valeur existante
            if ($Parameter) continue;
                
            $Parameter = new \Arii\CoreBundle\Entity\Parameter();
            $Parameter->setBundle($Param['bundle']);
            $Parameter->setDomain($Param['domain']);
            $Parameter->setName($Param['name']);
            $Parameter->setValue($Param['value']);

            $this->em->persist($Parameter);
            array_push($Parameters, $Parameter);
        }
        $this->em->flush();
        return $Parameters;
    }

    public function getParameter($name, $bundle='Core') {
        $Parameters = $this->getParameters();
        $name = $bundle.'.'.$name;        
        if (!isset($Parameters[$name]['value']))
            throw new \Exception("Parameter '$bundle.$name' ?!");                
        return $Parameters[$name]['value'];
    }
    
    /**************************************
     * Modules
     **************************************/
    public function getModules() {
        
        if ($this->session->get('Modules')!='') 
            return $this->session->get('Modules');        
        
        $modules = $this->getParameter('modules');
        $Result = array();
        foreach (explode(',',$modules) as $p) {
            // Core n'est pas un module
            // compatibilité ascendante
            if ($p=='Core') continue;
            
            if (($d = strpos($p,'('))>0) {
                $module = substr($p,0,$d);
                $f = strpos($p,')',$d+1);
                $role = substr($p,$d+1,$f-$d-1);
            }
            else {
                $module = $p;
                $role = "ANONYMOUS";
            }   
            if ($module=='') continue;
            
            $Result[$module] = array(
                'BUNDLE'=>$module,
                'role' => $role, 
                'role_str' => $this->translator->trans($role), 
                'mod' => strtolower($module), 
                'name' => $this->translator->trans('module.'.$module), 
                'desc' => $this->translator->trans('text.'.$module), 
                'summary' => $this->translator->trans('summary.'.$module), 
                'img' => "$module.png",
                'url' => $this->router->generate('arii_'.$module.'_index') );
        }
        return $this->session->set('Modules',$Result);
    }

    public function getModule($name) {
        $Modules = $this->getModules();
        if (isset($Modules[$name]))
            return $Modules[$name];

        throw new \Exception('ARI',7);
    }

    /**************************************
     * Couleurs
     **************************************/    
    public function getColors($filter=['domain'=>'color'])
    {
//        if ($this->session->get('Colors')!='') 
//            return $this->session->get('Colors');
        
//        $Colors = $this->em->getRepository("AriiCoreBundle:Parameter")->findBy($filter,[ 'name'=>'ASC' ]);
//        if (!$Colors) 
            $Colors = $this->getDefaultColors();

        // Tableau 
        $Result = array();
        foreach ($Colors as $Color) {
            $name = $Color->getName();
            $Cols = explode('/',$Color->getValue());      
            $bgcolor = $Cols[0];
            if (isset($Cols[1]))
                $color = $Cols[1];
            else
                $color = 'black';                  
            $Result[$name] = array(
                'id' => $Color->getId(),
                'status' => $Color->getName(),
                'color' => $color,
                'bgcolor' => $bgcolor
            );
        }
        return $this->session->set( 'Colors', $Result);
    }

/*
PASTELS
Rouge   #fbb4ae
Bleu    #b3cde3
Vert    #ccebc5
Violet  #decbe4
Orange  #fed9a6
Jaune   #ffffcc
Marron  #e5d8bd
Rose    #fddaec
Gris    #f2f2f2
*/
    
    public function getDefaultColors() 
    {
        $Default = array(
        // SURVEILLANCE
            'OK'  => '#ccebc5',
        // TRANSFERTS
            'TRANSFERT_ABORTED' =>   '#ff66cc',
        // SERVEURS
            'EP_SHUTDOWN' => '#000000',
        // AGENTS
            'MACHINE_UNAVAILABLE' => '#ff928c',
        // JOBS
            'REFUSED' => '#fbb4ae',
            'SUCCESS'  => '#ccebc5',
            'STARTING' => '#00ff33',
            'RUNNING' => '#ffffcc',
            'FAILURE' => '#fbb4ae',
            'JOBFAILURE' => '#fbb4ae',
            'MAX_RETRYS' => '#ff0000',
            'STOPPED' => '#FF0000',
            'ERROR' => '#FF0000/yellow',
            'TERMINATED' => '#ff66cc',
            'STOPPING' => '#ffffcc',
            'INACTIVE' => 'lightgrey',
            'ACTIVATED' => '#006633/lightgrey',
            'WAIT_REPLY' => 'grey',       
            'CHK_RUN_WINDOW' => 'white',
            'STARTJOB' => '#00ff33',
            'JOB_ON_ICE' => '#ccffff',
            'ON_ICE' => '#ccffff',
            'JOB_ON_HOLD' => '#3333ff',
            'ON_HOLD' => '#ccffff',
            'QUEUED' => '#ccffff',
            'MAXRUNALARM' => '#ff',
            'MINRUNALARM' => '#fed9a6',
        // CHAINES
            "SUSPENDED" => "red",
            "CHAIN STOP." => "red",
            "SPOOLER STOP." => "red",
            "SPOOLER PAUSED" => "#bbbbbb",
            "SPOOLER RUNNING" => "#ccebc5",
            "SPOOLER LOST" => "#fbb4ae",
            "NODE STOP." => "red",
            "NODE SKIP." => "#ffffcc",
            "JOB STOP." => "#fbb4ae",        
            "SETBACK" => "lightsalmon",
            "RUNNING" => "#ffffcc",
            "ERROR" => "#fbb4ae",
            "WARNING" => "#fed9a6",
            "FAILURE" => "#fbb4ae",
            "FALSE" => "#fbb4ae",
            "ENDED" => "lightblue",
            "ON REQUEST" => "lightblue",
            "FATAL" => '#fbb4ae',        
        // GENERIQUE
            'READY' => '#ccebc5',
            'TRUE' => '#ccebc5',
            'FALSE' => '#fbb4ae',

            'NOTHING' => '#dddddd',
            'ABORT' => '#cccccc',            
            '!CONFIG' => 'red',
        // TYPE
            'I' => '#d9edf7',
            'W' => '#ffffcc',
            'A' => '#fbb4ae',
            'E' => '#FF0000',
        // ALERTE
            'OPEN' => '#fbb4ae',
            'ACKNOWLEDGED' => '#ffffcc',
            'ACK.' => '#ffffcc',
            'CLOSED' => '#ccebc5',
        // LOCK
            'FREE' => '#ccebc5',
            'USED' => '#fbb4ae',
            'MISSING'=> 'red',
        // COMMUN
            'DOWNTIME' => '#decbe4',
            'UNKNOW' => '#BBB',
            'UNKNOWN' => '#BBB'
        );

        // On stocke dans la base de données
        $Result = array();
        foreach ($Default as $name => $color) {
            $DBColor = $this->em->getRepository("AriiCoreBundle:Parameter")->findOneBy(array( 'domain'=> 'color', 'name' => $name ) );
            if (!$DBColor) {
                $DBColor = new \Arii\CoreBundle\Entity\Parameter();
            }
            $DBColor->setBundle('Core');
            $DBColor->setDomain('color');
            $DBColor->setName($name);
            $DBColor->setValue($color);
            $this->em->persist($DBColor);
            array_push($Result, $DBColor);
        }
        $this->em->flush();                    
        return $Result;
    }        

    public function getColorById($id)
    {
        $Colors = $this->getColors();
        foreach ($Colors as $name => $Color) {
            if ($Color['id']==$id) {
                return $Color;
            }
        }
        return array();
    }

    
    /**************************************
     * Pays
     **************************************/    
    public function getCountries($filter=[])
    {
        if ($this->session->get('Countries')!='') 
            return $this->session->get('Countries');
        
        $Countries = $this->em->getRepository("AriiCoreBundle:Country")->findBy($filter,[ 'name'=>'ASC' ]);
        if (!$Countries) 
            $Countries = $this->getDefaultCountries();

        // Tableau 
        $Result = array();
        foreach ($Countries as $Country) {
            $name = $Country->getName();
            $Result[$name] = array(
                'id' =>        $Country->getId(),
                'title' =>     $Country->getTitle(),
                'latitude' =>  $Country->getLatitude(),
                'longitude' => $Country->getLongitude()
            );
        }
        return $this->session->set( 'Countries', $Result);
    }

    public function getDefaultCountries() 
    {
        $countries=file_get_contents('../src/Arii/CoreBundle/Resources/data/countries.tsv');

        // On stocke dans la base de données
        $Result = array();
        foreach (split("\n",$countries) as $line) {
            list($name,$latitude,$longitude,$title) = split("\t",$line);
            $Country = $this->em->getRepository("AriiCoreBundle:Country")->findOneBy(array( 'name' => $name ) );
            if (!$Country)
                $Country = new \Arii\CoreBundle\Entity\Country();
            $Country->setName($name);
            $Country->setTitle($title);
            $Country->setLatitude($latitude);
            $Country->setLongitude($longitude);
            $this->em->persist($Country);
            
            array_push($Result, $Country);
        }
        $this->em->flush();                    
        return $Result;
    }        

    public function getCountryById($id)
    {
        $Countries = $this->getCountries();
        foreach ($Countries as $name => $Country) {
            if ($Country['id']==$id) {
                return $Country;
            }
        }
        return array();
    }
    
    /**************************************
     * Catégories
     **************************************/    
    public function getCategories($filter=[])
    { 
        if ($this->session->get('Categories')!='') 
            return $this->session->get('Categories');
        
        $Categories = $this->em->getRepository("AriiCoreBundle:Category")->findBy($filter,[ 'title'=>'ASC','name'=>'ASC' ]);
        if (!$Categories) 
            $Categories = $this->getDefaultCategories();
        
        $Result = array();
        foreach ($Categories as $Category) {
            if ($Category->getCategory()) {
                $parent_name = $Category->getCategory()->getName();;
                $parent_id = $Category->getCategory()->getId();
            }
            else {
                $parent_name = '';
                $parent_id = 0;
            }
            $name = $Category->getName();
            $Result[$name] = array( 
                'id' => $Category->getId(),
                'name' => $Category->getName(),                    
                'title' => ($Category->getTitle())?$Category->getTitle():$Category->getName(),
                'description' => $Category->getDescription(),
                'parent_id' => $parent_id,
                'parent_name' => $parent_name
            );
        }
        return $this->session->set('Categories', $Result);
    }

    public function resetCategories($force=false) {                    
        $this->session->set('Categories', '');
        if ($force) $this->getDefaultCategories();
        return $this->getCategories();
    }
    
    public function getDefaultCategories() {

        $Category = $this->em->getRepository("AriiCoreBundle:Category")->findOneBy(['name' => 'arii']);
        if (!$Category) {
            $Category = new \Arii\CoreBundle\Entity\Category();
            $Category->setName('arii');
            $Category->setTitle('Arii');
            $Category->setDescription('Racine');
            $this->em->persist($Category);        
        }
        
        $Applications = $this->em->getRepository("AriiCoreBundle:Category")->findOneBy(['name' => 'applications']);
        if (!$Applications) {
            $Applications = new \Arii\CoreBundle\Entity\Category();
            $Applications->setName('applications');
            $Applications->setTitle('Applications');
            $Applications->setDescription('Application');
            $Applications->setCategory($Category);
            $this->em->persist($Applications);        
        }

        $Exploitation = $this->em->getRepository("AriiCoreBundle:Category")->findOneBy(['name' => 'exploitation']);
        if (!$Exploitation) {
            $Exploitation = new \Arii\CoreBundle\Entity\Category();
            $Exploitation->setName('exploitation');
            $Exploitation->setTitle('Exploitation');
            $Exploitation->setDescription('Exploitation');
            $Exploitation->setCategory($Category);
            $this->em->persist($Exploitation);        
        }
        
        $FileTransfers = $this->em->getRepository("AriiCoreBundle:Category")->findOneBy(['name' => 'file_transfers']);
        if (!$FileTransfers) {
            $FileTransfers = new \Arii\CoreBundle\Entity\Category();
            $FileTransfers->setName('file_transfers');
            $FileTransfers->setTitle('Transferts de fichiers');
            $FileTransfers->setDescription('Transferts de fichiers');
            $FileTransfers->setCategory($Category);
            $this->em->persist($FileTransfers);        
        }
        
        $Network = $this->em->getRepository("AriiCoreBundle:Category")->findOneBy(['name' => 'network']);
        if (!$Network) {
            $Network = new \Arii\CoreBundle\Entity\Category();
            $Network->setName('network');
            $Network->setTitle('Réseau');
            $Network->setDescription('Réseau');
            $Network->setCategory($Category);
            $this->em->persist($Network);        
        }

        $Nodes = $this->em->getRepository("AriiCoreBundle:Category")->findOneBy(['name' => 'nodes']);
        if (!$Nodes) {
            $Nodes = new \Arii\CoreBundle\Entity\Category();
            $Nodes->setName('nodes');
            $Nodes->setTitle('Noeuds');
            $Nodes->setDescription('Noeuds du réseau.');
            $Nodes->setCategory($Network);
            $this->em->persist($Nodes);        
        }
        
        $this->em->flush();        
        return array($Category,$Applications,$Exploitation,$FileTransfers,$Network,$Nodes);
    }

    public function getCategoryById($id)
    {
        $Categories = $this->getCategories();
        foreach ($Categories as $name => $Category) {
            if ($Category['id']==$id)
                return $Category;
        }
        return array();
    }
    
    public function getCategoryByName($name)
    {
        $Categories = $this->getCategories();
        if (isset($Categories[$name])) {
            return $Categories[$name];
        }
        return array();
    }

    /**************************************
     * Applications
     **************************************/    
    public function getApplications($filter=[])
    {
        if ($this->session->get('Applications')!='') 
            return $this->session->get('Applications');
        
        $Applications = $this->em->getRepository("AriiCoreBundle:Application")->findBy($filter,[ 'title'=>'ASC', 'name'=>'ASC' ]);
        if (!$Applications) 
            $Applications = $this->getDefaultApplications();

        // Tableau 
        $Result = array();
        foreach ($Applications as $Application) {
            $name = $Application->getName();
            $Result[$name] = array(
                'id' => $Application->getId(),
                'name' => $Application->getName(),
                'title' => $Application->getTitle(),
                'description' => $Application->getDescription(),
                'contact' => $Application->getContact(),
                'active' => $Application->getActive(),
                'category' => ($Application->getCategory()?$Application->getCategory()->getTitle():''),
                'category_id' => ($Application->getCategory()?$Application->getCategory()->getId():'')           
            );
        }
        return $this->session->set( 'Applications', $Result);
    }

    public function getDefaultApplications() 
    {
        $Default = array(
            // EN FR ES DE CN 
            array(  'ARI', 'Ari\'i', 'Portail'),
            array(  'SFY', 'Symfony', 'Framework'),
            array(  'OJS', 'Open Source JobScheduler', 'Workload')
        );
        
        list($Category) =  $this->getDefaultCategories();
        // Mets à jour les enregistrements manquants
        $Result = array();
        foreach ($Default as $Record) {
            $Application = $this->em->getRepository("AriiCoreBundle:Application")->findOneBy([ 'name' => $Record[0] ]);

            if (!$Application)
                $Application = new \Arii\CoreBundle\Entity\Application();
            
            $Application->setName($Record[0]);
            $Application->setTitle($Record[1]);
            $Application->setDescription($Record[2]);
            $Application->setCategory($Category);
            $this->em->persist($Application);
            
            array_push($Result,$Application);
        }
        $this->em->flush();
        
        return $Result;
    }        

    public function getApplicationById($id)
    {
        $Applications = $this->getApplications();
        foreach ($Applications as $name => $Application) {
            if ($Application['id']==$id)
                return $Application;
        }
        return array();
    }
        
    public function resetApplications($force=false) {                    
        $this->session->set('Applications', '');
        if ($force) $this->getDefaultApplications();
        return $this->getApplications();
    }

    /**************************************
     * Rules
     **************************************/    
    public function getRules($filter=[])
    {
        if ($this->session->get('Rules')!='') 
            return $this->session->get('Rules');
        
        $Rules = $this->em->getRepository("AriiCoreBundle:Rule")->findBy($filter,[ 'in_job'=>'ASC' ]);
        if (!$Rules) 
            $Rules = $this->getDefaultRules();

        // Tableau 
        $Result = array();
        foreach ($Rules as $Rule) {
            $name = $Rule->getInJob();
            $Result[$name] = array(
                'id' => $Rule->getId(),
                'InJob' => $Rule->getInJob(),
                'OutApp' => $Rule->getOutApp(),
                'OutEnv' => $Rule->getOutEnv(),
            );
        }
        return $this->session->set( 'Rules', $Result);
    }

    public function getDefaultRules() 
    {
        $Default = array(
            // EN FR ES DE CN 
            array(  'ARIT%', 'ARII', 'T'),
            array(  'ARIP%', 'ARII', 'P')
        );
        
        // Mets à jour les enregistrements manquants
        $Result = array();
        foreach ($Default as $Record) {
            $Rule = $this->em->getRepository("AriiCoreBundle:Rule")->findOneBy([ 'in_job' => $Record[0] ]);

            if (!$Rule)
                $Rule = new \Arii\CoreBundle\Entity\Rule();
            
            $Rule->setInJob($Record[0]);
            $Rule->setOutApp($Record[1]);
            $Rule->setOutEnv($Record[2]);
            $this->em->persist($Rule);
            
            array_push($Result,$Rule);
        }
        $this->em->flush();
        
        return $Result;
    }        

    public function resetRules($force=false) {                    
        $this->session->set('Rules', '');
        if ($force) $this->getDefaultRules();
        return $this->getRules();
    }

       
    /**************************************
     * Domains
     * Remplacé par les tags
     **************************************/    
    public function getDomains($filter=[])
    {

        if ($this->session->get('Domains')!='') 
            return $this->session->get('Domains');
        
        $Domains = $this->em->getRepository("AriiCoreBundle:Domain")->findBy($filter,[ 'name'=>'ASC', 'title'=>'ASC' ]);
        if (!$Domains) 
            $Domains = $this->getDefaultDomains();

        // Tableau 
        $Result = array();
        foreach ($Domains as $Domain) {
            $name = $Domain->getName();
            $Result[$name] = array(
                'id' => $Domain->getId(),
                'name' => $Domain->getName(),
                'title' => $Domain->getTitle(),
                'description' => $Domain->getDescription()
            );
        }
        return $this->session->set( 'Domains', $Result);
    }

    public function getDefaultDomains() 
    {
        $Default = array(
            // EN FR ES DE CN 
            array(  'USER', 'Users', 'Utilisateurs' ),
            array(  'DATA', 'Data', 'Données' ),
            array(  'MAIL', 'Mail', 'Messagerie' ),
        );
        
        // Mets à jour les enregistrements manquants
        $Result = array();
        foreach ($Default as $Record) {
            $Domain = $this->em->getRepository("AriiCoreBundle:Domain")->findOneBy([ 'name' => $Record[0] ]);

            if (!$Domain)
                $Domain = new \Arii\CoreBundle\Entity\Domain();
            
            $Domain->setName($Record[0]);
            $Domain->setTitle($Record[1]);
            $Domain->setDescription($Record[2]);
            $this->em->persist($Domain);
            
            array_push($Result,$Domain);
        }
        $this->em->flush();
        
        return $Result;
    }        

    public function resetDomains($force=false) {                    
        $this->session->set('Domains', '');
        if ($force) $this->getDefaultDomains();
        return $this->getDomains();
    }
    
    /**************************************
     * Options de liste
     **************************************/    
    public function getOptions($filter=[]){
  
        if ($this->session->get('Options')!='') 
            return $this->session->get('Options');
 
        $Options = $this->em->getRepository("AriiCoreBundle:Option")->findBy($filter,[ 'name'=>'ASC' ]);
        if (!$Options)
            $Options = $this->getDefaultOptions();

        $Result = array();
        foreach ($Options as $Option) {            
            $name = $Option->getName();
            $Result[$name] = array(
                'title' => $this->translator->trans($Option->getDomain().'.'.$Option->getName(),[],'internal'),
                'id' => $Option->getId(),
                'domain' => $Option->getDomain(),
                'name' => $name
            );
        }
        return $this->session->set('Options', $Result);
    }
    
    public function setDefaultOptions() {
        return $this->getDefaultOptions();
    }
    
    public function getDefaultOptions() {
        $Default = array(
            // EN FR ES DE CN 
            array( 'component', 'webserver',    'Web server' ),
            array( 'component', 'jobscheduler', 'Job scheduler' ),
            array( 'component', 'database',     'Database' ),
            array( 'component', 'mailserver',   'Mail server' ),
            array( 'component', 'os',           'System' ),

            # Systeme
            array( 'os', 'windows', 'Windows' ),
            array( 'os', 'unix'   , 'Unix' ),
            array( 'os', 'iseries', 'iSeries' ),
            
            # Type os
            array( 'unix', 'debian', 'Debian' ),
            array( 'unix', 'centos', 'CentOS' ),
            array( 'unix', 'redhat', 'RedHat' ),
            array( 'unix', 'sunos',  'Sun OS' ),
            array( 'unix', 'aix',    'IBM AIX' ),
            
            array( 'windows', 'win_vista', 'Windows Vista' ),
            array( 'windows', 'win_xp',    'Windows XP' ),
            array( 'windows', 'win_10', 'Windows 10' ),
            
            # Ordonnanceur
            array( 'jobscheduler', 'ojs', 'Open Source JobScheduler' ),
            array( 'jobscheduler', 'ats', 'Autosys' ),
            array( 'jobscheduler', 'ctm', 'Control-M' ),
            array( 'jobscheduler', 'uni', '$ Univers' ),
            array( 'jobscheduler', 'run', 'Rundeck' ),
            
            # Serveur Web
            array( 'webserver', 'apache', 'Apache' ),
            array( 'webserver', 'iis', 'IIS' ),
            
            array( 'database', 'mysql',    'MySQL'),
            array( 'database', 'oracle',   'Oracle'),
            array( 'database', 'postgres', 'PostGres'),
            array( 'database', 'sqlserver','SqlServer'),
            
            array( 'protocol', 'ftp', 'FTP'),
            array( 'protocol', 'sftp', 'SFTP'),
            array( 'protocol', 'ftps', 'FTPS'),
            array( 'protocol', 'scp',  'SCP'),
            array( 'protocol', 'copy', 'Copy'),
            array( 'protocol', 'ssh',  'SSH'),
            array( 'protocol', 'http', 'HTTP'),
            array( 'protocol', 'https', 'HTTPS'),
            array( 'protocol', 'smtp', 'SMTP'),
            
            array( 'env', 'T', 'Test'),
            array( 'env', 'D', 'Development'),
            array( 'env', 'R', 'Recette'),
            array( 'env', 'I', 'Integration'),
            array( 'env', 'P', 'Production'),
            array( 'env', 'U', 'UAT'),

            array( 'role', 'primary', 'Primary'),
            array( 'role', 'backup',  'Backup'),
            array( 'role', 'agent',   'Agent'),
            array( 'role', 'client',  'Client'),
            array( 'role', 'supervisor','Supervisor'),
            array( 'role', 'eventmanager','Event manager'),
            array( 'role', 'tiebreaker', 'Tie breaker'),
            array( 'role', 'launcher', 'Launche'),
            array( 'role', 'scheduler', 'Scheduler'),
            array( 'role', 'execution', 'Execution'),
            array( 'role', 'communication', 'Communication'),
            
            array( 'job_type', 'shell',        'Shell'),
            array( 'job_type', 'filetransfer', 'File transfer'),
            array( 'job_type', 'filewatcher',  'File watcher'),
            array( 'job_type', 'box',          'Box'),

            array( 'shell', 'bash', 'Bash'),
            array( 'shell', 'cmd',  'Cmd'),
            
            array( 'language', 'perl',          'Perl'),
            array( 'language', 'python',        'Python'),
            array( 'language', 'java',          'Java'),
            array( 'language', 'javascript',    'Javascript'),
            
            array( 'event_type', 'sys',    'System'),
            array( 'event_type', 'usr',    'User'),
            array( 'event_type', 'sec',    'Security'),
            array( 'event_type', 'app',    'Application'),
            array( 'event_type', 'hdw',    'Hardware'),            
            array( 'event_type', 'net',    'Network'),            
            array( 'event_type', 'upd',    'Update'),            
            array( 'event_type', 'tst',    'Test')            
        );
        
        // Mets à jour les enregistrements manquants
        $Result = array();
        foreach ($Default as $Record) {
            $Option = $this->em->getRepository("AriiCoreBundle:Option")->findOneBy([ 'name' => $Record[1] ]);
            if ($Option) continue;
            
            # On cherche le père
            $Parent = $this->em->getRepository("AriiCoreBundle:Option")->findOneBy([ 'parent' => $Record[0] ]);            
            
            $Option = new \Arii\CoreBundle\Entity\Option();
            $Option->setParent($Parent);
            $Option->setDomain($Record[0]); // obsolete
            
            $Option->setName($Record[1]);
            $Option->setTitle($Record[2]);
            
            $this->em->persist($Option);
            
            array_push($Result,$Option);
        }
        $this->em->flush();
        
        return $Result;
    }

    public function resetOptions($force=false) {                    
        $this->session->set('Options', '');
        if ($force) $this->getDefaultOptions();
        return $this->getOptions();
    }
    
    public function getOptionsByDomain($domain) {
        $AllOptions = $this->getOptions();
        $Options = array();
        foreach ($AllOptions as $O) {
            if ($O['domain']!=$domain) continue;            
            array_push($Options,$O);
        }
        return $Options;
    }

    /**************************************
     * Repos
     **************************************/     
    public function getRepoById($id) {
        $Repo = $this->em->getRepository("AriiCoreBundle:Repo")->findOneBy($id);
        if (!$Repo)
            return;
        return $this->getRepoByName($Repo->getName()) ;
    }
    
    public function getRepoByName($name) {
        $Repos = $this->getRepos();
        if (isset($Repos[$name]))
            return $Repos[$name];
        return;
    }

    public function getReposByType($type='') {
        $Repos = $this->getRepos();
        $Filtered = [];
        $type = strtolower($type);
        foreach ($Repos as $k=>$Repo) {
            if ($Repo['type']==$type)
                array_push($Filtered,$Repo);
        }
        return $Filtered;
    }
    
    public function getRepos($filter=[])
    { 
        if ($this->session->get('Repos')!='') 
            return $this->session->get('Repos');

        $Repos = $this->em->getRepository("AriiCoreBundle:Repo")->findBy($filter,[ 'title'=>'ASC','name'=>'ASC' ]);
        if (!$Repos) 
            $Repos = $this->getDefaultRepos();
        
        $Result = array();
        foreach ($Repos as $Repo) {
            $name = $Repo->getName();
            $Result[$name] =  array( 
                'id' => $Repo->getId(),
                'name' =>  $name,
                'title' => $Repo->getTitle(),
                'type' =>  $Repo->getType(),
                'description' => $Repo->getDescription()
            );
        }        
        return $this->session->set('Repos', $Result);
    }

    public function resetRepos($force=false) {                    
        $this->session->set('Repos', '');
        if ($force) $this->getDefaultRepos();
        return $this->getRepos();
    }
        
    public function setRepos() {
        $this->session->set('Repos','');         
        return $this->getRepos();
    }
    
    public function getDefaultRepos() {
        $Repo = $this->em->getRepository("AriiCoreBundle:Repo")->findOneBy(['name' => 'ojs_db']);
        if (!$Repo) {
            $Repo = new \Arii\CoreBundle\Entity\Repo();
            $Repo->setName('ojs_db');
            $Repo->setTitle('DB JobScheduler');
            $Repo->setDescription('DB JobScheduler');
            $Repo->setType('ojs');
            $this->em->persist($Repo);        
        }
        $this->em->flush();        
        return array($Repo);
    }
    
    /**************************************
     * Sites
     **************************************/     
    public function getSiteById($id) {
        $Site = $this->em->getRepository("AriiCoreBundle:Site")->findOneBy($id);
        if (!$Site)
            return;
        return $this->getSiteByName($Site->getName()) ;
    }
    
    public function getSiteByName($name) {
        $Sites = $this->getSites();
        if (isset($Sites[$name]))
            return $Sites[$name];
        return;
    }
    
    public function getSites($filter=[])
    { 
        if ($this->session->get('Sites')!='') 
            return $this->session->get('Sites');

        $Sites = $this->em->getRepository("AriiCoreBundle:Site")->findBy($filter,[ 'title'=>'ASC','name'=>'ASC' ]);
        if (!$Sites) 
            $Sites = $this->getDefaultSites();
        
        $Result = array();
        foreach ($Sites as $Site) {
            $name = $Site->getName();
            $Result[$name] =  array( 
                'id' => $Site->getId(),
                'name' => $name,
                'title' => $Site->getTitle(),
                'description' => $Site->getDescription(),
                'address' => $Site->getAddress(),
                'zipcode' => $Site->getZipCode(),
                'city' => $Site->getCity(),
                'latitude' => $Site->getLatitude(),
                'longitude' => $Site->getLongitude()
            );
        }
        
        return $this->session->set('Sites', $Result);
    }

    public function resetSites($force=false) {                    
        $this->session->set('Sites', '');
        if ($force) $this->getDefaultSites();
        return $this->getSites();
    }
        
    public function setSites() {
        $this->session->set('Sites','');         
        return $this->getSites();
    }
    
    public function getDefaultSites() {
        $Site = $this->em->getRepository("AriiCoreBundle:Site")->findOneBy(['name' => 'local']);
        if (!$Site) {
            $Site = new \Arii\CoreBundle\Entity\Site();
            $Site->setName('local');
            $Site->setTitle('Site local');
            $Site->setDescription('Localisation du serveur');
            $Site->setTimezone(ini_get('date.timezone'));
            $Site->setLatitude(ini_get('date.default_latitude'));
            $Site->setLongitude(ini_get('date.default_longitude'));        
            $this->em->persist($Site);        
        }
        $this->em->flush();        
        return array($Site);
    }

    /**************************************
     * Connections
     **************************************/
    public function getConnectionById($id) {
        $Connection = $this->em->getRepository("AriiCoreBundle:Connection")->findOneBy($id);
        if (!$Connection)
            return;
        return $this->getConnectionByName($Connection->getName()) ;
    }
    
    public function getConnectionByName($name) {
        $Connections = $this->getConnections();
        if (isset($Connections[$name]))
            return $Connections[$name];
        return;
    }
   
    public function getConnectionsBy($id,$value) {
        $Connections = $this->getConnections();
        $Result = array();
        foreach ($Connections as $name => $Connection) {
            if ($Connection[$id]==$value) {
                $Result[$name] = $Connection;
            }                       
        }
        return $Result;
    }
    
    public function GetConnections($filter=[])
    {
        if ($this->session->get('Connections')!='') 
           return $this->session->get('Connections');
        
        $Connections = $this->em->getRepository("AriiCoreBundle:Connection")->findBy($filter,[ 'title'=>'ASC','name'=>'ASC' ]);
        if (!$Connections) 
            $Connections = $this->getDefaultConnections();

        // Tableau 
        $Result = array();
        foreach ($Connections as $Connection) {
            $name = $Connection->getName();  
            $Result[$name] = array(
                'id' => $Connection->getId(),
                'auth_method' => $Connection->getAuthMethod(),
                'backup' => $Connection->getBackup(),
                'description' => $Connection->getDescription(),
                'database' => $Connection->getDatabase(),  
                'domain' => $Connection->getDomain(),
                'driver' => ($Connection->getDriver()!=''?$Connection->getDriver():$Connection->getProtocol()), // Pour la db, Le driver est une précision du protocol
                'env' => $Connection->getEnv(),
                'host' => $Connection->getHost(),
                'instance' => $Connection->getInstance(),
                'interface' => $Connection->getInterface(),
                'login' => $Connection->getLogin(),
                'name' => $Connection->getName(),
                'owner' => $Connection->getOwner(),
                'port' => $Connection->getPort(),
                'protocol' => ($Connection->getProtocol()!=''?$Connection->getProtocol():$Connection->getDriver()), // Pour la db, Le driver est une précision du protocol
                'title' => $Connection->getTitle(),
                'password' => $Connection->getPassword(),
                'path' => $Connection->getPath(),
                'private_key' => $Connection->getKey(),    
                'service' => $Connection->getService()
            );
        }
        
        return $this->session->set('Connections', $Result);
    }

    public function resetConnections($force=false) {                    
        $this->session->set('Connections', '');
        if ($force) $this->getDefaultConnections();
        $this->resetDatabase();
        return $this->getConnections();
    }
    
    private function getDefaultConnections() {
        // Il y a eu moins nu jobscheduler en tache de fond Ari'i
        $AriiScheduler = $this->em->getRepository("AriiCoreBundle:Connection")->findOneBy(['name' => 'arii_scheduler']);
        if (!$AriiScheduler) {
            $AriiScheduler = new \Arii\CoreBundle\Entity\Connection();
            $AriiScheduler->setName('arii_scheduler');
            $AriiScheduler->setTitle('Arii Scheduler');
            $AriiScheduler->setDescription('Connexion pour la communication avec l\'ordonnanceur interne.');
            $AriiScheduler->setProtocol('ojs');
            $AriiScheduler->setHost('localhost');
            $AriiScheduler->setInterface('127.0.0.1');
            $AriiScheduler->setPort('44444');
            $AriiScheduler->setDomain('jobscheduler');
            $this->em->persist($AriiScheduler);
        }
        
        // La base de données local
        $AriiDB = $this->em->getRepository("AriiCoreBundle:Connection")->findOneBy(['name' => 'arii_db']);
        if (!$AriiDB) {
            $AriiDB = new \Arii\CoreBundle\Entity\Connection();
            $AriiDB->setName('arii_db');
            $AriiDB->setTitle('Arii DB');
            $AriiDB->setProtocol($this->parameters['database_driver']);
            $AriiDB->setDriver($this->parameters['database_driver']);
            $AriiDB->setHost($this->parameters['database_host']);
            $AriiDB->setDatabase($this->parameters['database_name']);
            $AriiDB->setInstance($this->parameters['database_name']);
            $AriiDB->setDescription('Connexion pour la base de données Arii.');            
            $AriiDB->setInterface(gethostbyname($this->parameters['database_host']));
            if (isset($this->parameters['database_port']))
                $AriiDB->setPort($this->parameters['database_port']);
            $AriiDB->setLogin($this->parameters['database_user']);
            $AriiDB->setPassword($this->parameters['database_password']);
            $AriiDB->setDomain('database');
            if (isset($this->parameters['database_service']))
                $AriiDB->setService($this->parameters['database_service']);
            $this->em->persist($AriiDB);       
        }
        
        // La messagerie
        $AriiMail = $this->em->getRepository("AriiCoreBundle:Connection")->findOneBy(['name' => 'arii_mail']);
        if (!$AriiMail) {
            $AriiMail = new \Arii\CoreBundle\Entity\Connection();
            $AriiMail->setName('arii_mail');
            $AriiMail->setTitle('Arii Mail');
            $AriiMail->setDescription('Connexion pour l\'envoi de mail.');
            $AriiMail->setProtocol($this->parameters['mailer_transport']);
            $AriiMail->setHost($this->parameters['mailer_host']);
            $AriiMail->setInterface(gethostbyname($this->parameters['mailer_host']));
            if (isset($this->parameters['mailer_port'])) $AriiMail->setPort($this->parameters['mailer_port']);
            $AriiMail->setLogin($this->parameters['mailer_user']);
            $AriiMail->setPassword($this->parameters['mailer_password']);
            $AriiMail->setDomain('mail');
            $AriiMail->setService(false);
            $this->em->persist($AriiMail);               
        }
        
        // L'accès web
        $AriiWeb = $this->em->getRepository("AriiCoreBundle:Connection")->findOneBy(['name' => 'arii_web']);
        if (!$AriiWeb) {
            $AriiWeb = new \Arii\CoreBundle\Entity\Connection();
            $AriiWeb->setName('arii_web');
            $AriiWeb->setTitle('Arii Web');
            $AriiWeb->setDescription('Connexion pour l\'interface web utilisateur.');
            $AriiWeb->setProtocol('http');
            // Attention aux variables non definies dans le serveur web interne (php -S)
            if (isset($_SERVER['SERVER_NAME'])) 
                $host = $_SERVER['SERVER_NAME'];
            else
                $host = 'localhost';
            if (isset($_SERVER['SERVER_ADDR']))
                $AriiWeb->setInterface($_SERVER['SERVER_ADDR']);
            else 
                $AriiWeb->setInterface(gethostbyname($host));
            if (isset($_SERVER['SERVER_PORT']))
                $AriiWeb->setPort($_SERVER['SERVER_PORT']);
            else 
                $AriiWeb->setPort(80);
            $AriiWeb->setDomain('web');
            $AriiWeb->setService(false);
            $this->em->persist($AriiWeb);
        }

        // L'accès fs
        $AriiFS = $this->em->getRepository("AriiCoreBundle:Connection")->findOneBy(['name' => 'arii_config']);
        if (!$AriiFS) {
            $AriiFS = new \Arii\CoreBundle\Entity\Connection();
            $AriiFS->setName('arii_config');
            $AriiFS->setTitle('Arii Config');
            $AriiFS->setDescription('Connexion pour l\'accès à la configuration.');
            $AriiFS->setProtocol('local');
            $AriiFS->setHost('localhost');
            $AriiFS->setInterface('127.0.0.1');
            $AriiFS->setPath('../../jobscheduler/arii/config');
            $AriiFS->setDomain('fs');
            $AriiFS->setService(false);
            $this->em->persist($AriiFS);
        }
        
        $this->em->flush();        
        return array($AriiScheduler,$AriiDB,$AriiMail,$AriiWeb,$AriiFS);
    }

    /**************************************
     * Nodes
     **************************************/
    public function getNodeById($id) {
        $Node = $this->em->getRepository("AriiCoreBundle:Node")->findOneBy($id);
        if (!$Node)
            throw new \Exception('ARI',5);
        return $this->getNodeByName($Node->getName()) ;
    }

    public function getNodeByName($name,$exception=true) {
        $Node = $this->getNodes();        
        if (isset($Node[$name]))
            return $Node[$name];
        if ($exception)            
            throw new \Exception('ARI',5);
        return;
    }

    public function getNodesBy($id,$value) {
        $Nodes = $this->getNodes();
        $Result = array();
        foreach ($Nodes as $name => $Node) {
            if ($Node[$id]==$value) {
                $Result[$name] = $Node;
            }                       
        }
        return $Result;
    }

    public function setNodes() {
        $this->session->set('Nodes','');         
        return $this->getNodes();
    }

    public function setNode($name) {
        $this->session->set('Node',$name);         
        return $this->getNodeByName($name);
    }

    public function getNode() {
        $name = $this->session->get('Node');         
        return $this->getNodeByName($name);
    }

    public function getNodes($filter=[])
    { 
        if ($this->session->get('Nodes')!='') 
           return $this->session->get('Nodes');
        
        $Nodes = $this->em->getRepository("AriiCoreBundle:Node")->findBy($filter,[ 'title'=>'ASC','name'=>'ASC' ]);
        if (!$Nodes) 
            $Nodes = $this->getDefaultNodes();
        
        // Tableau 
        $Result = array();
        foreach ($Nodes as $Node) {
            $category=$category_id='';
            if ($Node->getCategory()) {
                $category=$Node->getCategory()->getTitle();
                $category_id=$Node->getCategory()->getId();
            }
            $site=$site_id='';
            if ($Node->getSite()) {
                $site=$Node->getSite()->getTitle();
                $site_id=$Node->getSite()->getId();
            }
            $cluster=$cluster_id='';
            if ($Node->getCluster()) {
                $cluster=$Node->getCluster()->getTitle();
                $cluster_id=$Node->getCluster()->getId();
            }

            $name = $Node->getName();            
            $Result[$name] = array(
                'id' => $Node->getId(),
                'category' => $category,
                'name' => $name,
                'title' => $Node->getTitle(),
                'description' => $Node->getDescription(),
                'component' => $Node->getComponent(),
                'vendor' => $Node->getVendor(),
                'component_str' => $this->translator->trans('component.'.$Node->getComponent(),[],'internal'),
                'vendor_str' => $this->translator->trans($Node->getComponent().'.'.$Node->getVendor(),[],'internal'),
                'site' => $site,
                'status' => $Node->getStatus(),
                'status_date' => $Node->getStatusDate()->format('Y-m-d h:i:s'),
                'category_id' => $category_id,
                'site_id' => $site_id,
                'cluster_id' => $cluster_id,
                'Connections' => $this->GetNodeConnections($this->GetConnectionsByNode($Node))
            );
        }
        return $this->session->set('Nodes', $Result);            
    }

    public function resetNodes($force=false) {                    
        $this->session->set('Nodes', '');
        if ($force) $this->getDefaultNodes();
        return $this->getNodes();
    }
    
    public function GetConnectionsByNode($Node)
    { 
        $NodeConnections = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findBy( [ 'node' => $Node ], [ 'priority' => 'ASC' ] );
        if (!$NodeConnections)
            return array();     

        $Connections = array();
        $p2 = 0;
        foreach ($NodeConnections as $NC) {
            $priority = $NC->getPriority();
            $Connection = $NC->getConnection();
            if (isset($Connections[$priority])) {
                $p2++;
                $priority = $priority.'.'.$p2;
            }
            else {
                $p2 = 0;
            }
            $Connections[$priority] = $this->em->getRepository("AriiCoreBundle:Connection")->find( $Connection );
        }    
        ksort($Connections);
        return $Connections;
    }
    
    // Connexions d'un noeud
    public function GetNodeConnectionsById($id)
    {
        $Node = $this->em->getRepository("AriiCoreBundle:Node")->findOneBy($id);
        $Connections = $this->GetConnectionsByNode($Node);        
        return $this->GetNodeConnections($Connections);
    }
    
    private function GetNodeConnections($Connections) {
        // Tableau 
        $Result = array();
        foreach ($Connections as $Connection) {
            $name = $Connection->getName();
            $Result[$name] = array(
                'id' => $Connection->getId(),
                'name' => $Connection->getName(),
                'title' => $Connection->getTitle(),
                'description' => $Connection->getDescription(),
                'domain' => $Connection->getDomain(),
                'backup' => $Connection->getBackup(),
                'host' => $Connection->getHost(),
                'interface' => $Connection->getInterface(),
                'port' => $Connection->getPort(),
                'protocol' => $Connection->getProtocol(),
                'login' => $Connection->getLogin(),
                'env' => $Connection->getEnv(),
                'auth_method' => $Connection->getAuthMethod(),
                'password' => $Connection->getPassword(),
                'key' => $Connection->getKey(),    
                'driver' => $Connection->getDriver(),  
                'database' => $Connection->getDatabase(),  
                'path' => $Connection->getPath(),
                'instance' => $Connection->getInstance(),
                'service' => $Connection->getService()
            );
        }
        return $Result;            
    }
    
    private function getDefaultNodes() {
        // Initialisation des connexions
        list($AriiSite) =  $this->getDefaultSites();
        list($Category,$Applications,$FileTransfers,$Network,$Nodes) =  $this->getDefaultCategories();
        list($Scheduler,$DB,$Mail,$Web,$Config) = $this->getDefaultConnections();

        // Noeuds
        $AriiWeb = $this->em->getRepository("AriiCoreBundle:Node")->findOneBy(['name' => 'arii_web']);
        if (!$AriiWeb) {
            $AriiWeb = new \Arii\CoreBundle\Entity\Node();
            $AriiWeb->setName('arii_web');
            $AriiWeb->setTitle('Arii Web');
            $AriiWeb->setDescription('Serveur Web');      
            $AriiWeb->setComponent('webserver');
            $AriiWeb->setVendor('apache');
            $AriiWeb->setStatusDate(new \DateTime());      
            $AriiWeb->setStatus('default');
            $AriiWeb->setSite($AriiSite);
            $AriiWeb->setCategory($Nodes);
            $this->em->persist($AriiWeb);
            
           // On update le Node<->Connection
            $AriiWebWeb = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findOneBy([ 'node' => $AriiWeb, 'connection' => $Web]);
            if (!$AriiWebWeb)
                $AriiWebWeb = new \Arii\CoreBundle\Entity\NodeConnection();            
            $AriiWebWeb->setNode($AriiWeb);
            $AriiWebWeb->setConnection($Web);
            $AriiWebWeb->setPriority(0);
            $AriiWebWeb->setDisabled(false);
            $AriiWebWeb->setDescription($AriiWeb->getName().' -> '.$Web->getName());
            $this->em->persist($AriiWebWeb);         
            $this->em->flush();
            
            $AriiWebDB = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findOneBy([ 'node' => $AriiWeb, 'connection' => $DB]);
            if (!$AriiWebDB)
                $AriiWebDB = new \Arii\CoreBundle\Entity\NodeConnection();
            
            $AriiWebDB->setNode($AriiWeb);
            $AriiWebDB->setConnection($DB);
            $AriiWebDB->setPriority(0);
            $AriiWebDB->setDisabled(false);
            $AriiWebDB->setDescription($AriiWeb->getName().' -> '.$DB->getName());
            $this->em->persist($AriiWebDB);
            
            $AriiWebMail = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findOneBy([ 'node' => $AriiWeb, 'connection' => $Mail]);
            if (!$AriiWebMail)
                $AriiWebMail = new \Arii\CoreBundle\Entity\NodeConnection();
            
            $AriiWebMail->setNode($AriiWeb);
            $AriiWebMail->setConnection($Mail);
            $AriiWebMail->setPriority(0);
            $AriiWebMail->setDisabled(false);
            $AriiWebMail->setDescription($AriiWeb->getName().' -> '.$Mail->getName());
            $this->em->persist($AriiWebMail);
            
            $AriiWebScheduler = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findOneBy([ 'node' => $AriiWeb, 'connection' => $Scheduler]);
            if (!$AriiWebScheduler)
                $AriiWebScheduler = new \Arii\CoreBundle\Entity\NodeConnection();
            
            $AriiWebScheduler->setNode($AriiWeb);
            $AriiWebScheduler->setConnection($Scheduler);
            $AriiWebScheduler->setPriority(0);
            $AriiWebScheduler->setDisabled(false);
            $AriiWebScheduler->setDescription($AriiWeb->getName().' -> '.$Scheduler->getName());
            $this->em->persist($AriiWebScheduler);
            
            $this->em->flush();  
        }
        
        $AriiScheduler = $this->em->getRepository("AriiCoreBundle:Node")->findOneBy(['name' => 'arii_scheduler']);
        if (!$AriiScheduler) {
            $AriiScheduler = new \Arii\CoreBundle\Entity\Node();
            $AriiScheduler->setName('arii_scheduler');
            $AriiScheduler->setTitle('Arii Scheduler');
            $AriiScheduler->setComponent('jobscheduler');
            $AriiScheduler->setVendor('ojs');
            $AriiScheduler->setDescription('Supervisor');      
            $AriiScheduler->setStatusDate(new \DateTime());      
            $AriiScheduler->setStatus('default');
            $AriiScheduler->setSite($AriiSite);
            $AriiScheduler->setCategory($Nodes);            
            $this->em->persist($AriiScheduler);
            $this->em->flush();
            
            $AriiSchedulerScheduler = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findOneBy([ 'node' => $AriiScheduler, 'connection' => $Scheduler]);
            if (!$AriiSchedulerScheduler)
                $AriiSchedulerScheduler = new \Arii\CoreBundle\Entity\NodeConnection();
            
            $AriiSchedulerScheduler->setNode($AriiScheduler);
            $AriiSchedulerScheduler->setConnection($Scheduler);
            $AriiSchedulerScheduler->setPriority(0);
            $AriiSchedulerScheduler->setDisabled(false);
            $AriiSchedulerScheduler->setDescription($AriiScheduler->getName().' -> '.$Scheduler->getName());
            $this->em->persist($AriiSchedulerScheduler);
            
            $AriiSchedulerDB = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findOneBy([ 'node' => $AriiScheduler, 'connection' => $DB]);
            if (!$AriiSchedulerDB)
                $AriiSchedulerDB = new \Arii\CoreBundle\Entity\NodeConnection();
            
            $AriiSchedulerDB->setNode($AriiScheduler);
            $AriiSchedulerDB->setConnection($DB);
            $AriiSchedulerDB->setPriority(0);
            $AriiSchedulerDB->setDisabled(false);
            $AriiSchedulerDB->setDescription($AriiScheduler->getName().' -> '.$DB->getName());
            $this->em->persist($AriiSchedulerDB);

            $AriiSchedulerMail = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findOneBy([ 'node' => $AriiScheduler, 'connection' => $Mail]);
            if (!$AriiSchedulerMail)
                $AriiSchedulerMail = new \Arii\CoreBundle\Entity\NodeConnection();
            
            $AriiSchedulerMail->setNode($AriiScheduler);
            $AriiSchedulerMail->setConnection($DB);
            $AriiSchedulerMail->setPriority(0);
            $AriiSchedulerMail->setDisabled(false);
            $AriiSchedulerMail->setDescription($AriiScheduler->getName().' -> '.$Mail->getName());
            $this->em->persist($AriiSchedulerMail);

            // Pour la config
            $AriiSchedulerFS = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findOneBy([ 'node' => $AriiScheduler, 'connection' => $Config]);
            if (!$AriiSchedulerFS)
                $AriiSchedulerFS = new \Arii\CoreBundle\Entity\NodeConnection();
            
            $AriiSchedulerFS->setNode($AriiScheduler);
            $AriiSchedulerFS->setConnection($Config);
            $AriiSchedulerFS->setPriority(0);
            $AriiSchedulerFS->setDisabled(false);
            $AriiSchedulerFS->setDescription($AriiScheduler->getName().' -> '.$Config->getName());
            $this->em->persist($AriiSchedulerFS);
            
            $this->em->flush();                    
        }

        $AriiDB = $this->em->getRepository("AriiCoreBundle:Node")->findOneBy(['name' => 'arii_database']);
        if (!$AriiDB) {
            $AriiDB = new \Arii\CoreBundle\Entity\Node();
            $AriiDB->setName('arii_database');
            $AriiDB->setTitle('Arii Database');
            $AriiDB->setComponent('database');
            switch ($this->parameters['database_driver']) {
                case 'oracle':
                case 'oci8':
                    $AriiDB->setVendor('oracle');
                    break;
                case 'mysql':
                case 'mysqli':
                    $AriiDB->setVendor('mysql');
                    break;
                default:
                    $AriiDB->setVendor('mysql');
                    break;
            }
            $AriiDB->setDescription('Database');      
            $AriiDB->setStatusDate(new \DateTime());      
            $AriiDB->setStatus('default');
            $AriiDB->setSite($AriiSite);
            $AriiDB->setCategory($Nodes);            
            $this->em->persist($AriiDB);
             $this->em->flush();    
             
            // Connexions
            $AriiDB = $this->em->getRepository("AriiCoreBundle:NodeConnection")->findOneBy([ 'node' => $AriiDB, 'connection' => $DB]);
            if (!$AriiDB)
                $AriiDB = new \Arii\CoreBundle\Entity\NodeConnection();
            
            $AriiDB->setNode($AriiScheduler);
            $AriiDB->setConnection($DB);
            $AriiDB->setPriority(0);
            $AriiDB->setDisabled(false);
            $AriiDB->setDescription($AriiScheduler->getName().' -> '.$DB->getName());
            $this->em->persist($AriiDB);

            $this->em->flush();        
        }
        
        return array($AriiWeb, $AriiScheduler, $AriiDB);
    }

    /**************************************
     * Teams
     * 
     **************************************/    
    public function getTeams($filter=[])
    {
        if ($this->session->get('Teams')!='') 
           return $this->session->get('Teams');
        
        $Teams = $this->em->getRepository("AriiCoreBundle:Team")->findBy($filter,['title'=>'ASC', 'name'=>'ASC' ]);
        if (!$Teams) 
            $Teams = $this->getDefaultTeams();

        // Tableau 
        $Result = array();
        foreach ($Teams as $Team) {
            $name = $Team->getName();
            $Result[$name] = array (
                'id' => $Team->getId(),
                'name' => $name,
                'title' => $Team->getTitle(),
                'description' => $Team->getDescription()
            );
        }
        return $this->session->set('Teams', $Result);
    }

    public function resetTeams($force=false) {                    
        $this->session->set('Teams', '');
        if ($force) $this->getDefaultTeams();
        return $this->getTeams();
    }
    
    public function getDefaultTeams()
    {
        
        $Admins = $this->em->getRepository("AriiCoreBundle:Team")->findOneBy(['name' => 'administrators']);
        if (!$Admins) {
            $Admins = new \Arii\CoreBundle\Entity\Team();
            $Admins->setName('administrators');
            $Admins->setTitle($this->translator->trans('administrators') );
            $Admins->setDescription('full access');      
            $this->em->persist($Admins);
        }

        $Ops = $this->em->getRepository("AriiCoreBundle:Team")->findOneBy(['name' => 'operators']);
        if (!$Ops) {
            $Ops = new \Arii\CoreBundle\Entity\Team();
            $Ops->setName('operators');
            $Ops->setTitle($this->translator->trans('operators') );
            $Ops->setDescription('read/execute access');      
            $this->em->persist($Ops);
        }

        $Builders = $this->em->getRepository("AriiCoreBundle:Team")->findOneBy(['name' => 'builders']);
        if (!$Builders) {
            $Builders = new \Arii\CoreBundle\Entity\Team();
            $Builders->setName('builders');
            $Builders->setTitle($this->translator->trans('builders') );
            $Builders->setDescription('read/write access');      
            $this->em->persist($Builders);
        }

        $Users = $this->em->getRepository("AriiCoreBundle:Team")->findOneBy(['name' => 'users']);
        if (!$Users) {
            $Users = new \Arii\CoreBundle\Entity\Team();
            $Users->setName('users');
            $Users->setTitle($this->translator->trans('users') );
            $Users->setDescription('read access');      
            $this->em->persist($Users);
        }
        
        $Unknown = $this->em->getRepository("AriiCoreBundle:Team")->findOneBy(['name' => 'unknown']);
        if (!$Unknown) {
            $Unknown = new \Arii\CoreBundle\Entity\Team();
            $Unknown->setName('unknown');
            $Unknown->setTitle($this->translator->trans('unknown') );
            $Unknown->setDescription('no access');      
            $this->em->persist($Unknown);
        }
        
        $this->em->flush();
        return array($Admins,$Ops,$Builders,$Users,$Unknown);
    }
    
    
    /**************************************
     * Users
     * 
     **************************************/
    public function getUsers($filter=[])
    {
        if ($this->session->get('Users')!='') 
           return $this->session->get('Users');
        
        $Users = $this->em->getRepository("AriiUserBundle:User")->findBy($filter,['username'=>'ASC']);
        if (!$Users) 
            $Users = $this->getDefaultUsers();

        // Tableau 
        $Result = array();
        foreach ($Users as $User) {
            $name = $User->getUsername();
            $Result[$name] = array( 
                'id' => $User->getId(),
                'team' => $User->getTeam(),
                'roles' => $User->getRoles()
            );
        }
        return $this->session->set('Users', $Result);
    }

    public function resetUsers($force=false) {                    
        $this->session->set('Users', '');
        if ($force) $this->getDefaultUsers();
        return $this->getUsers();
    }
            
    public function getDefaultUsers()
    {
        list($Admins,$Ops,$Builders,$Users) = $this->getDefaultTeams();
        
        $user_manager = $this->userManager;
        
        $Admin = $this->em->getRepository("AriiUserBundle:User")->findOneBy(['username' => 'admin']);
        if (!$Admin) {
            $Admin = $user_manager->createUser();
            $Admin->setUsername('admin');
            $Admin->setEmail($this->translator->trans('admin') );
            $Admin->setPlainPassword('admin');
            $Admin->setFirstName('admin'); 
            $Admin->setLastName('admin'); 
            $Admin->setTeam($Admins);
            $Admin->setRoles(['ROLE_ADMIN']);
            $Admin->setEnabled(true); 

            $user_manager->updateUser($Admin);                                
            $this->em->persist($Admin);
        }
        
        $Operator = $this->em->getRepository("AriiUserBundle:User")->findOneBy(['username' => 'operator']);
        if (!$Operator) {
            $Operator = $user_manager->createUser();
            $Operator->setUsername('operator');
            $Operator->setEmail($this->translator->trans('operator') );
            $Operator->setPlainPassword('operator');
            $Operator->setFirstName('operator'); 
            $Operator->setLastName('operator'); 
            $Operator->setTeam($Admins);
            $Operator->setRoles(['ROLE_OPERATOR']);
            $Operator->setEnabled(true);
            
            $user_manager->updateUser($Operator);        
            $this->em->persist($Operator);
        }

        $Builder = $this->em->getRepository("AriiUserBundle:User")->findOneBy(['username' => 'builder']);
        if (!$Builder) {
            $Builder = $user_manager->createUser();
            $Builder->setUsername('builder');
            $Builder->setEmail($this->translator->trans('builder') );
            $Builder->setPlainPassword('builder');
            $Builder->setFirstName('builder'); 
            $Builder->setLastName('builder'); 
            $Builder->setTeam($Admins);
            $Builder->setRoles(['ROLE_BUILDER']);
            $Builder->setEnabled(true);
            
            $user_manager->updateUser($Builder);
            $this->em->persist($Builder);
        }

        $User = $this->em->getRepository("AriiUserBundle:User")->findOneBy(['username' => 'user']);
        if (!$User) {
            $User = $user_manager->createUser();
            $User->setUsername('user');
            $User->setEmail($this->translator->trans('user') );
            $User->setPlainPassword('user');
            $User->setFirstName('user'); 
            $User->setLastName('user'); 
            $User->setTeam($Admins);
            $User->setRoles(['ROLE_USER']);
            $User->setEnabled(true); 
            
            $user_manager->updateUser($User);                
            $this->em->persist($User);
        }
        
        return array($Admin,$Operator,$Builder,$User);
    }
    
    /**************************************
     * TeamRights
     * 
     **************************************/    
    public function getTeamRights($filter=[]) {
        return $this->getRights($filter);
    }

    public function getRights($filter=[])
    {
        if ($this->session->get('Rights')!='') 
           return $this->session->get('Rights');
        
        $Rights = $this->em->getRepository("AriiCoreBundle:TeamFilter")->findBy($filter,['name'=>'ASC' ]);
        if (!$Rights) 
            $Rights = $this->getDefaultRights();

        // Tableau 
        $Result = array();
        foreach ($Rights as $Right) {
            $Team = $Right->getTeam();
            $Filter = $Right->getFilter();
            $name = $Team->getName(); 
            if (!isset($Result[$name])) 
                $Result[$name] =  array();
            array_push( $Result[$name],
            array( 
                'name' => $Right->getName(),     
                'env' => $Filter->getEnv(),
                'spooler' => $Filter->getSpooler(),
                'job' => $Filter->getJobName(),
                'box' => $Filter->getJobGroup(),
                'chain' => $Filter->getJobChain(),
                'trigger' => $Filter->getTrigger(),
                'R' => $Right->getR(),
                'W' => $Right->getW(),
                'X' => $Right->getX()
            ));
        }
        return $this->session->set('Rights', $Result);   
    }

    public function resetRights($force=false) {                    
        $this->session->set('Rights', '');
        if ($force) $this->getDefaultRights();
        return $this->getRights();
    }

    public function getDefaultRights()
    {
        // Initialisation des equipes
        list($Admins,$Operators,$Builders,$Users,$Unknown) =  $this->getDefaultTeams();
        // Initialisation des filtres
        $Filters = $this->getDefaultFilters();

        $name = 'all rights';
        $All = $this->em->getRepository("AriiCoreBundle:TeamFilter")->findOneBy([ 'team' => $Admins, 'filter' => $Filters['all'] ]);
        if (!$All) {
            $All = new \Arii\CoreBundle\Entity\TeamFilter();
            $All->setName($name);
            $All->setFilter($Filters['all']);
            $All->setTeam($Admins);
            $All->setR(1);
            $All->setW(1);
            $All->setX(1);
            $this->em->persist($All);
        }

        $name = 'only read';
        $AllRead = $this->em->getRepository("AriiCoreBundle:TeamFilter")->findOneBy([ 'team' => $Users, 'filter' => $Filters['all'] ]);
        if (!$AllRead) {
            $AllRead = new \Arii\CoreBundle\Entity\TeamFilter();
            $AllRead->setName($name);
            $AllRead->setFilter($Filters['all']);
            $AllRead->setTeam($Users);
            $AllRead->setR(1);
            $AllRead->setW(0);
            $AllRead->setX(0);
            $this->em->persist($AllRead);
        }

        $name = 'read and write on test';
        $AllWrite = $this->em->getRepository("AriiCoreBundle:TeamFilter")->findOneBy([ 'team' => $Builders, 'filter' => $Filters['env test'] ]);
        if (!$AllWrite) {
            $AllWrite = new \Arii\CoreBundle\Entity\TeamFilter();
            $AllWrite->setName($name);
            $AllWrite->setTeam($Builders);
            $AllWrite->setFilter($Filters['env test']);
            $AllWrite->setR(1);
            $AllWrite->setW(1);
            $AllWrite->setX(0);
            $this->em->persist($AllWrite);
        }

        $name = 'only read on prod';
        $AllWrite = $this->em->getRepository("AriiCoreBundle:TeamFilter")->findOneBy([ 'team' => $Builders, 'filter' => $Filters['env prod'] ]);
        if (!$AllWrite) {
            $AllWrite = new \Arii\CoreBundle\Entity\TeamFilter();
            $AllWrite->setName($name);
            $AllWrite->setTeam($Builders);
            $AllWrite->setFilter($Filters['env prod']);
            $AllWrite->setR(1);
            $AllWrite->setW(0);
            $AllWrite->setX(0);
            $this->em->persist($AllWrite);
        }
        
        $name = 'only execute on prod';
        $AllExecute = $this->em->getRepository("AriiCoreBundle:TeamFilter")->findOneBy(['team' => $Operators, 'filter' => $Filters['env prod']]);
        if (!$AllExecute) {
            $AllExecute = new \Arii\CoreBundle\Entity\TeamFilter();
            $AllExecute->setName($name);
            $AllExecute->setTeam($Operators);
            $AllExecute->setFilter($Filters['env prod']);
            $AllExecute->setR(1);
            $AllExecute->setW(0);
            $AllExecute->setX(1);
            $AllExecute->setTeam($Operators);
            $this->em->persist($AllExecute);
        }

        $this->em->flush();
        return array($All,$AllRead,$AllWrite,$AllExecute);
    }

    /**************************************
     * FILTRES
     **************************************/
    public function getFilters($filter=[]) {

        if ($this->session->get('Filters')!='') 
            return $this->session->get('Filters');

        $Filters = $this->em->getRepository("AriiCoreBundle:Filter")->findBy($filter,[ 'name'=>'ASC' ]);
        if (!$Filters) 
            $Filters = $this->getDefaultFilters();

        $Result = array();
        foreach ($Filters as $Filter) {
            $owner = '';
            if ($Filter->getOwner())
                $owner = $Filter->getOwner()->getUsername();
            // si il n'est à personne, on en devient propriétaire (on est admin)
            if ($owner=='')
                $owner = $this->session->getUsername();
            
            $name = $owner.'.'.$Filter->getName();    
            $Result[$name] = array(
                'owner' => $owner,
                'id' => $Filter->getId(),
                'name' => $Filter->getName(),
                'title' => $Filter->getTitle(),
                'description' => $Filter->getDescription(),
                'env' => $Filter->getEnv(),
                'spooler' => $Filter->getSpooler(),
                'folder' => $Filter->getFolder(),
                'trigger' => $Filter->getTrigger(),
                'job_name' => $Filter->getJobName(),
                'job_chain' => $Filter->getJobChain(),
                'job_group' => $Filter->getJobGroup(),
                'status' => $Filter->getStatus(),
                'type' => $Filter->getFilterType(),
            );
        }
        return $this->session->set('Filters', $Result );
    }

    // Les filtres directement créés par l'utilisateur
    public function GetUserFilters()
    {         
        $Filters = $this->getFilters();
        $UserFilters = [];
        foreach ($Filters as $k=>$Filter) {
            if ($Filter['type']!=1) continue;
            // On en profite pour compléter
            if ($Filter['title']=='')
                $Filter['title'] = $Filter['name'];
            $UserFilters[$k] = $Filter;
        }
        return $UserFilters;
    }

    // Les filtres mis pour tous
    public function GetPublicFilters()
    { 
        $Filters = $this->getFilters();
        $PublicFilters = [];
        foreach ($Filters as $k=>$Filter) {
            if ($Filter['type']!=2) continue;
            // On en profite pour compléter
            if ($Filter['title']=='')
                $Filter['title'] = $Filter['name'];
            $PublicFilters[$k] = $Filter;
        }
        return $PublicFilters;
    }
    
    public function resetFilters($force=false) {                    
        $this->session->set('Filters', '');
        if ($force) $this->getDefaultFilters();
        return $this->getFilters();
    }

    public function getDefaultFilters() {
        // Parametrage standard
        $Default = array(
            array(
                'name'   => 'all',
                'title'  => 'All jobs',
                'env' =>  '',
                'spooler' => '',
                'folder' => '',
                'job_name' => '',
                'job_chain' => '',
                'job_group' => '',
                'trigger' => '',
                'status' => 'FAILURE',
                'exit_code' => ''
            ),
            array(
                'name'   => 'env test',
                'title'  => 'Jobs in test environment',
                'env' =>  'T',
                'spooler' => '',
                'folder' => '',
                'job_name' => '',
                'job_chain' => '',
                'job_group' => '',
                'trigger' => '',
                'status' => '',
                'exit_code' => ''
            ),
            array(
                'name'   => 'env prod',
                'title'  => 'Jobs in prod environment',
                'env' =>  'P',
                'spooler' => '',
                'folder' => '',
                'job_name' => '',
                'job_chain' => '',
                'job_group' => '',
                'trigger' => '',
                'status' => '',
                'exit_code' => ''
            ),
            array(
                'name'   => 'status failure',
                'title'  => 'Jobs with status FAILURE',
                'env' =>  '',
                'spooler' => '',
                'folder' => '',
                'job_name' => '',
                'job_chain' => '',
                'job_group' => '',
                'trigger' => '',
                'status' => 'FAILURE',
                'exit_code' => ''
            ),
            array(
                'name'   => 'job arii',
                'title'  => 'Jobs for Ari\'i',
                'env' =>  '',
                'spooler' => '',
                'folder' => '',
                'job_name' => 'ARI%',
                'job_chain' => 'ARI%',
                'job_group' => '',
                'trigger' => '',
                'status' => '',
                'exit_code' => ''
            ),
            array(
                'name'   => 'job ari and status failure',
                'title'  => 'Jobs failed for Ari\'i',
                'env' =>  '',
                'spooler' => '',
                'folder' => '',
                'job_name' => 'ARI%',
                'job_chain' => 'ARI%',
                'job_group' => '',
                'trigger' => '',
                'status' => 'FAILURE',
                'exit_code' => ''
            )
        );

        // Filtres internes appartenant à l'admin
        list($Admin) = $this->getDefaultUsers();
 
        $Filters = array();
        foreach ($Default as $Param) {
            // le parametre existe ?
            $name = $Param['name'];
            $Filter = $this->em->getRepository("AriiCoreBundle:Filter")->findOneBy(
                [   'name' => $name,
                    'filter_type' => 2 ]
            );
            if (!$Filter)
                $Filter = new \Arii\CoreBundle\Entity\Filter();

            $Filter->setFilterType(2);
            $Filter->setOwner($Admin);
            
            $Filter->setName($name);
            $Filter->setTitle($Param['title']);
            $Filter->setEnv($Param['env']);
            $Filter->setTrigger($Param['trigger']);
            $Filter->setSpooler($Param['spooler']);
            $Filter->setFolder($Param['folder']);
            $Filter->setJobName($Param['job_name']);
            $Filter->setJobChain($Param['job_chain']);
            $Filter->setJobGroup($Param['job_group']);
            
            $this->em->persist($Filter);
            $Filters[$name] = $Filter;
        }
        $this->em->flush();               
        return $Filters;
    }

    public function getFilterByName($name) {
        $Filters = $this->getFilters();
        if (isset($Filters[$name]))
            return $Filters[$name];
        return;
    }

    public function getFilterById($id) {
        $Filters = $this->getFilters();
        foreach ($Filters as $Filter) {
            if ($Filter['id']==$id)
                return $Filter;
        }
        return;
    }
    
    public function setUserFilters()
    {
        $this->set( 'Filters', $this->em->getRepository("AriiCoreBundle:UserFilter")->findBy(array('user'=>$this->get('User')),array('name' => 'asc')));
    }
    
    public function setUserFilter($current)
    {
        $this->set('user_filter',$current);
    }   

    public function getUserFilter()
    { 
        return $this->session->get('user_filter');
    }

    public function setUserFilterById($id=-1)
    {
        if ($id>=0) {
            $F = $this->em->getRepository("AriiCoreBundle:Filter")->find($id);
            if ($F) {
                $Filter['name']      = $F->getName();
                $Filter['spooler']   = $F->getSpooler();
                $Filter['job']       = $F->getJob();
                $Filter['job_chain'] = $F->getJobChain();
                $Filter['order_id']  = $F->getOrderId();
                $Filter['status']    = $F->getStatus();
                return $this->session->set('user_filter',$data->sql->get_next($res));
            }
        }
        $Filter['name']      = 'All';
        $Filter['spooler']   = '%';
        $Filter['job']       = '%';
        $Filter['job_chain'] = '%';   
        $Filter['order_id']  = '%';   
        $Filter['status']    = '%';
        return $this->session->set('user_filter',$Filter);
    }

    public function setSpooler($spooler)
    { 
       $this->session->set('spooler',$spooler);
    }

    public function getUserId()
    {
        $Infos = $this->getUserInfo();   
        if (isset($Infos['id']))
            return $Infos['id'];
        return;
    }

    /**************************************
     * MES FILTRES
     * Filtres de l'utilisateur connecté
     **************************************/
    public function getMyFilters($filter=[]) {

        if ($this->session->get('MyFilters')!='') 
            return $this->session->get('MyFilters');

        $owner = $this->getUsername();        
        $Filters = $this->getFilters();

        // limité à mes filtres
        $Result = array();
        foreach ($Filters as $k=>$Filter) {
            // mes filtres privés
            if (($Filter['owner']!=$owner) or ($Filter['type']!=1))
                continue;
            $name = $Filter['name'];
            $Result[$name] = $Filter;
        }

        return $this->session->set('MyFilters', $Result );
    }

    public function resetMyFilters($force=false) {  
        $this->session->set('Filters', '');
        $this->session->set('MyFilters', '');
        return $this->getMyFilters();
    }
        
/*******************************************************************/    
/* APPELS D'OBJETS                                                 */
/*******************************************************************/
    
    /**************************************
     * Database
     **************************************/    
    public function getDatabases() {

        if ($this->session->get('Databases')!='')
            return $this->session->get('Databases');

        $Databases = array();
        $Connections = $this->getConnections();
        foreach ($Connections as $name=>$Data) {
            if ($Data['domain']=='database')
                $Databases[$name] = $Data;
        }
        
        return $this->session->set('Databases',$Databases);
    }
    
    public function getDatabase($name='') {
        if ($this->session->get('Database')!='')
            return $this->session->get('Database');
        $Databases = $this->getDatabases();        
        if (isset($Databases[$name])) {
            return $this->session->set('Database',$Databases[$name]);
        }
        return null;
    }

    public function getDatabaseByConnection($instance='',$login='') {
        $Databases = $this->getDatabases();
        foreach ($Databases as $name=>$Database) {
            if (($Database['instance']==$instance) and ($Database['login']==$login)) {
                return $name;
            }
        }
        return;
    }
    
    public function setDatabaseByName($name) {
        $Databases = $this->getDatabases();
        if (isset($Databases[$name]))
            return $this->session->set('Database',$Databases[$name]);
        throw new \Exception("Database name '$name' not found !");    
    }

    public function setDatabaseById($id) {
        $Databases = $this->getDatabases();
        foreach ($Databases as $db) {
            if ($db['id']==$id)
                return $this->session->set('Database',$db);
        };
        throw new \Exception("Database ID '$id' not found !");    
    }
    
    public function setDatabase($Database) {
        return $this->session->set('Database',$Database);
    }
    
    public function resetDatabase() {
        $current = $this->session->get('Database');
        if ($current!='')
            return $this->getDatabase($current);
        
        return array();
    }
    
    /**************************************
     * JobScheduler
     **************************************/    
    public function getJobSchedulers() {
        
        if ($this->session->get('JobSchedulers')!='')
            return $this->session->get('JobSchedulers');

        $JobSchedulers = array();
        $Nodes = $this->getNodes();
        foreach ($Nodes as $name=>$Data) {
            if ($Data['component']=='jobscheduler')
                $JobSchedulers[$name] = $Data;
        }
        
        return $this->session->set('JobSchedulers',$JobSchedulers);
    }
    
    public function getJobScheduler($name='arii_scheduler') {

        if ($this->session->get('JobScheduler')!='')
            return $this->session->get('JobScheduler');

        $JobSchedulers = $this->getJobSchedulers();        
        if (isset($JobSchedulers[$name]))
            return $JobSchedulers[$name];
        
        return array();
    }

    public function setJobScheduler($name='arii_scheduler') {

        $JobSchedulers = $this->getJobSchedulers();        
        if (isset($JobSchedulers[$name]))
            return $this->session->set('JobScheduler', $JobSchedulers[$name]);
        
        return $this->session->set('JobScheduler', $JobSchedulers['arii_scheduler']);
    }
    
    /**************************************
     * Workspace
     **************************************/
    /* prevoir la notion d'espace utilisateur */
    public function getWorkspace(){
        return $this->getParameter('workspace');
    }
    
    // obsolete
    public function getBaseDir($dir,$bundle='Core') {
        return $portal->getWorkspace().'/'.$bundle.$dir;        
    }    

    public function getWorkPaths($dir,$bundle='Core') {
        return [
            $this->getWorkspace().'/'.$bundle.$dir,
            '../src/Arii/'.$bundle.'Bundle/Resources/workspace'.$dir 
        ];
    }    
    
    public function getWorkFile($file,$bundle='Core') {
        $Paths = $this->getWorkPaths(dirname($file),$bundle);
        $File = basename($file);

        // Recherche dans l'espace de travail
        foreach ($Paths as $path) {
            $filename = str_replace('\/','/',"$path/$File");
            if (file_exists($filename)) 
                return $filename;
        }
        return;
    }    
    
    /**************************************
     * Appel direct des données en session
     **************************************/
    public function getUserInterface($init=false) {
        $User = $this->session->get('UserInterface');
        if ((!$User) or ($init))
            return $this->getDefaultUserInterface();
        
        return $User;
    }

    public function setUserInterface($User) {

        // On complete
        if (!isset($User['date']) or ($User['date']==''))
            $User['date'] = new \DateTime();
        if (!isset($User['year']) or ($User['year']==''))
            $User['year'] =   $User['date']->format('Y');
        if (!isset($User['month']) or ($User['month']==''))
            $User['month'] =  $User['date']->format('m');
        if (!isset($User['day']) or ($User['day']==''))
            $User['day'] =    $User['date']->format('d');
        if (!isset($User['hour']) or ($User['hour']==''))
            $User['hour'] =   $User['date']->format('H');
        if (!isset($User['minute']) or ($User['minute']==''))
            $User['minute'] = $User['date']->format('i');
        if (!isset($User['second']) or ($User['second']==''))
            $User['second'] = $User['date']->format('s');
        
        $User['date'] = new \DateTime(sprintf('%04d-%02d-%02d',$User['year'],$User['month'],$User['day']));
        $User['timestamp'] = time(sprintf('%02d:%02d:%02d',$User['hour'],$User['minute'],$User['second']));

        return $this->session->set('UserInterface',$User);
    }
    
    public function getDefaultUserInterface() {
        
        $User['spooler'] = '%';
        
        $Time = localtime(time(),true);

        // Date de reference 
        $refDate = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $Time['tm_year']+1900, $Time['tm_mon']+1, $Time['tm_mday'], $Time['tm_hour'], $Time['tm_min'], $Time['tm_sec']);

        $User['day'] = $Time['tm_mday'];
        $User['month'] = $Time['tm_mon']+1;
        $User['year'] = $Time['tm_year']+1900;
                
        $User['date'] = new \DateTime($refDate);        
        $User['timestamp'] = time();
        
        // Pour la partie exploitation
        $User['refPast'] = -1;
        $User['refFuture'] = 1;

        // Pour la majorité des bundles, juste le dernier jour
        $User['dayPast'] = -1;
        $User['dayFuture'] = 30;
        
        $User['past'] = $this->CalcDate($refDate, $User['refPast'] );
        $User['future'] = $this->CalcDate( $refDate, $User['refFuture'] );

        $User['datePast'] =   new \DateTime( $User['past'] );
        $User['dateFuture'] = new \DateTime( $User['future'] );
        
        $User['refresh'] = 30;
        $User['refreshPause'] = 0;
        
        $User['maxResults'] = 1000;

        $User['env'] = 'P';
        $User['app'] = '*';
        $User['class'] = '*';
        
        // Compatibilité ascendante
        $User['refDate'] = clone $User['date'];
        $User['refTimestamp'] = $User['timestamp'];
        
        return $this->session->set('UserInterface',$User);
    }    
            
    public function setRefDate($date)
    {
        $User = $this->getUserInterface();
        $User['refDate'] = $date;        
        $User['past'] = $this->CalcDate( $date, $User['refPast'] );
        $User['future'] = $this->CalcDate( $date, $User['refFuture'] );
        return $this->session->set('UserInterface',$User);        
    }

    public function setRefPast($hours)
    {
        $User = $this->getUserInterface();
        $User['refPast'] = $hours;
        $User['past'] = $this->CalcDate( $User['refDate'], $hours );
        return $this->session->set('UserInterface',$User);
    }
    
    public function setRefFuture($hours)
    {
        $User = $this->getUserInterface();
        $User['refFuture'] = $hours;
        $User['future'] = $this->CalcDate( $User['refFuture'], $hours );
        return $this->session->set('UserInterface',$User);
    }

    public function setRefresh($seconds)
    {
        $User = $this->getUserInterface();        
        $User['refresh'] = $seconds;                
        return $this->session->set('UserInterface',$User);
    }

    public function setRefreshPause($pause)
    {
        $User = $this->getUserInterface();        
        $User['refreshPause'] = $pause;                
        return $this->session->set('UserInterface',$User);
    }
    
    public function setApp($app)
    {
        $User = $this->getUserInterface();        
        $User['app'] = $app;                
        return $this->session->set('UserInterface',$User);
    }

    public function setEnv($env)
    {
        $User = $this->getUserInterface();        
        $User['env'] = $env;                
        return $this->session->set('UserInterface',$User);
    }

    public function setTag($tag)
    {
        $User = $this->getUserInterface();        
        $User['tag'] = $tag;                
        return $this->session->set('UserInterface',$User);
    }

    public function setJobClass($jobClass)
    {
        $User = $this->getUserInterface();        
        $User['jobClass'] = $jobClass;                
        return $this->session->set('UserInterface',$User);
    }
    
    public function setCategory($category)
    {
        $User = $this->getUserInterface();        
        $User['category'] = $category;                
        return $this->session->set('UserInterface',$User);
    }

    public function setDay($day)
    {
        $User = $this->getUserInterface();        
        $User['day'] = $day;                
        return $this->session->set('UserInterface',$User);
    }

    public function setMonth($month)
    {
        $User = $this->getUserInterface();        
        $User['month'] = $month;      
        return $this->session->set('UserInterface',$User);
    }
    
    public function setYear($year)
    {
        $User = $this->getUserInterface();        
        $User['year'] = $year;                
        return $this->session->set('UserInterface',$User);
    }
    
    /**************************************
     * Calcule de date (appel du service AriiDate ?
     **************************************/                
    private function CalcDate($date,$days) 
    {
        // compatibilite datetime et localtime
        if (is_object($date)) {
            $date->add(\DateInterval::createFromDateString($days.' days'));
            return $date->format('Y-m-d H:i:s');
        }
        elseif ($date=='') {
            $tm = localtime(time()+($days*86400),true);
            return sprintf("%04d-%02d-%02d %02d:%02d:%02d",$tm['tm_year']+1900,$tm['tm_mon']+1,$tm['tm_mday'],$tm['tm_hour'],$tm['tm_min'],$tm['tm_sec']);
        }        
        $year = substr($date,0,4);
        $month = substr($date,5,2);
        $day = substr($date,8,2);
        $hour = substr($date,11,2);
        $min = substr($date,14,2);
        $sec = substr($date,17,2);
        $tm = localtime(mktime($hour, $min, $sec, $month, $day, $year )+($days*86400),true);
        return sprintf("%04d-%02d-%02d %02d:%02d:%02d",$tm['tm_year']+1900,$tm['tm_mon']+1,$tm['tm_mday'],$tm['tm_hour'],$tm['tm_min'],$tm['tm_sec']);
    }

    private function CalcDateHours($date,$hours) 
    {
        $year = substr($date,0,4);
        $month = substr($date,5,2);
        $day = substr($date,8,2);
        $hour = substr($date,11,2);
        $min = substr($date,14,2);
        $sec = substr($date,17,2);
        $tm = localtime(mktime($hour, $min, $sec, $month, $day, $year )+($hours*3600),true);
        return sprintf("%04d-%02d-%02d %02d:%02d:%02d",$tm['tm_year']+1900,$tm['tm_mon']+1,$tm['tm_mday'],$tm['tm_hour'],$tm['tm_min'],$tm['tm_sec']);
    }


    
}
