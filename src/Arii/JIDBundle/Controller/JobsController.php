<?php

namespace Arii\JIDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class JobsController extends Controller
{
    protected $images;
    protected $ColorStatus = array (
            'SUCCESS' => '#ccebc5',
            'RUNNING' => '#ffffcc',
            'FAILURE' => '#fbb4ae',
            'STOPPED' => '#FF0000',
            'QUEUED' => '#AAA',
            'STOPPING' => '#ffffcc',
            'UNKNOW' => '#BBB'
        );

    public function __construct( )
    {
          $request = Request::createFromGlobals();
          $this->images = $request->getUriForPath('/../bundles/ariicore/images/wa');
    }

    public function indexAction()
    {
        $portal = $this->container->get('arii_core.portal');
        if (!$portal->getDatabase())
            return $this->render('AriiJIDBundle:Default:index.html.twig');
        
        $User = $portal->getUserInterface();
        
        $request = Request::createFromGlobals();
        if ($request->query->get( 'refDate' )!='')
            $User['refDate'] = $request->query->get( 'refDate' );
            
        $refDate = $User['refDate'];
        $past   = $User['past'];
        $future = $User['future'];
        $refresh = $User['refresh'];
        
        $portal->getUserInterface($User);
        
        $Timeline['refDate'] = $refDate;

        // On prend 24 fuseaux entre maintenant et le passe
        // on trouve le step en minute
        $step = ($future-$past)*2.5; // heure * 60 minutes / 24 fuseaux
        if ($step == 0) $step = 1;
        $Timeline['step'] = 60;

        // on recalcule la date courante moins la plage de passé
        $year = $refDate->format('Y');
        $month = $refDate->format('m');
        $day = $refDate->format('d');

        $start = substr($past,11,2);
        $Timeline['start'] = (60/$step)*$start;
        $Timeline['js_date'] = $year.','.($month - 1).','.$day;

        $Timeline['start'] = 0;
        
        return $this->render('AriiJIDBundle:Jobs:index.html.twig', array('refresh' => $refresh, 'Timeline' => $Timeline ) );
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        return $this->render('AriiJIDBundle:Jobs:toolbar.xml.twig',array(), $response );
    }

    public function folder_toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJIDBundle:Jobs:folder_toolbar.xml.twig',array(), $response );
    }

    public function grid_toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJIDBundle:Jobs:grid_toolbar.xml.twig',array(), $response );
    }

    public function formAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $this->render('AriiJIDBundle:Jobs:form.json.twig',array(), $response );
    }

    public function form2Action()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJIDBundle:Jobs:form.xml.twig',array(), $response );
    }

    public function form_toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJIDBundle:Jobs:form_toolbar.xml.twig',array(), $response );
    }

    public function grid_menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJIDBundle:Jobs:grid_menu.xml.twig',array(), $response );
    }

    public function treeAction() {
        $request = Request::createFromGlobals();
        $ordered = $request->get('ordered');
        $stopped = $request->get('stopped');

        // Historique des jobs
        $history = $this->container->get('arii_jid.history');
        $Info = $history->Jobs(0,$ordered,false);
        
        $key_files = array();
        foreach ( $Info as $id=>$line ) {
            $dir = "/".$line['spooler'].'/'.dirname($line['name']);
            if ($line['error']>0) {
                if (isset($Info[$dir]['errors']))
                    $Info[$dir]['errors']++;
                else
                    $Info[$dir]['errors']=1;
            }
            // On ccompte les erreurs
            $key_files[$dir] = $dir;
        }
        
        // print_r($Info);
        # On remonte les erreurs
        foreach (array_keys($Info) as $dir) {
            // On calcule le nombre
            $n = 0;
            if (isset($Info[$dir]['errors']))
                $n += $Info[$dir]['errors'];
            if (isset($Info[$dir]['stopped']))
                $n += $Info[$dir]['stopped'];
            // print "(($n))";
            // si c'est superieur a 0, on le remonte
            if ($n>0) {
                $Path = explode('/',$dir);
                array_shift($Path);
                $dir = '';
                foreach ($Path as $p) {
                    $dir .= '/'.$p;
                    if (isset($Info[$dir]['total']))
                        $Info[$dir]['total'] += $n;
                    else
                        $Info[$dir]['total'] = $n;
                }
            }
        }
        ksort($Info);
        /*
         *     print_r($Info);
            exit();
         */

        $tools = $this->container->get('arii_core.tools');
        $tree = $tools->explodeTree($key_files, "/");

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<tree id='0'>\n";

        $list .= $this->Folder2XML( $tree, '', $Info );
        $list .= "</tree>\n";
        $response->setContent( $list );
        return $response;
    }

   function Folder2XML( $leaf, $id = '', $Info ) {
            $return = '';
            if (is_array($leaf)) {
                    foreach (array_keys($leaf) as $k) {
                            $Ids = explode('/',$k);
                            $here = array_pop($Ids);
                            $i  = "$id/$k";
                            # On ne prend que l'historique
                            if (isset($Info[$i]['stopped']) and (isset($Info[$i]['errors']))) {
                                $return .= '<item style="background-color: red;" id="'.$i.'" text="'.basename($i).' ['.($Info[$i]['errors']+$Info[$i]['stopped']).']" im0="folderOpen.gif">';
                            }
                            elseif (isset($Info[$i]['errors'])) {
                                $return .= '<item style="background-color: #fbb4ae;" id="'.$i.'" text="'.basename($i).' ['.$Info[$i]['errors'].']" im0="folderOpen.gif">';
                            }
                            elseif (isset($Info[$i]['stopped'])) {
                                $return .= '<item style="background-color: red;" id="'.$i.'" text="'.basename($i).' ['.$Info[$i]['stopped'].']" im0="folderOpen.gif">';
                            }
                            elseif (isset($Info[$i]['total'])) {
                                $return .= '<item id="'.$i.'" text="'.basename($i).' ['.$Info[$i]['total'].']" im0="folderOpen.gif"  open="1">';
                            }
                            else {
                                $return .=  '<item style="background-color: #ccebc5;" id="'.$i.'" text="'.basename($i).'" im0="folderClosed.gif">';
                            }
                           $return .= $this->Folder2XML( $leaf[$k], $id.'/'.$k, $Info);
                           $return .= '</item>';
                    }
            }
            return $return;
    }
//...................................................;;
// New
//....................................................
    public function form_dateAction(){
      $response = new Response();
      $response->headers->set('Content-Type', 'text/xml');
      return $this->render('AriiJIDBundle:Jobs:form_date.xml.twig',array(), $response );
    }

    public function gridChainAction(Request $request, $bool){
      $request->getSession()->set('bool', $bool);
      //echo "bool: ". $request->getSession()->get('bool');
      //return new Response("bool : ".$request->getSession()->get('bool'));
      return  $this->redirectToRoute('xml_JID_jobs_grid');
    }


    public function gridAllAction(Request $request, $bool){
      $request->getSession()->set('viewAll', $bool );
      // en refresh grid
      return  $this->redirectToRoute('xml_JID_jobs_grid');
      //return  $this->redirectToRoute('arii_JID_jobs');
    }

    //change les variable de session past et future
    public function gridDatedAction(Request $request, $date1, $date2){
      $request->getSession()->set('past', $date1 );
      $request->getSession()->set('future', $date2 );
      //return new Response("ok \n future : ".$request->getSession()->get('future') ." \n past : ".$request->getSession()->get('past'));
      return  $this->redirectToRoute('xml_JID_jobs_grid');
      //return  $this->redirectToRoute('arii_JID_jobs');
      //return $this->render('AriiJIDBundle:Jobs:index.html.twig', array('refresh' => $refresh, 'Timeline' => $Timeline ) );
    }

    public function gridAction($history_max=0,$ordered = 0,$stopped=1) {

        $request = Request::createFromGlobals();
        if ($request->get('history')>0) {
            $history_max = $request->get('history');
        }
        $ordered = $request->get('chained');
        $stopped = $request->get('onlyWarning');

        $history = $this->container->get('arii_jid.history');

        //echo "date1 : ".$date1." date2 : ".$date2." ";
        $Jobs = $history->Jobs(0, $ordered, $stopped, false);

        $tools = $this->container->get('arii_core.tools');

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        $list .= '<head>
        <afterInit>
        <call command="clearAll"/>
        </afterInit>
        </head>';
        ksort($Jobs);

        foreach ($Jobs as $k=>$job) {
            
            $status = $job['status'];
            
            $list .='<row id="'.$job['dbid'].'" style="background-color: '.$this->ColorStatus[$status].'">';
            $list .='<cell>'.$job['spooler'].'</cell>';
            $list .='<cell>'.$job['folder'].'</cell>';

            $list .='<cell>'.$job['name'].'</cell>';
            $list .='<cell>'.$status.'</cell>';
            $list .='<cell>'.$this->images.'/'.strtolower($status).'.png</cell>';
            $list .='<cell>'.$job['start'].'</cell>';
            $list .='<cell>'.$job['end'].'</cell>';
            $list .='<cell>'.$job['duration'].'</cell>';
            $list .='<cell>'.$job['exit'].'</cell>';
            $list .='<cell><![CDATA[<img src="'.$this->generateUrl('png_JID_gantt').'?'.$tools->Gantt($job['start'],$job['end'],$status).'"/>]]></cell>';
            $list .='<cell>'.$job['pid'].'</cell>';
            $list .='<cell>'.$this->images.'/'.strtolower($job['cause']).'.png</cell>';
            $list .='</row>';
        }

        $list .= "</rows>\n";
        $response->setContent( $list );
        return $response;
    }

    public function pieAction($history_max=0,$ordered = 0,$onlyWarning=1) {
        
         $color = array (
             'SUCCESS' => '#ccebc5',
             'RUNNING' => '#ffffcc',
             'FAILURE' => '#fbb4ae',
             'STOPPED' => '#FF0000',
             'QUEUED' => '#AAA',
             'STOPPING' => '#ffffcc'
         );
         $request = Request::createFromGlobals();
         if ($request->get('history')>0) {
             $history_max = $request->get('history');
         }
         $ordered = $request->get('chained');
         $onlyWarning = $request->get('onlyWarning');
         $history = $this->container->get('arii_jid.history');
         $Jobs = $history->Jobs(0, $ordered, $onlyWarning, false);
         $stopped=$success=$failure=$running=0;
         foreach ($Jobs as $k=>$job) {
             if (isset($job['stopped'])) {
                 $stopped += 1;
             }
             if (isset($job['status'])) {
                 $status = $job['status'];
                 switch ($status) {
                     case 'SUCCESS':
                         $success += 1;
                         break;
                     case 'FAILURE':
                         if (!$stopped)
                             $failure += 1;
                         break;
                     case 'RUNNING':
                         $running += 1;
                         break;
                 }
             }
         }

         $pie = '<data>';
         $pie .= '<item id="SUCCESS"><STATUS>SUCCESS</STATUS><JOBS>'.$success.'</JOBS><COLOR>#ccebc5</COLOR></item>';
         $pie .= '<item id="FAILURE"><STATUS>FAILURE</STATUS><JOBS>'.$failure.'</JOBS><COLOR>#fbb4ae</COLOR></item>';
         $pie .= '<item id="STOPPED"><STATUS>STOPPED</STATUS><JOBS>'.$stopped.'</JOBS><COLOR>red</COLOR></item>';
         $pie .= '<item id="RUNNING"><STATUS>RUNNING</STATUS><JOBS>'.$running.'</JOBS><COLOR>#ffffcc</COLOR></item>';
         $pie .= '</data>';
         $response = new Response();
         $response->headers->set('Content-Type', 'text/xml');
         $response->setContent( $pie );
         return $response;
     }

    public function barAction()
    {
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');

        $sql = $this->container->get('arii_core.sql');
        $Fields = array (
            '{spooler}'    => 'SPOOLER_ID',
            '{start_time}' => 'START_TIME',
            '{job_name}'   => 'JOB_NAME');

        $qry = $sql->Select(array('ID','START_TIME','END_TIME','ERROR','CAUSE','SPOOLER_ID'))
                .$sql->From(array('SCHEDULER_HISTORY'))
                .$sql->Where($Fields)
                .$sql->OrderBy(array('START_TIME desc'));

        $res = $data->sql->query( $qry );
        // Par jour
        // Attention on est en heure GMT, il y a donc un décalage
        $date = $this->container->get('arii_core.date');
        while ($line = $data->sql->get_next($res)) {
            # On recupere les heures
            $day = substr($date->Date2Local($line['START_TIME'],$line['SPOOLER_ID']),8,5);
            $Days[$day]=1;
            if ($line['CAUSE']=='order') {
                if ($line['END_TIME']='') {
                    if (isset($HRO[$day]))
                        $HRO[$day]++;
                    else $HRO[$day]=1;
                }
                else {
                    if ($line['ERROR']==0) {
                        if (isset($HSO[$day]))
                            $HSO[$day]++;
                        else $HSO[$day]=1;
                    }
                    else {
                        if (isset($HFO[$day]))
                            $HFO[$day]++;
                        else $HFO[$day]=1;
                    }
                }
            }
            else {
                if ($line['END_TIME']='') {
                    if (isset($HR[$day]))
                        $HR[$day]++;
                    else $HR[$day]=1;
                }
                else {
                    if ($line['ERROR']==0) {
                        if (isset($HS[$day]))
                            $HS[$day]++;
                        else $HS[$day]=1;
                    }
                    else {
                        if (isset($HF[$day]))
                            $HF[$day]++;
                        else $HF[$day]=1;
                    }
                }
            }
        }
        $bar = "<?xml version='1.0' encoding='utf-8' ?>";
        $bar .= '<data>';
        if (isset($Days)) {
            foreach($Days as $i=>$v) {
                if (!isset($HS[$i])) $HS[$i]=0;
                if (!isset($HF[$i])) $HF[$i]=0;
                if (!isset($HR[$i])) $HR[$i]=0;
                if (!isset($HSO[$i])) $HSO[$i]=0;
                if (!isset($HFO[$i])) $HFO[$i]=0;
                if (!isset($HRO[$i])) $HRO[$i]=0;
                $bar .= '<item id="'.$i.'"><HOUR>'.substr($i,-2).'</HOUR>';
                $bar .= '<SUCCESS>'.$HS[$i].'</SUCCESS><FAILURE>'.$HF[$i].'</FAILURE><RUNNING>'.$HR[$i].'</RUNNING>';
                $bar .= '<SUCCESS_ORDER>'.$HSO[$i].'</SUCCESS_ORDER><FAILURE_ORDER>'.$HFO[$i].'</FAILURE_ORDER><RUNNING_ORDER>'.$HRO[$i].'</RUNNING_ORDER>';
                $bar .= '</item>';
            }
        }
        $bar .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $bar );
        return $response;
    }


    public function pieFullAction($ordered=0)
    {
        $request = Request::createFromGlobals();
        if ($request->get('ordered')=='true') {
            $ordered = $request->get('ordered');
        }

        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');

        $sql = $this->container->get('arii_core.sql');
        $Fields = array (
            '{spooler}'    => 'SPOOLER_ID',
            '{start_time}' => 'START_TIME',
            '{end_time}'   => 'END_TIME',
            '{!(spooler)}' => 'JOB_NAME' );
        if ($ordered!='true') {
            $Fields['{standalone}'] = 'sh.CAUSE';
        }
        $qry = $sql->Select(array('count(ID) as NB','getdate(START_TIME) as START_TIME','getdate(END_TIME) as END_TIME','ERROR'))
        .$sql->From(array('SCHEDULER_HISTORY'))
        .$sql->Where($Fields)
        .$sql->GroupBy(array('getdate(START_TIME)','getdate(END_TIME)','ERROR'));


        //echo 'query : ' . $qry;

        $res = $data->sql->query( $qry );
        $running = $success = $failure = 0;
        while ($line = $data->sql->get_next($res)) {
            $nb = $line['NB'];
            if ($line['END_TIME'] == '') {
                $running+=$nb;
            }
            elseif ($line['ERROR']==0) {
                $success+=$nb;
            }
            else {
                $failure+=$nb;
            }
        }
        $pie = '<data>';
        $pie .= '<item id="SUCCESS"><STATUS>SUCCESS</STATUS><JOBS>'.$success.'</JOBS><COLOR>#ccebc5</COLOR></item>';
        $pie .= '<item id="FAILURE"><STATUS>FAILURE</STATUS><JOBS>'.$failure.'</JOBS><COLOR>#fbb4ae</COLOR></item>';
        $pie .= '<item id="STOPPED"><STATUS>FAILURE</STATUS><JOBS>'.$stopped.'</JOBS><COLOR>red</COLOR></item>';
        $pie .= '<item id="RUNNING"><STATUS>RUNNING</STATUS><JOBS>'.$running.'</JOBS><COLOR>#ffffcc</COLOR></item>';
        $pie .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $pie );
        return $response;
    }

    public function timelineAction($history=0,$ordered=false)
    {
        $request = Request::createFromGlobals();
        if ($request->get('history')>0) {
            $history = $request->get('history');
        }
        if ($request->get('ordered')=='true') {
            $ordered = true;
        }

        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');

        $session =  $this->container->get('arii_core.session');
        $this->refDate  =  $session->getRefDate();
        $xml = '<data>';

        $sql = $this->container->get('arii_core.sql');
        $Fields = array (
            '{!(spooler)}' => 'JOB_NAME',
            '{spooler}'    => 'SPOOLER_ID',
            '{job_name}'   => 'JOB_NAME',
            '{error}'      => 'ERROR',
            '{start_time}' => 'START_TIME' );
        if (!$ordered) {
            $Fields['{standalone}'] = 'CAUSE';
        }
        
        // le passé
        $qry = $sql->Select(array('ID','SPOOLER_ID','JOB_NAME','START_TIME','END_TIME','ERROR','EXIT_CODE','CAUSE','PID'))
                  .$sql->From(array('SCHEDULER_HISTORY'))
                  .$sql->Where($Fields)
                  .$sql->OrderBy(array('SPOOLER_ID','JOB_NAME','START_TIME desc'));

        $res = $data->sql->query( $qry );
        while ($line = $data->sql->get_next($res)) {
            $id = $line['SPOOLER_ID'].'/'.$line['JOB_NAME'];
            if (isset($Nb[$id])) {
                $Nb[$id]++;
            }
            else {
                 $Nb[$id]=0;
            }
            if ($Nb[$id]>$history) continue;
            $xml .= '<event id="'.$line['ID'].'">';
            $start = new \DateTime($line['START_TIME']);
            print_r($start);
            exit();
            $xml .= '<start_date>'.$start->format('Y-m-d').'</start_date>';
            $textColor='yellow';
            if ($line['END_TIME']!='') {
                $xml .= '<end_date>'.$line['END_TIME'].'</end_date>';
                if ($line['ERROR']==0) {
                   if ($line['CAUSE']=='order') {
                       $color= 'lightgreen';
                   }
                   else {
                        $color= '#ccebc5';
                   }
                }
                else {
                   if ($line['CAUSE']=='order') {
                       $color= 'lightred';
                   }
                   else {
                        $color='#fbb4ae';
                   }
                }
                $xml .= '<text>'.$line['JOB_NAME'].' (exit '.$line['EXIT_CODE'].')</text>';
            }
            else {
                $xml .= '<end_date>'.gmdate("Y-M-d H:i:s").'</end_date>';
                $color = '#ffffcc';
                $xml .= '<text>'.$line['JOB_NAME'].' (pid '.$line['PID'].')</text>';
            }
            $xml .= '<section_id>'.$line['SPOOLER_ID'].'</section_id>';
            $xml .= '<color>'.$color.'</color>';
          //  $xml .= '<textColor>'.$textColor.'</textColor>';
            $xml .= '</event>';
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $xml );
        return $response;
    }


    public function detailsAction(){
      $list = '<?xml version="1.0" encoding="UTF-8"?>';
      $list .= '<job order="no" stop_on_error="no" title="Checking a DailySchedule with runs in history">
        <description>
          <include file="jobs/jobSchedulerCheckDailySchedule.xml"/>
        </description>
        <lock.use exclusive="yes" lock="JID"/>
        <script java_class="com.sos.dailyschedule.job.CheckDailyScheduleJSAdapterClass" java_class_path="" language="java"/>
        <run_time>
          <period begin="00:00" end="00:30"/>
        </run_time>
      </job>';
      $response->headers->set('Content-Type', 'text/xml');
      $response->setContent( $list );
      return $response;
    }

    public function atomAction($db='arii_db',$history_max=0,$ordered = 0,$stopped=1) 
    {        
        // forcer la base de données
        if ($db!="0") {
            $portal = $this->container->get('arii_core.portal');
            $portal->setDatabaseByName($db);        
        }
        
        $request = Request::createFromGlobals();
        if ($request->get('history')>0) {
            $history_max = $request->get('history');
        }
        $ordered = $request->get('chained');
        $stopped = $request->get('onlyWarning');

        $history = $this->container->get('arii_jid.history');

        $Jobs = $history->Jobs(0, $ordered, $stopped, false);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJIDBundle:RSS:default.atom.twig', array( 'Infos' => $Jobs), $response );
    }

    public function rssAction($db='arii_db',$history_max=0,$ordered = 0,$stopped=1) 
    {
        $portal = $this->container->get('arii_core.portal');
        
        $User = $portal->getUserInterface(true);

        // forcer la base de données
        if ($db!="0")
            $portal->setDatabaseByName($db);        
        
        $request = Request::createFromGlobals();
        if ($request->get('history')>0) {
            $history_max = $request->get('history');
        }
        $ordered = $request->get('chained');
        $stopped = $request->get('onlyWarning');

        $history = $this->container->get('arii_jid.history');

        $Jobs = $history->Jobs(0, $ordered, $stopped, false);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJIDBundle:RSS:default.rss.twig', array( 'Infos' => $Jobs), $response );
    }
    
}
