<?php
namespace Arii\ACKBundle\Controller\Import;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AutosysController extends Controller
{
    private $Status = [
           'FA' => 'FAILURE',
           'SU' => 'SUCCESS',
           'AC' => 'ACTIVATED',
           'OH' => 'ON_HOLD',
           'OI' => 'ON_ICE',
           'TE' => 'TERMINATED',
           'IN' => 'INACTIVE',
            1 => 'RUNNING',
            3 => 'STARTING',
            4 => 'SUCCESS',
            5 => 'FAILURE',
            6 => 'TERMINATED',
            7 => 'ON_ICE',
            8 => 'INACTIVE',
            9 => 'ACTIVATED',
            10 => 'RESTART',
            11 => 'ON_HOLD',
            12 => 'QUEUE_WAIT',
            13 => 'WAIT_REPLY',
            14 => 'PEND_MACH',
            15 => 'RES_WAIT',
            16 => 'NO_EXEC'        
        ];

    public function dbAction($db)
    {  
        set_time_limit(3600);
        $time = time();
        $ats = $this->getDoctrine()->getManager($db);
        $em = $this->getDoctrine()->getManager();
        
        // Info global
        $Info = $ats->getRepository("AriiATSBundle:UjoAlamode")->findOneBy([ 'type' => 'AUTOSERV']);
        $instance = $Info->getStrVal('strVal');

        // Referentiel des jobs
        $n = 0;
        $Records = $ats->getRepository("AriiATSBundle:UjoJobst")->synchroJobst(time()-2*24*3600);
        foreach ($Records as $Record) {
            $job_name       = $Record['jobName'];                        
            $name = "$instance#$job_name";
            print "$name</br>";
            $Status = $this->getDoctrine()->getRepository("AriiACKBundle:Status")->findOneBy(
            [
                'name'   =>   $name
            ]);    

            if (!$Status) {
                $Status = new \Arii\ACKBundle\Entity\Status();
                $Status->setState('OPEN');
                $Status->setStateTime(new \DateTime());
            }

            $Status->setInstance ($instance);            
            $Status->setName     ($name);
            $Status->setTitle    ($job_name);
            $Status->setSource   ('ATS');
            $Status->setObjType  (4);
            $Status->setJobLog   ('?');

            $Status->setType     ($Record['jobType']);
            $Status->setLastStart(new \DateTime('@'.$Record['lastStart']));
            $Status->setLastEnd  (new \DateTime('@'.$Record['lastEnd']));
            $Status->setExitCode ($Record['exitCode']);
            $status = $Record['status'];
            $Status->setStatus   (isset($this->Status[$status])?$this->Status[$status]:'UNKNOWN');
            if ($status=='FAILURE' or $status=='TERMINATED')
                $Status->setState('OPEN');
            else 
                $Status->setState('CLOSE');

            $Status->setUpdated  (new \DateTime());
            
            $em->persist($Status);
            if ($n++ % 100 == 0)
                $em->flush();            
        } 
        $em->flush();
        return new Response(sprintf( "# %d\n%d s\n%s\n",$n,(time()-$time),"success"));        
    }
    
    public function statusAction()
    {  
        set_time_limit(300);
                
        // On traite le log
        if (isset($_FILES['txt']['tmp_name']))
            $log = file_get_contents($_FILES['txt']['tmp_name']);
        else 
            $log = file_get_contents('../workspace/ACK/Input/Autosys/status.txt');

        // Info supplementaires
        $request = Request::createFromGlobals();
        $source = $request->get('source');
        
        $Infos = explode("\n",$log);
        do {
            $head = array_shift($Infos);
        } while (substr($head,0,10)!='__________');
        $head = array_shift($Infos);
        
        $em = $this->getDoctrine()->getManager();
        $n = 0;
        foreach ($Infos as $info) {
            $job_name   = trim(substr($info,0,64));
            $last_start = trim(substr($info,65,20));
            $last_end   = trim(substr($info,86,20));
            $status     = trim(substr($info,107,2));
            $run        = trim(substr($info,110,9));
            $exit       = trim(substr($info,119,9));
            
            $record = $this->getDoctrine()->getRepository("AriiACKBundle:Status")->findOneBy(
            [
                'source' => $source,
                'name'   => $job_name
            ]);            
            if (!$record)
                $record = new \Arii\ACKBundle\Entity\Status();
            
            $record->setSource               ($source);
            $record->setName                 ($job_name);
            $record->setType                 ('ATS');
            $record->setTitle                ($source.' '.$job_name);
            $record->setLastStart            (($last_start == '-----')?null:new \DateTime($last_start));
            $record->setLastEnd              (($last_end == '-----')?null:new \DateTime($last_end));
            $record->setExitCode             ($exit);
            $record->setStatus               (isset($this->Status[$status])?$this->Status[$status]:'UNKNOWN');
            $record->setUpdated              (new \DateTime());
            
            $em->persist($record);
            if ($n++ % 100 == 0)
                $em->flush();            
        }
        $em->flush();
        return new Response("success");        
    }
    
    public function purgeAction()
    {  
        set_time_limit(300);
        return new Response("success");        
    }
    
}

