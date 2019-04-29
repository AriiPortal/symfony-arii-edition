<?php

namespace Arii\JIDBundle\Controller\DailyPlan;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class JobsController extends Controller
{
    protected $images;
    protected $ColorStatus = array (
            'EXECUTED' => '#ccebc5',
            'WAITING' => '#DDD',
            'DELAYED' => '#ffffcc',
            'BLOCKED' => '#fbb4ae',
            'UNKNOWN' => '#FF0000'
        );
    
    public function __construct( )
    {
          $request = Request::createFromGlobals();
          $this->images = $request->getUriForPath('/../bundles/ariicore/images/wa');          
    }

    public function indexAction()
    {
        $portal = $this->container->get('arii_core.portal');
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
        $year = substr($refDate, 0, 4);
        $month = substr($refDate, 5, 2);
        $day = substr($refDate, 8, 2);

        $start = substr($past,11,2);
        $Timeline['start'] = (60/$step)*$start;
        $Timeline['js_date'] = $year.','.($month - 1).','.$day;

        $Timeline['start'] = 0;
                
        // Liste des spoolers pour cette plage
        $Spoolers = $portal->getNodesBy('vendor','ojs');
        
        $Timeline['spoolers'] = $Spoolers;
       return $this->render('AriiDSBundle:Jobs:index.html.twig', array('refresh' => $refresh, 'Timeline' => $Timeline ) );
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiDSBundle:Jobs:toolbar.xml.twig',array(), $response );
    }

    public function form_toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiDSBundle:Jobs:form_toolbar.xml.twig',array(), $response );
    }

    public function formAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $this->render('AriiDSBundle:Jobs:form.json.twig',array(), $response );
    }

    public function gridAction($ordered = 0,$stopped=1) {

        $request = Request::createFromGlobals();
        $cyclic = $request->get('cyclic');
        $onlyWarning = $request->get('onlyWarning');

        $history = $this->container->get('arii_ds.dailyschedule');
        $Jobs = $history->DailySchedule( $cyclic, $onlyWarning, true);
        
        $date = $this->container->get('arii_core.date');
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        foreach ($Jobs as $k=>$job) {
            $status = $job['STATUS'];
            if (isset($this->ColorStatus[$status])) 
                $color = $this->ColorStatus[$status];
            else 
                $color = '#AAA';
            list($planned,$executed,$next,$duration) = $date->getLocaltimes( $job['SCHEDULE_PLANNED'],$job['SCHEDULE_EXECUTED'],'', $job['SCHEDULER_ID'], false  );   
            $list .='<row id="'.$job['ID'].'" style="background-color: '.$color.'">';
            $list .='<cell>'.$job['SCHEDULER_ID'].'</cell>';
            $list .='<cell>'.dirname($job['JOB']).'</cell>';
            $list .='<cell>'.basename($job['JOB']).'</cell>';
            $list .='<cell>'.$status.'</cell>';
            $list .='<cell>'.substr($planned,0,16).'</cell>';
            $list .='<cell>'.substr($executed,0,16).'</cell>';
            $list .='<userdata name="SCHEDULER_HISTORY_ID">'.$job['SCHEDULER_HISTORY_ID'].'</userdata>';
            $list .='</row>';
        }
        
        $list .= "</rows>\n";
        $response->setContent( $list );
        return $response;
    }
    
    public function pieAction($history_max=0,$ordered = 0,$onlyWarning=1) {        
        $request = Request::createFromGlobals();
        $cyclic = $request->get('cyclic');
        $onlyWarning = $request->get('onlyWarning');

        $ds = $this->container->get('arii_ds.dailyschedule');
        $Jobs = $ds->DailySchedule($cyclic, $onlyWarning, true);

        $States = array('WAITING','EXECUTED','DELAYED','BLOCKED');
        foreach ($States as $state) {
            $Status[$state]=0;
        }

        foreach ($Jobs as $k=>$job) {
            $status = $job['STATUS'];
            $Status[$status]++;
        }
        
        if ($onlyWarning) $Status['EXECUTED']=$Status['WAITING']=0; 
        $pie = '<data>';
        foreach ($Status as $status=>$nb) {
            $pie .= '<item id="'.$status.'"><STATUS>'.str_replace(' ','_',$status).'</STATUS><JOBS>'.$nb.'</JOBS><COLOR>'.$this->ColorStatus[$status].'</COLOR></item>';
        }
         $pie .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $pie );
        return $response;
    }

    public function timelineAction($ordered=false)
    {
        $request = Request::createFromGlobals();
        $cyclic = $request->get('cyclic');
        $onlyWarning = $request->get('onlyWarning');

        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');
        
        $ds = $this->container->get('arii_ds.dailyschedule');
        $Jobs = $ds->DailySchedule( $cyclic, $onlyWarning, true);
        
        $xml = '<data>';
        foreach ($Jobs as $id=>$line) {
            $xml .= '<event id="'.$line['ID'].'">';
            $xml .= '<start_date>'.$line['SCHEDULE_PLANNED'].'</start_date>';
            if ($line['SCHEDULE_EXECUTED']!='')
                $xml .= '<end_date>'.$line['SCHEDULE_EXECUTED'].'</end_date>';
            else 
                $xml .= '<end_date>'.$line['SCHEDULE_PLANNED'].'</end_date>';
            $status = $line['STATUS'];
            $xml .= '<text>'.$line['JOB'].' ('.$line['SCHEDULER_ID'].')</text>';
            $xml .= '<section_id>'.$line['SCHEDULER_ID'].'</section_id>';
            $xml .= '<textColor>black</textColor>';
            $xml .= '<color>'.$this->ColorStatus[$status].'</color>';
            $xml .= '</event>';
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;    
    }

}