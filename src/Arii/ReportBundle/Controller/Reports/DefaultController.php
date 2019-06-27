<?php

namespace Arii\ReportBundle\Controller\Reports;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        
        return $this->render('AriiReportBundle:Reports:index.html.twig', $Filters );
    }

    // Prepare un graphique
    // Ajout du filtre et du format
    private function DrawBar($Data,$Filter=[],$format='xml') { 
        // liste triee
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        
        if (!empty($Filter)) {
            $xml .= '<filter>';        
            foreach ($Filter as $k=>$data) {
                $xml .= '<'.$k.'>'.$data.'</'.$k.'>';
            }
            $xml .= '</filter>';
        }
        foreach ($Data as $k=>$data) {
            $xml .= '<item id="'.$k.'">';
            foreach ($data as $k=>$v) {
                $xml .= '<'.$k.'>'.$v.'</'.$k.'>';
            }
            $xml .= '</item>';
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
    public function envsAction()
    {        
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        // tous les jobs non supprimés dans la période
        $em = $this->getDoctrine()->getManager(); 
        $Envs = $em->getRepository("AriiReportBundle:JOB")->findEnv($Filters['start'],$Filters['end'],$Filters['appl'],$Filters['jobClass']);
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $nb=0;
        foreach ($Envs as $env) {
            $xml .= '<item id="'.$env['env'].'">';
            $xml .= '<env>'.$env['env'].'</env>';
            $xml .= '<environment>'.$this->get('translator')->trans('env.'.$env['env']).'</environment>';
            $xml .= '<count>'.$env['envs'].'</count>';
            $xml .= '</item>';
        }
        $xml .= '</data>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;           
    }

    public function domsAction()
    {        
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        // tous les jobs non supprimés dans la période
        $em = $this->getDoctrine()->getManager(); 
        $Doms = $em->getRepository("AriiReportBundle:JOB")->findDom($Filters['start'],$Filters['end'],$Filters['env'],$Filters['jobClass']);
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $nb=0;
        foreach ($Doms as $dom) {
            $xml .= '<item id="'.$dom['domain'].'">';
            $xml .= '<dom>'.$dom['dom'].'</dom>';
            $xml .= '<domain>'.$dom['domain'].'</domain>';
            $xml .= '<count>'.$dom['doms'].'</count>';
            $xml .= '</item>';
        }
        $xml .= '</data>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;           
    }

    public function appsAction()
    {        
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        // tous les jobs non supprimés dans la période
        $em = $this->getDoctrine()->getManager(); 
        $Apps = $em->getRepository("AriiReportBundle:JOB")->findApp($Filters['start'],$Filters['end'],$Filters['env'],$Filters['jobClass']);
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $nb=0;
        foreach ($Apps as $app) {
            $xml .= '<item id="'.$app['app'].'">';
            $xml .= '<app>'.$app['app'].'</app>';
            $xml .= '<application>'.$app['app'].'</application>';
            $xml .= '<count>'.$app['apps'].'</count>';
            $xml .= '</item>';
        }
        $xml .= '</data>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;           
    }

    public function jclsAction()
    {        
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        // tous les jobs non supprimés dans la période
        $em = $this->getDoctrine()->getManager(); 
        $Jcls = $em->getRepository("AriiReportBundle:JOB")->findJcl($Filters['start'],$Filters['end'],$Filters['appl']);
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $nb=0;
        foreach ($Jcls as $jcl) {
            $xml .= '<item id="'.$jcl['jcl'].'">';
            $xml .= '<jcl>'.$jcl['jcl'].'</jcl>';
            $xml .= '<jobClass>'.$this->get('translator')->trans('class.'.$jcl['jcl']).'</jobClass>';
            $xml .= '<count>'.$jcl['jcls'].'</count>';
            $xml .= '</item>';
        }
        $xml .= '</data>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;           
    }

    public function spoolersAction()
    {        
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        // tous les jobs non supprimés dans la période
        $em = $this->getDoctrine()->getManager(); 
        $Spools = $em->getRepository("AriiReportBundle:JOB")->findSpool($Filters['start'],$Filters['end'],$Filters['env'],$Filters['appl']);
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $nb=0;
        foreach ($Spools as $spool) {
            $xml .= '<item id="'.$spool['spooler'].'">';
            $xml .= '<spooler>'.$spool['spooler'].'</spooler>';
            $xml .= '<count>'.$spool['spoolers'].'</count>';
            $xml .= '</item>';
        }
        $xml .= '</data>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;           
    }

    // Nombre de jobs par applications
    public function jobsAction() 
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $end = clone $Filters['start'];
        $end = $end->add(\DateInterval::createFromDateString('1 day'));
        $DBJobs = $em->getRepository("AriiReportBundle:JOBDay")->findApps($Filters['start'],$end,$Filters['env'],$Filters['jobClass'],true,'jobs','DESC');
        
        $Parameters = [
            'repository' => "AriiReportBundle:JOBDay",
            'start'      => $Filters['start']->format('Y-m-d H:i:s'),
            'end'        => $Filters['end']->format('Y-m-d H:i:s'),
            'env'        => $Filters['env'],
            'app'        => $Filters['appl'],
            'class'      => $Filters['jobClass']
        ];

        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();

        $nb=0;
        
        $Jobs =[];
        foreach ($DBJobs as $job) {
            $a = $job['app'];
            // Filtre par categorie
            if (!isset($App[$a])) continue;

            $title=$App[$a]['title'];
            if ($App[$a]['active']==0) continue;            
            if ($job['jobs']==0) continue;

            // filtre des categories
            if (($Filters['category']!='*') and ($App[$a]['category']!=$Filters['category'])) continue;
            
            if ($nb++ >= $Filters['limit']) break;
            
            $Jobs[$a] = [
                'application' => $title,
                'jobs' =>    $job['jobs'],
                'created' => $job['created'],
                'deleted' => $job['deleted']
            ];
        }
        return $this->DrawBar($Jobs, $Parameters);
    }

    // Nombre d'exécution par applications
    public function runsAction ()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $DBRuns = $em->getRepository("AriiReportBundle:RUNDay")->findApps($Filters['start'],$Filters['end'],$Filters['env'],$Filters['jobClass'],'runs','DESC');

        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();

        $Runs = [];
        $nb=0;
        foreach ($DBRuns as $run) {
            $a = $run['app'];
         
            if (!isset($App[$a])) continue;
            $title=$App[$a]['title'];
            if ($App[$a]['active']==0) continue;
            if ($run['runs']==0) continue;
            
            // filtre des categories
            if (($Filters['category']!='*') and ($App[$a]['category']!=$Filters['category'])) continue;
            
            if ($nb++ >= $Filters['limit']) break;
            
            $Runs[$a] = [
                'application' => $title,
                'runs' =>    $run['runs'],
            ];
        }
        return $this->DrawBar($Runs);
    }

    // Nombre d'exécution par applications
    // Pour radar
    public function alarmsAction ($radar=false)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $DBRuns = $em->getRepository("AriiReportBundle:RUNHour")->findRuns($Filters['start'],$Filters['end'],$Filters['env'],$Filters['appl'],$Filters['jobClass'],'alarms','DESC');

        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();

        $Runs = $Radar = [];
        $nb=0;
        $Alias=[];
        $na=0;
        foreach ($DBRuns as $run) {
            print_r($run);
            $a = $run['app'];

            if (!isset($App[$a])) continue;
            $title=$App[$a]['title'];
            if ($App[$a]['active']==0) continue;
            if ($run['alarms']==0) continue;

            // filtre des categories
            if (($Filters['category']!='*') and ($App[$a]['category']!=$Filters['category'])) continue;
            
            // Attention ce sont les applis qu'il faut compter
            if ($nb++ >= $Filters['limit']) break;

            $t = $run['date']->format('w');
            $Runs[$a] = [
                'application' => $title,
                'date' =>   $run['date']->format('Y-m-d'), // frequence
                'period' => 'week',
                'time' =>   $t,
                'hour' =>   $run['hour']*1,
                'alarms' => $run['alarms'],
            ];

            if (!isset($Alias[$title])) {
                $Alias[$title] = $na++;
            }
            
            // Pour le radar
            // radar /semaine ou /mois
            if ($radar) {
                $Radar[$t]['period'] = 'week';
                $Radar[$t]['freq'] = $t;
                $a = "app".$Alias[$title];
                $Radar[$t][$a] = $run['hour']*1;
                $Radar[$t][$a.'_text'] = $title;
            }
        }

        if (!$radar)
            return $this->DrawBar($Runs);
        ksort($Radar);
        return $this->DrawBar($Radar);
    }

    // Taux d'erreurs
    public function errorsAction ()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager();
        $DBRuns = $em->getRepository("AriiReportBundle:RUNDay")->findApps($Filters['start'],$Filters['end'],$Filters['env'],$Filters['jobClass'],'rate','DESC');
        
        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();

        $Runs = [];
        $nb=0;
        foreach ($DBRuns as $run) {
            $a = $run['app'];

            if (!isset($App[$a])) continue;
            $title=$App[$a]['title'];
            if ($App[$a]['active']==0) continue;
            if ($run['rate']==0) continue;

            // filtre des categories
            if (($Filters['category']!='*') and ($App[$a]['category']!=$Filters['category'])) continue;

            if ($nb++ >= $Filters['limit']) break;
            
            $rate = round($run['rate']*100);
            if ($rate>3) 
                $image = '4.png';
            elseif ($rate>2) 
                $image = '3.png';
            elseif ($rate>1) 
                $image = '2.png';
            elseif ($rate>0) 
                $image = '1.png';
            else
                $image = '0.png';
            
            $Runs[$a] = [
                'application' => $title,
                'rate' =>    $rate,
                'runs'  => $run['runs'],
                'alarms'  => $run['alarms'],
                'img' => $image
            ];
        }
        return $this->DrawBar($Runs);
    }
    
    // Changements
    public function changesAction() 
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $DBJobs = $em->getRepository("AriiReportBundle:JOBDay")->findApps($Filters['start'],$Filters['end'],$Filters['env'],$Filters['jobClass'],false,'created','DESC');

        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();

        $nb=0;
        
        $Jobs =[];
        foreach ($DBJobs as $job) {
            $a = $job['app'];
            
            // Filtre par categorie
            if (!isset($App[$a])) continue;

            $title=$App[$a]['title'];
            if ($App[$a]['active']==0) continue;            
            if ($job['jobs']==0) continue;
            
            // filtre des categories
            if (($Filters['category']!='*') and ($App[$a]['category']!=$Filters['category'])) continue;
            
            if ($nb++ >= $Filters['limit']) break;
            
            $Jobs[$a] = [
                'application' => $title,
                'jobs' =>    $job['jobs'],
                'created' => $job['created'],
                'deleted' => $job['deleted']
            ];
        }
        return $this->DrawBar($Jobs);
    }

    // Nombre d'exécution par jours
    public function evolutionAction ()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager();
        $DBRuns = $em->getRepository("AriiReportBundle:RUNDay")->findByDay($Filters['start'],$Filters['end'],$Filters['env'],$Filters['appl'],$Filters['jobClass'],false);
        
        $Parameters = [
            'repository' => "AriiReportBundle:RUNDay",
            'start'      => $Filters['start']->format('Y-m-d H:i:s'),
            'end'        => $Filters['end']->format('Y-m-d H:i:s'),
            'env'        => $Filters['env'],
            'app'        => $Filters['appl'],
            'class'      => $Filters['jobClass']
        ];
        
        $nb=0;
        $Runs = [];
        foreach ($DBRuns as $run) {
            $a = $run['date']->format('Ymd');
            $Runs[$a] = [
                'date'  =>    $run['date']->format('Ymd'),
                'day' =>    $run['date']->format('d')*1,
                'runs' =>    $run['runs'],
                'alarms' =>    $run['alarms']
            ];
        }
        return $this->DrawBar($Runs,$Parameters);
    }
    
}

