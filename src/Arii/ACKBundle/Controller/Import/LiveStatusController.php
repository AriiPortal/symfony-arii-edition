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

            $record = $this->getDoctrine()->getRepository("AriiACKBundle:Host")->findOneBy(
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
                $status = 'CRITICAL';
            } 
            else {
                $status = 'UNKNOWN';
            }
     
            // Status UNKNOWN, c'est utile ?
            if ($status == 'UNKNOWN') continue;
                                    
            if (!$record) {
                $record = new \Arii\ACKBundle\Entity\Host();
                
                // Verification IP
                // Il faudra distinguer synchro rapide et màj
                if (filter_var($Info['address'], FILTER_VALIDATE_IP)) {
                    $record->setIPAddress            ($Info['address']);
                } else {
                    $record->setIPAddress            (gethostbyname($Info['address']));
                }                

            }
            
            // si aucun Probe n'est attaché
            if (!$record->getProbe()) {
                // on retrouve l'objet HOST
                $host = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->findOneBy(
                [
                    'name' => $Info['name'],
                    'obj_type' => 'HOST'
                ]);
                
                if (!$host) {
                    $host = new \Arii\ACKBundle\Entity\Probe();
                    $host->setName($Info['name']);
                    $host->setTitle($Info['name']);
                    $host->setSource('HOST');
                    $host->setObjType('HOST');
                    $host->setDescription($Info['alias']);
                    
                    $host->setState($status);
                    $host->setStateTime(new \DateTime('@'.$Info['last_check']));
                    
                    $host->setStatus($status);
                    $host->setUpdated(new \DateTime());
                    
                    $em->persist($host);                    
                }
                $record->setProbe($host);
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
            
            $record->setEventSource('NAGIOS');
            $record->setState($status);
            $record->setStateTime(new \DateTime('@'.$Info['last_check'])); 
                        
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
    
/* Exemple de service

accept_passive_checks	0
acknowledged	1
acknowledgement_type	2
action_url	
action_url_expanded	
active_checks_enabled	1
cache_interval	0
cached_at	0
check_command	check_nrpe_windows_2_args!check_scheduledjoblog!C:/\Log/\Smoketest/\Connectique_Animalia_07h00
check_command_expanded	check_nrpe_windows_2_args!check_scheduledjoblog!C:/\Log/\Smoketest/\Connectique_Animalia_07h00
check_freshness	0
check_interval	5.00E+00
check_options	0
check_period	TP_05h30-05h35
check_type	0
checks_enabled	1
comments	7975381504
comments_with_extra_info	79753send_nscadi.vaudoise.ch/browse/EXPLOIT-808694153706873681504AIJhttps://di.vaudoise.ch/browse/EXPLOIT-8104241537332707
comments_with_info	79753send_nscadi.vaudoise.ch/browse/EXPLOIT-8086981504AIJhttps://di.vaudoise.ch/browse/EXPLOIT-81042
contact_groups	CG_JIRA_Notification
contacts	Jira
current_attempt	2
current_notification_number	0
custom_variable_names	CRITICALITY_LEVELCRITICALITY_ID
custom_variable_values	35
custom_variables	CRITICALITY_LEVEL3CRITICALITY_ID5
description	Sched_Job_CheckSmokeTestRunConnectiqueAnimalia
display_name	
downtimes	
downtimes_with_info	
event_handler	
event_handler_enabled	1
execution_time	4.41E-01
first_notification_delay	0.00E+00
flap_detection_enabled	1
groups	SG_Exploitation_Notification
has_been_checked	1
high_flap_threshold	0.00E+00
host_accept_passive_checks	0
host_acknowledged	0
host_acknowledgement_type	0
host_action_url	
host_action_url_expanded	
host_active_checks_enabled	1
host_address	sgitexploit1
host_alias	IT management Exploit
host_check_command	check_host_alive
host_check_command_expanded	check_host_alive
host_check_flapping_recovery_notification	0
host_check_freshness	0
host_check_interval	5.00E+00
host_check_options	0
host_check_period	TP_24x7
host_check_type	0
host_checks_enabled	1
host_childs	
host_comments	
host_comments_with_extra_info	
host_comments_with_info	
host_contact_groups	
host_contacts	
host_current_attempt	1
host_current_notification_number	0
host_custom_variable_names	SNMPVERSIONSNMPCOMMUNITY
host_custom_variable_values	2cpublic
host_custom_variables	SNMPVERSION2cSNMPCOMMUNITYpublic
host_display_name	sgitexploit1
host_downtimes	
host_downtimes_with_info	
host_event_handler	
host_event_handler_enabled	1
host_execution_time	8.57E-02
host_filename	
host_first_notification_delay	0.00E+00
host_flap_detection_enabled	1
host_groups	HG_Windows_2012HG_WindowsHG_Prod_OS_Windows_2012HG_Prod_OS_WindowsHG_Prod_HW_Virt_VMwareESXHG_Prod_HW_VirtHG_ProdHG_HW_Virt_VMwareESXHG_HW_VirtHG_ALL
host_hard_state	0
host_has_been_checked	1
host_high_flap_threshold	0.00E+00
host_icon_image	win-2012/Windows-logo-2012.png
host_icon_image_alt	
host_icon_image_expanded	win-2012/Windows-logo-2012.png
host_in_check_period	1
host_in_notification_period	1
host_in_service_period	1
host_initial_state	0
host_is_executing	0
host_is_flapping	0
host_last_check	1537345471
host_last_hard_state	0
host_last_hard_state_change	1525192320
host_last_notification	0
host_last_state	0
host_last_state_change	1530644505
host_last_time_down	1530644497
host_last_time_unreachable	0
host_last_time_up	1537345476
host_latency	1.41E-01
host_long_plugin_output	
host_low_flap_threshold	0.00E+00
host_max_check_attempts	5
host_metrics	
host_mk_inventory	
host_mk_inventory_gz	
host_mk_inventory_last	0
host_modified_attributes	0
host_modified_attributes_list	
host_name	sgitexploit1
host_next_check	1537345776
host_next_notification	0
host_no_more_notifications	1
host_notes	
host_notes_expanded	
host_notes_url	
host_notes_url_expanded	
host_notification_interval	0.00E+00
host_notification_period	TP_24x7
host_notifications_enabled	1
host_num_services	35
host_num_services_crit	2
host_num_services_hard_crit	2
host_num_services_hard_ok	33
host_num_services_hard_unknown	0
host_num_services_hard_warn	0
host_num_services_ok	33
host_num_services_pending	0
host_num_services_unknown	0
host_num_services_warn	0
host_obsess_over_host	1
host_parents	
host_pending_flex_downtime	0
host_percent_state_change	0.00E+00
host_perf_data	rta=0.232ms;3000.000;5000.000;0; pl=0%;80;100;; rtmax=0.232ms;;;; rtmin=0.232ms;;;;
host_plugin_output	OK - sgitexploit1: rta 0.232ms, lost 0%
host_pnpgraph_present	-1
host_process_performance_data	1
host_retry_interval	1.00E+00
host_scheduled_downtime_depth	0
host_service_period	
host_services	UptimeSymantec_AV_Definition_AgeServicesSched_Job_Datadomain_Stat_MtreeSched_Job_DataDomainDecoMacSched_Job_CtrlSOGPostFinanceSched_Job_CtrlDecaissementSched_Job_CtrlDTCM630Sched_Job_CtrlChiffresSched_Job_CtrlBatchSoireeSched_Job_CtrlBRMSSched_Job_ControleListeAgencesSched_Job_CleanupMailboxFoldersSched_Job_CheckSmokeTestTransacSched_Job_CheckSmokeTestRunOffresAIASched_Job_CheckSmokeTestRunConnectiqueAnimaliaSched_Job_CheckSmokeTestPortailSched_Job_CheckSmokeTestOffresAIASched_Job_CheckSmokeTestDctm630Sched_Job_CheckSmokeTestDctm600Sched_Job_CheckSmokeTestConnectiqueAnimaliaSched_Job_CheckPrintJobStatusSched_Job_AutoPrestSched_Job_Admin-DDSched_Job_ADMemberGroupPingPaging_File_usageOS_VersionNTPNRPE_StatusMemoryEvent_Log_SysEvent_Log_ApplDisksCPU
host_services_with_fullstate	Uptime01OK: uptime: 2w 1d 07:50h, boot: 2018-sept.-04 00:30:16 (UTC)013001Symantec_AV_Definition_Age01Current Definitions: 2018-9-17 rev. 24 Which are 1 days old011001Services01OK: All 57 service(s) are ok.013001Sched_Job_Datadomain_Stat_Mtree01OK: No Errors011001Sched_Job_DataDomainDecoMac01OK: No Errors012001Sched_Job_CtrlSOGPostFinance01OK: No Errors011001Sched_Job_CtrlDecaissement01OK: No Errors011001Sched_Job_CtrlDTCM63001OK: No Errors011001Sched_Job_CtrlChiffres01OK: Status OK suite à Acknwowledgement - Sonde Passive - Ne PAS faire Force Check manuellement.011001Sched_Job_CtrlBatchSoiree01OK: No Errors011001Sched_Job_CtrlBRMS01OK: No Errors012001Sched_Job_ControleListeAgences01OK: OK: Status OK suite à Acknwowledgement - Sonde Passive - Ne PAS faire Force Check manuellement.011001Sched_Job_CleanupMailboxFolders01OK: No Errors012001Sched_Job_CheckSmokeTestTransac01OK: No Errors012001Sched_Job_CheckSmokeTestRunOffresAIA01OK: No Errors012001Sched_Job_CheckSmokeTestRunConnectiqueAnimalia21CRITICAL:222011Sched_Job_CheckSmokeTestPortail01OK: No Errors012001Sched_Job_CheckSmokeTestOffresAIA01OK: No Errors012001Sched_Job_CheckSmokeTestDctm63001OK: No Errors012001Sched_Job_CheckSmokeTestDctm60001OK: No Errors012001Sched_Job_CheckSmokeTestConnectiqueAnimalia21CRITICAL:222011Sched_Job_CheckPrintJobStatus01OK: No Errors012001Sched_Job_AutoPrest01OK - Time to get page: 0.0781254 secondes for http://va400p1:10050/web/services/checkAutoPrestCase?CaseNumber=319118&CaseYear=2018 with message : return code OK012001Sched_Job_Admin-DD01Ok011001Sched_Job_ADMemberGroup01Ok011001Ping01OK - sgitexploit1: rta 0.892ms, lost 0%012001Paging_File_usage01Paging File usage is 17.00 %013001OS_Version01OK: Windows 2012 (6.2.9200)013001NTP01OK: Time is -00.2972101s from adds.vaudoise.ch013001NRPE_Status01I (0.4.4.19 2015-12-08) seem to be doing fine...013001Memory01OK: committed = 7.046GB, physical = 5.005GB013001Event_Log_Sys01No Errors or Warnings were found in the System event logs as of 19.9.2018 10:22:25013001Event_Log_Appl01No Errors or Warnings were found in the Application event logs as of 19.9.2018 10:22:02013001Disks01OK All 1 drive(s) are ok013001CPU01OK: CPU load is ok.013001
host_services_with_info	Uptime01OK: uptime: 2w 1d 07:50h, boot: 2018-sept.-04 00:30:16 (UTC)Symantec_AV_Definition_Age01Current Definitions: 2018-9-17 rev. 24 Which are 1 days oldServices01OK: All 57 service(s) are ok.Sched_Job_Datadomain_Stat_Mtree01OK: No ErrorsSched_Job_DataDomainDecoMac01OK: No ErrorsSched_Job_CtrlSOGPostFinance01OK: No ErrorsSched_Job_CtrlDecaissement01OK: No ErrorsSched_Job_CtrlDTCM63001OK: No ErrorsSched_Job_CtrlChiffres01OK: Status OK suite à Acknwowledgement - Sonde Passive - Ne PAS faire Force Check manuellement.Sched_Job_CtrlBatchSoiree01OK: No ErrorsSched_Job_CtrlBRMS01OK: No ErrorsSched_Job_ControleListeAgences01OK: OK: Status OK suite à Acknwowledgement - Sonde Passive - Ne PAS faire Force Check manuellement.Sched_Job_CleanupMailboxFolders01OK: No ErrorsSched_Job_CheckSmokeTestTransac01OK: No ErrorsSched_Job_CheckSmokeTestRunOffresAIA01OK: No ErrorsSched_Job_CheckSmokeTestRunConnectiqueAnimalia21CRITICAL:Sched_Job_CheckSmokeTestPortail01OK: No ErrorsSched_Job_CheckSmokeTestOffresAIA01OK: No ErrorsSched_Job_CheckSmokeTestDctm63001OK: No ErrorsSched_Job_CheckSmokeTestDctm60001OK: No ErrorsSched_Job_CheckSmokeTestConnectiqueAnimalia21CRITICAL:Sched_Job_CheckPrintJobStatus01OK: No ErrorsSched_Job_AutoPrest01OK - Time to get page: 0.0781254 secondes for http://va400p1:10050/web/services/checkAutoPrestCase?CaseNumber=319118&CaseYear=2018 with message : return code OKSched_Job_Admin-DD01OkSched_Job_ADMemberGroup01OkPing01OK - sgitexploit1: rta 0.892ms, lost 0%Paging_File_usage01Paging File usage is 17.00 %OS_Version01OK: Windows 2012 (6.2.9200)NTP01OK: Time is -00.2972101s from adds.vaudoise.chNRPE_Status01I (0.4.4.19 2015-12-08) seem to be doing fine...Memory01OK: committed = 7.046GB, physical = 5.005GBEvent_Log_Sys01No Errors or Warnings were found in the System event logs as of 19.9.2018 10:22:25Event_Log_Appl01No Errors or Warnings were found in the Application event logs as of 19.9.2018 10:22:02Disks01OK All 1 drive(s) are okCPU01OK: CPU load is ok.
host_services_with_state	Uptime01Symantec_AV_Definition_Age01Services01Sched_Job_Datadomain_Stat_Mtree01Sched_Job_DataDomainDecoMac01Sched_Job_CtrlSOGPostFinance01Sched_Job_CtrlDecaissement01Sched_Job_CtrlDTCM63001Sched_Job_CtrlChiffres01Sched_Job_CtrlBatchSoiree01Sched_Job_CtrlBRMS01Sched_Job_ControleListeAgences01Sched_Job_CleanupMailboxFolders01Sched_Job_CheckSmokeTestTransac01Sched_Job_CheckSmokeTestRunOffresAIA01Sched_Job_CheckSmokeTestRunConnectiqueAnimalia21Sched_Job_CheckSmokeTestPortail01Sched_Job_CheckSmokeTestOffresAIA01Sched_Job_CheckSmokeTestDctm63001Sched_Job_CheckSmokeTestDctm60001Sched_Job_CheckSmokeTestConnectiqueAnimalia21Sched_Job_CheckPrintJobStatus01Sched_Job_AutoPrest01Sched_Job_Admin-DD01Sched_Job_ADMemberGroup01Ping01Paging_File_usage01OS_Version01NTP01NRPE_Status01Memory01Event_Log_Sys01Event_Log_Appl01Disks01CPU01
host_staleness	1.17E-01
host_state	0
host_state_type	1
host_statusmap_image	
host_total_services	35
host_worst_service_hard_state	2
host_worst_service_state	2
host_x_3d	0.00E+00
host_y_3d	0.00E+00
host_z_3d	0.00E+00
icon_image	
icon_image_alt	
icon_image_expanded	
in_check_period	0
in_notification_period	1
in_service_period	1
initial_state	0
is_executing	0
is_flapping	0
last_check	1537332707
last_hard_state	2
last_hard_state_change	1537332707
last_notification	0
last_state	2
last_state_change	1537327800
last_time_critical	1537332707
last_time_ok	1537241400
last_time_unknown	0
last_time_warning	0
latency	1.10E+00
long_plugin_output	File is older thant 1 day!
low_flap_threshold	0.00E+00
max_check_attempts	2
metrics	
modified_attributes	0
modified_attributes_list	
next_check	1537414200
next_notification	0
no_more_notifications	0
notes	Expl
notes_expanded	Expl
notes_url	https://wiki.vaudoise.ch/display/Monitoring/Sched_Job_CheckSmokeTestRunConnectiqueAnimalia
notes_url_expanded	https://wiki.vaudoise.ch/display/Monitoring/Sched_Job_CheckSmokeTestRunConnectiqueAnimalia
notification_interval	0.00E+00
notification_period	TP_JIRA_24x7
notifications_enabled	1
obsess_over_service	1
percent_state_change	1.76E+01
perf_data	
plugin_output	CRITICAL:
pnpgraph_present	-1
process_performance_data	1
retry_interval	5.00E+00
scheduled_downtime_depth	0
service_period	
staleness	4.27E+01
state	2
state_type	1

 */
    public function servicesAction()
    {
        set_time_limit(1800);        
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
            
            // on considère le hostname unique
            $host_name = $Info['host_name'];
            // on retourne le composant réseau
            $host = $this->getDoctrine()->getRepository("AriiACKBundle:Host")->findOneBy(
            [
                'name' => $host_name
            ]);
            // On verifie l'état 
            
            // Pas d'hote, on sort
            if (!$host) {
                print "? $host\n";
                continue;
            }
            
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
                    $record->setHostName($host_name);
                    $record->setHost($host);
                }
                
                // STATUS Nagios
                if ($state==0) {
                    $status = 'OK';
                } 
                elseif ($state==1) {
                    $status = 'WARNING';
                } 
                elseif ($state==4) {
                    $status = 'CRITICAL';
                } 
                else {
                    $status = 'UNKNOWN';
                }

                $record->setState($status);
                $record->setStateTime(new \DateTime('@'.$Info['last_check']));
                
                // si aucun Probe n'est attaché
                if (!$record->getProbe()) {
                    // on retrouve l'objet SERVICE
                    $host_service = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->findOneBy(
                    [
                        'name' =>$id,
                        'obj_type' => 'SERVICE'
                    ]);

                    if (!$host_service) {
                        $host_service = new \Arii\ACKBundle\Entity\Probe();
                        $host_service->setName($id);
                        $host_service->setTitle($service_name);
                        $host_service->setObjType('SERVICE');
                        $host_service->setSource('SERVICE');
                        $host_service->setDescription($description);

                        $host_service->setState($status);
                        $host_service->setStateTime(new \DateTime('@'.$Info['last_check']));

                        // On remonte tout de suite l'état par défaut (celui de Nagios)
                        $host_service->setStatus($status);
                        $host_service->setUpdated(new \DateTime());
                        
                        $em->persist($host_service);                        
                    }
                    $record->setProbe($host_service);
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
        // $this->getDoctrine()->getRepository("AriiACKBundle:Service")->purge($update_time);        
        print "(+$n)";
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

