<?php
namespace Arii\ACKBundle\Controller\Import;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LiveStatusController extends Controller
{
    public function downtimesAction()
    {
        // On traite le log
        if (isset($_FILES['txt']['tmp_name']))
            $log = file_get_contents($_FILES['txt']['tmp_name']);
        else 
            $log = file_get_contents('../workspace/ACK/Input/Nagios/downtimes.txt');
        // Nettoyage 
        $Infos = $this->csv2array($log);
        print_r($Infos);
        exit();
    }

    public function hostsAction()
    {
        // On traite le log
        if (isset($_FILES['txt']['tmp_name']))
            $log = file_get_contents($_FILES['txt']['tmp_name']);
        else 
            $log = file_get_contents('../workspace/ACK/Input/Nagios/hosts.txt');

        $Infos = $this->csv2array($log);

        $em = $this->getDoctrine()->getManager();
        $n = 0;
        foreach ($Infos as $Info) {
            
            $record = $this->getDoctrine()->getRepository("AriiACKBundle:Network")->findOneBy(
            [
                'name' => $Info['name']
            ]);
            
            // STATUS Nagios
            if ($Info['state']==0) {
                $status = 'OK';
            } 
            elseif ($Info['state']==1) {
                $status = 'WARNING';
            } 
            elseif ($Info['state']==4) {
                $status = 'ERROR';
            } 
            else {
                $status = 'UNKNOWN';
            }

            // Status UNKNOWN, c'est utile ?
            if ($status == 'UNKNOWN') continue;
            
            if (!$record) {
                $record = new \Arii\ACKBundle\Entity\Network();
                // c'est ouvert
                $record->setStateTime(new \DateTime());
                // On calcul l'état initiale
                $record->setState('OPEN');
                
                // Verification IP
                // Il faudra distinguer synchro rapide et màj
                if (filter_var($Info['address'], FILTER_VALIDATE_IP)) {
                    $record->setIPAddress            ($Info['address']);
                } else {
                    $record->setIPAddress            (gethostbyname($Info['address']));
                }                

            }
            
            // si aucun object n'est attaché
            if (!$record->getObject()) {
                // on retrouve l'objet HOST
                $host = $this->getDoctrine()->getRepository("AriiACKBundle:Object")->findOneBy(
                [
                    'name' => $Info['name'],
                    'obj_type' => 'HOST'
                ]);
                
                if (!$host) {
                    $host = new \Arii\ACKBundle\Entity\Object();
                    $host->setName($Info['name']);
                    $host->setTitle($Info['name']);
                    $host->setObjType('HOST');
                    $host->setDescription($Info['alias']);
                    
                    $em->persist($host);                    
                }
                $record->setObject($host);
            }
            
            // Si le status est ok, on ferme
            // même si c'est acquitté
            if ($Info['downtimes']!='') {
                $record->setEventType('DOWNTIME');
                $record->setState('OPEN');
            }
            else {
                $record->setEventType('STATE');
                if ($status == 'OK')
                    $record->setState('CLOSE');
            }
            
            // plusieurs Nagios ?
            $record->setEventSource('NAGIOS');
            $record->setName                 ($Info['name']);
            $record->setTitle                ($Info['display_name']);
            $record->setDescription          ($Info['alias']);
            $record->setHost                 ($Info['address']);
            if (isset($Info['port']))
                $record->setPort                 ($Info['port']);
            $record->setAcknowledged         ($Info['acknowledged']);
            $record->setAcknowledgementType  ($Info['acknowledgement_type']);
            $record->setLastStateChange      (new \DateTime('@'.$Info['last_state_change']));
            $record->setLastTimeDown         (new \DateTime('@'.$Info['last_time_down']));
            $record->setLastTimeUnreachable  (new \DateTime('@'.$Info['last_time_unreachable']));
            $record->setLastTimeUp           (new \DateTime('@'.$Info['last_time_up']));
            $record->setLatency              ($Info['latency']);
            $record->setStateInformation     ($Info['plugin_output']);
            $record->setStatus($status);
            $record->setStatusTime(new \DateTime('@'.$Info['last_check'])); 
            
            // Chaine speciale
            if (is_array($Info['downtimes']))
                $record->setDowntimes(implode(',',$Info['downtimes']));
            else
                $record->setDowntimes($Info['downtimes']);
            
            if (is_array($Info['downtimes_with_info'])) {
                // On prend le dernier 
                $last_info = array_pop($Info['downtimes_with_info']);
                
                $downtimes  = array_shift($last_info);
                $user       = array_shift($last_info);
                $info       = array_shift($last_info);
                
                $record->setDowntimesUser($user);
                $record->setDowntimesInfo($info);
            }
            else {
                $record->setDowntimesUser(null);
                $record->setDowntimesInfo($Info['downtimes_with_info']);
            }            
                     
            $em->persist($record);
            if ($n++ % 100 == 0)
                $em->flush();            
        }
        $em->flush();
        print "($n)";
        
        return new Response("success");        
    }

    public function servicesAction()
    {
        set_time_limit(300);        
        // On traite le log
        if (isset($_FILES['csv']['tmp_name']))
            $log = file_get_contents($_FILES['svc']['tmp_name']);
        else 
            $log = file_get_contents('../workspace/ACK/Input/Nagios/services.txt');

        $Infos = $this->csv2array($log);
        $update_time = new \DateTime();
        
        $em = $this->getDoctrine()->getManager();
        $n = 0;
        foreach ($Infos as $Info) {
            
            $host_name = $Info['host_name'];
            // on retourne le composant réseau
            $host = $this->getDoctrine()->getRepository("AriiACKBundle:Network")->findOneBy(
            [
                'name' => $host_name
            ]);
            // Pas d'hote, on sort
            if (!$host) continue;
            
            // On boucle sur les services
            foreach ($Info['host_services_with_fullstate'] as $k=>$Service) {

                list($service_name,$state,$ext,$description,$ack,$try,$attempt,$downtimes,$d,$e) = $Service;
                $id = $host_name.'#'.$service_name;
                
                // on retrouve le service
                $record = $this->getDoctrine()->getRepository("AriiACKBundle:Service")->findOneBy(
                [
                    'name' => $id,
                    'host' => $host
                ]);

                if (!$record) {
                    $record = new \Arii\ACKBundle\Entity\Service();
                    $record->setName($id);                    
                    // c'est ouvert
                    $record->setStateTime(new \DateTime());
                    // On calcul l'état initiale
                }
                
                // STATUS Nagios
                if ($state==0) {
                    $status = 'OK';
                    $record->setState('CLOSE');
                } 
                elseif ($state==1) {
                    $status = 'WARNING';
                    $record->setState('OPEN');
                } 
                elseif ($state==4) {
                    $status = 'ERROR';
                    $record->setState('OPEN');
                } 
                else {
                    $status = 'UNKNOWN';
                    $record->setState('OPEN');                    
                }
                
                $record->setStatusTime($update_time);
                
                // on attache le host
                $host_name = $Info['host_name'];
                
                $record->setHostName($host_name);
                $record->setHost($host);

                // si aucun object n'est attaché
                if (!$record->getObject()) {
                    // on retrouve l'objet SERVICE
                    $host_service = $this->getDoctrine()->getRepository("AriiACKBundle:Object")->findOneBy(
                    [
                        'name' =>$id,
                        'obj_type' => 'SERVICE'
                    ]);

                    if (!$host_service) {
                        $host_service = new \Arii\ACKBundle\Entity\Object();
                        $host_service->setName($id);
                        $host_service->setTitle($service_name);
                        $host_service->setObjType('SERVICE');
                        $host_service->setDescription($description);

                        $em->persist($host_service);                    
                    }
                    $record->setObject($host_service);
                }

                // plusieurs Nagios ?
                $record->setEventSource('NAGIOS');
                $record->setTitle                ($service_name);
                $record->setDescription          ($description);
                $record->setAcknowledged         ($ack);

                // Chaine speciale
                $record->setDowntimes($downtimes);

                $em->persist($record);
            }
            if ($n++ % 100 == 0)
                $em->flush();            
        }
        
        // $update_time
        $em->flush();
        
        // Purge
        $this->getDoctrine()->getRepository("AriiACKBundle:Service")->purge($update_time);        
        
        return new Response("success");        
    }
    
    // Symfony3 offre un serializer
    private function csv2array($log,$sep_col=1,$sep_list=2,$sep_hs=3,$sep_line="\n") {
        $Infos = explode($sep_line,$log);
        $header = array_shift($Infos);
        $Columns = explode(chr($sep_col),$header);
        
        $Array = [];
        $limit=50;
        foreach ($Infos as $line) {
            $i=0;
            foreach (explode(chr($sep_col),$line) as $v) {
                if (!isset($Columns[$i]))
                    continue;
                
                $c = $Columns[$i];
                // Tableau de tableau ?
                if (strpos($v, $sep_hs)) {
                    $Lines = [];
                    foreach (explode (chr ($sep_list), $v) as $hs) {
                        array_push($Lines, explode(chr($sep_hs), $hs));
                    }
                    $New[$c] = $Lines;
                }
                elseif (strpos($v, $sep_list)) {
                    $New[$c] = explode(chr($sep_list),$v);                    
                }
                // Valeur
                else {
                    $New[$c] = $v;
                }                
                $i++;
            }
            array_push($Array,$New);
            // if ($limit-- <= 0) break;
        }
        return $Array;
    }
    
}

