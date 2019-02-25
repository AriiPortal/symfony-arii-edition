<?php
/*
 * Gere les parametres passés en session et en requête
 */
namespace Arii\CoreBundle\Service;
use Symfony\Component\HttpFoundation\Request;

class AriiFilter
{
    protected $portal;
    protected $start;
    
    public function __construct (\Arii\CoreBundle\Service\AriiPortal $portal) {
        $this->portal = $portal;
    }
    
    // fonction qui remplacera le getfilter
    public function getRequestFilter() {
        $date = new \DateTime();
        $Default = [
            'env' => 'P',
            'app' => '*',
            'day_past' => -30,
            'job_class' => '*',
            'spooler' => '*',
            'hour' => $date->format('h'),
            'day' => $date->format('d'),
            'month' => $date->format('m'),
            'year' => $date->format('Y'),
            'hour' => $date->format('H'),
            'minute' => $date->format('i'),
            'second' => $date->format('s'),
            'limit' => 10,
            'category' => '*',
            'monthday' => $date->format('m').$date->format('d'),  // code jour
            'max_result' => 1000,     // limitation 
            'only_warning' => 0,  // alertes
            'sort' => 'last',          // tri par défaut
            'retention' => 35          // tri par défaut
        ];
        
        $request = Request::createFromGlobals();
        $User = $this->portal->getUserInterface();    

        // le jour est la reference
        $User['day_past'] = $User['ref_past'];
        
        foreach (array_keys($Default) as $f) {
            // cas speciaux
            switch ($f) {
                case 'month':
                    if ($request->query->get($f)=='last') {
                        $date->modify("last month");
                        $User[$f] = $date->format('m');
                    }
                    break;
                default:
                    if ($request->query->get($f)!='') {
                        $User[$f] = $request->query->get($f);
                    }
                    elseif (!isset($User[$f]) or ($User[$f]=='')) {
                        $User[$f] = $Default[$f];
                    }
                    break;
            }
        }       
        
        // date par defaut
        // normalement c'est fait a l'initialisation du portail
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
        
        // Calcul fixe
        $end = new \DateTime($User['year'].'-'.$User['month'].'-'.$User['day'].' 23:59:59');
        $start = clone $end;
        $start->add(\DateInterval::createFromDateString(($User['day_past']*86400+1).' seconds'));
        
        $User['start'] = $start;
        $User['end'] = $end;
        
        // forcer les entiers
        $User['day'] = $User['day']*1;
        $User['month'] = $User['month']*1;
        // 
        // Conflit avec app.request de twig
        $User['ref_past'] = $User['day_past'];
        $Filters = $this->portal->setUserInterface($User);
        
        $Filters['appl']=$Filters['app'];
        unset($Filters['app']);
        
        // Contexte
        foreach ([ 'max_result', 'only_warning', 'sort'] as $f)  {
            if ($request->query->get($f)!='')
                $Filters[$f] = $request->query->get($f);
        }
        
        // Complement
        foreach ([ 'id', 'job_id', 'job_name'] as $f)  {
            $Filters[$f] = $request->query->get($f);
        }
        return $Filters;
    }
    
    // fonction obsolete
    public function getFilter($env='',$app='',$day_past='',$day='',$month='',$year='',$class='', $hour='', $spooler='' ) {       
        $User = $this->portal->getUserInterface();        

        // nouveauté
        if (!isset($User['class'])) $User['class']='*';
        
        if ($class=='') $class = $User['class'];
        if ($class=='') $class = '*';        
        $User['class'] = $class;
        
        if ($env=='') $env = $User['env'];
        if ($env=='') $env = 'P';        
        $User['env'] = $env;
        
        if ($app=='') $app = $User['app'];
        if ($app=='') $app = '*';
        $User['app'] = $app;

        if ($spooler=='') $spooler = $User['spooler'];
        if ($spooler=='') $spooler = 'arii';
        $User['spooler'] = $spooler;
        
        if ($day_past=='') $day_past = $User['day_past'];
        if ($day_past=='') $day_past = -90;
        $User['day_past'] = $day_past;
        
        $date = new \DateTime();
        // compatibilite temporaire
        if (!isset($User['day'])) $User['day']=$date->format('d');
        if ($day=='') $day = $User['day'];
        if ($day=='') $day = $date->format('d');
        $User['day'] = $day;
        
        if ($year=='') $year = $User['year'];
        if ($year=='') $year = $date->format('Y');
        $User['year'] = $year;

        if ($month=='') $month = $User['month'];
        if ($month=='last') {
            $date->modify("last month");
            $month = $date->format('m');
        }

        if ($hour=='') $hour = $User['hour'];
        if ($hour=='') $hour = $date->format('H');
        $User['hour'] = $hour;
        
/*        
        $end = new \DateTime("$year-$month-01 23:59:59");
        $end->modify('last day of this month');
        
        $month_add = round($day_past/30)+1; // on va du 1er au 31 de la fin
        $start = new \DateTime("$year-$month-01"); 
        $start->modify("$month_add months");
        $start->modify('first day of this month');
        // on se positionne au premier jour de mois precedent
        $this->start = date('Y-m-d',strtotime(substr($User['day_past'],0,8).'01'));
*/
        // Calcul fixe
        $end = new \DateTime("$year-$month-$day");
        $start = clone $end;
        $start->add(\DateInterval::createFromDateString($day_past.' day'));

        $this->portal->setUserInterface($User);
        return array($env,$app,$day_past,$day,$month,$year,$start,$end,$class,$hour,$spooler);
    }
    
    public function getEnv() {
        $User = $this->portal->getUserInterface();
    return $User['env'];  
    }

    public function getApp() {
        $User = $this->portal->getUserInterface();
    return $User['app'];  
    }

    public function getStart() {
    return $this->start;
    }
    
}
