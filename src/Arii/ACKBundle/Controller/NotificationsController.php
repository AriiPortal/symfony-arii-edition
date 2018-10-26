<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class NotificationsController extends Controller{

    public function indexAction()
    {
        return $this->render('AriiACKBundle:Notifications:index.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Notifications:toolbar.xml.twig", array(), $response);
    }
    
    public function toolbar2Action()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Notifications:toolbar2.xml.twig", array(), $response);
    }

    public function grid_toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Notifications:grid_toolbar.xml.twig", array(), $response);
    }

    public function gridAction()
    {   
        $sql = $this->container->get('arii_core.sql');        
        $qry = $sql->Select(array("ID","NAME","TITLE","DESCRIPTION","ORIGIN","PATTERN","STATUS","EXIT_CODE","ACTIVE"))
                .$sql->From(array("ARII_ALERT"))
                .$sql->OrderBy(array('NAME,TITLE,STATUS,EXIT_CODE'));
        
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('grid');
        $data->event->attach("beforeRender",array($this,"grid_render"));
        $data->render_sql($qry,"ID","NAME,TITLE,PATTERN,ORIGIN,STATUS,EXIT_CODE");
    }
    
    function grid_render($data){
        if ($data->get_value('ACTIVE')==1)
            $data->set_row_color("#ccebc5");
        else
            $data->set_row_color("#fbb4ae");
    }
    
    
    public function formAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $sql = $this->container->get('arii_core.sql');        
        $qry = $sql->Select(array("ID","NAME","TITLE","DESCRIPTION","ORIGIN","PATTERN","STATUS","EXIT_CODE","MESSAGE","TIME_SLOT","MSG_TYPE","MSG_FROM","MSG_TO","MSG_CC","TO_DO","ACTION","ACTIVE","APPLICATION_ID","NOTE_ID"))
                .$sql->From(array("ARII_ALERT"))
                .$sql->Where(array("ID"=>$id));
        
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('form');
        $data->event->attach("beforeRender",array($this,"form_render"));
        $data->render_sql($qry,"ID","ID,NAME,TITLE,DESCRIPTION,ORIGIN,PATTERN,STATUS,EXIT_CODE,MESSAGE,TIME_SLOT,MSG_TYPE,MSG_FROM,MSG_TO,MSG_CC,TO_DO,ACTION,ACTIVE,APPLICATION_ID,NOTE_ID");
    }
    function form_render($data){
        // astuce pour traiter les blobs
        foreach (array('TO_DO','ACTION') as $c) {
            if (is_Probe($data->get_value($c))) {
                $value = $data->get_value($c)->load();
                $data->set_value($c,$value);
                $data->set_value(strtolower($c),$value);
            }
        } 
    }

    public function checkAction()
    {   
        $request = Request::createFromGlobals();
        $job = $request->get('job');
        if ($job=='')
            $job='.*';
        $len_job = strlen($job);
        $status = $request->get('status');
        if ($status=='')
            $status='JOBFAILURE';
        $codes = $request->get('codes');
        if ($codes=='')
            $codes='.*';
        
        $request = [
            'job' => $job,
            'status' => $status,
            'codes' => $codes,            
        ];
        
        $Alerts = $this->getDoctrine()->getRepository("AriiCoreBundle:Alert")->Alerts($job);      
        $Check = array();
        $max= 0;
        $Result = [];
        foreach ($Alerts as $Alert) {
            $pattern = $Alert->getPattern();
            $pstatus = $Alert->getStatus();
            $pexit = $Alert->getExitCodes();
            if (!preg_match('/'.$pattern.'/',$job,$matches)) 
                continue;
            # Si un status est indiqué mais qu'il n'est pas dans le pattern, on sort
            if (!preg_match('/'.$pstatus.'/',$status,$matches)) 
                continue;
            # Si un exit est indiqué mais qu'il n'est pas dans le pattern, on sort
            if (($pexit!='') and (!preg_match('/('.str_replace(',','|',$pexit).')/',$codes,$matches))) 
                continue;
            
            # Le score est la taille du pattern le plus grand
            $score = strlen($pattern);
            if ($score>=$max) {
                $max=$score;
                $len_pattern = strlen(str_replace(['^','$','\\','[',']'],['','','','',''],$pattern));
                $percent = round($len_pattern*100/$len_job);
                $Result = array(
                    'pattern'     => $pattern,
                    'status'      => $pstatus,
                    'exit_code'   => $pexit,
                    'name'        => $Alert->getName(),
                    'to_do'       => $Alert->getToDo(),
                    'description' => $Alert->getDescription(),
                    'message'     => $Alert->getMessage(),
                    'msg_from'    => $Alert->getMsgFrom(),                
                    'msg_to'      => $Alert->getMsgTo(),
                    'msg_cc'      => $Alert->getMsgCc(),
                    'score'       => $score,
                    'percent'     => $percent
                );
            }
        }     
        return $this->render('AriiACKBundle:Alerts:check.html.twig', array( 'request' => $request, 'result' => $Result));
    }
    
    public function deleteAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('ID');
        
        $alert = $this->getDoctrine()->getRepository("AriiCoreBundle:Alert")->find($id);      
        if ($alert) {
            $em = $this->getDoctrine()->getManager();       
            $em->remove($alert);
            $em->flush();
            return new Response("success");            
        }
        
        return new Response("?!");            
    }
    
    public function saveAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('ID');

        $alert = new \Arii\CoreBundle\Entity\Alert();
        if( $id!="" )
            $alert = $this->getDoctrine()->getRepository("AriiCoreBundle:Alert")->find($id);      
  
        $alert->setName($request->get('NAME'));
        $alert->setTitle($request->get('TITLE'));
        $alert->setDescription($request->get('DESCRIPTION'));
        $alert->setOrigin($request->get('ORIGIN'));
        $alert->setPattern($request->get('PATTERN'));
        $alert->setStatus($request->get('STATUS'));
        $alert->setExitCodes($request->get('EXIT_CODE'));
        $alert->setMessage($request->get('MESSAGE'));
        $alert->setTimeSlot($request->get('TIME_SLOT'));
        $alert->setMsgType($request->get('MSG_TYPE'));
        $alert->setMsgFrom($request->get('MSG_FROM'));
        $alert->setMsgTo($request->get('MSG_TO'));
        $alert->setMsgCc($request->get('MSG_CC'));
        $alert->setToDo($request->get('TO_DO'));
        $alert->setAction($request->get('ACTION'));
        $alert->setActive($request->get('ACTIVE'));

        $application = $this->getDoctrine()->getRepository("AriiCoreBundle:Application")->find($request->get('APPLICATION_ID'));
        if ($application)
            $alert->setApplication($application);
        
        $note = $this->getDoctrine()->getRepository("AriiCoreBundle:Note")->find($request->get('NOTE_ID'));
        if ($note)
            $alert->setNote($note);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($alert);
        $em->flush();
        
        return new Response("success");
    }
    
    public function importAction()
    {
        $portal = $this->container->get('arii_core.portal');        
        $file = file_get_contents($portal->getWorkspace().'/Admin/Import/autoclose.csv');
        $Log = explode("\r\n",$file);
        
        $header = array_shift($Log);
        $Head = explode("\t",$header);
                
        // On traite le log
        $em = $this->getDoctrine()->getManager();
        $n=0;
        foreach ($Log as $l) {
            $Infos = explode("\t",$l);
            for($i=0;$i<count($Head);$i++) {
                if (isset($Infos[$i])) 
                    $Data[$Head[$i]] = $Infos[$i];
                else
                    $Data[$Head[$i]] = '';
            }

            // if (!isset($Data['pattern']) or ($Data['pattern']=='')) continue;
        
            $Alert = $this->getDoctrine()->getRepository("AriiCoreBundle:Alert")->findOneBy( array( 'name' => $Data['pattern'] ) );
            if (!$Alert) 
                $Alert = new \Arii\CoreBundle\Entity\Alerts();
        
            $Alert->setOrigin('ATS-VA1');
            $Alert->setPattern($Data['pattern']);
            $Alert->setName($Data['pattern']);
            $Alert->setTitle($Data['pattern']);
            $Alert->setDescription($Data['description']);
            $Alert->setTimeSlot($Data['time_slot']);
            $Alert->setStatus($Data['alarm']);
            $Alert->setExitCodes($Data['exit_codes']);
            $Alert->setMsgType($Data['send']);
            $Alert->setMessage($Data['message']);
            $Alert->setMsgFrom($Data['from']);
            $Alert->setMsgTo($Data['to']);
            $Alert->setMsgCc($Data['cc']);
            $Alert->setToDo($Data['help']);
            $Alert->setAction($Data['action']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($Alert);
        }
        $em->flush();        
        return new Response("success");
    }

    // Exporte un fichier 
    public function getAction($file="tsv")
    {   
        $alerts = "pattern	description	time_slot	alarm	exit_codes	send	to	cc	from	message	help	action\n";
        $em = $this->getDoctrine()->getManager();
        $Alerts = $em->getRepository("AriiACKBundle:Alert")->Alerts();
        foreach ($Alerts as $Alert) {
            if ($Alert->getPattern()=='') continue;
            
            $Line = array();            
            array_push($Line,$Alert->getPattern());
            array_push($Line,$Alert->getDescription());
            array_push($Line,$Alert->getTimeSlot());
            array_push($Line,$Alert->getStatus());
            array_push($Line,$Alert->getExitCodes());
            array_push($Line,$Alert->getMsgType());
            array_push($Line,$Alert->getMsgTo());
            array_push($Line,$Alert->getMsgCc());
            array_push($Line,$Alert->getMsgFrom());
            array_push($Line,$Alert->getMessage());
            array_push($Line,$Alert->getToDo());
            array_push($Line,$Alert->getAction());
            $alerts .= implode("\t",$Line)."\n";
        }
        $response = new Response();
        switch ($file) {
            case 'xls':
                $alerts = utf8_decode($alerts);
                $response->headers->set('Content-type', 'application/vnd.ms-excel; charset=utf-8');
                $response->headers->set("Content-disposition", "attachment; filename=autoclose.xls"); 
                break;
            default:
                $response->headers->set('Content-Type', 'text/plain');
        }
        $response->setContent($alerts);
        return $response;
    }
    
}

?>
