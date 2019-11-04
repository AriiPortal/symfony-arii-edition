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
            'dayPast' => -30,
            'jobClass' => '*',
            'spooler' => '*',
            'hour' => $date->format('h'),
            'day' => $date->format('d'),
            'month' => $date->format('m'),
            'year' => $date->format('Y'),
            'hour' => $date->format('H'),
            'minute' => $date->format('i'),
            'second' => $date->format('s'),
            'category' => '*',
            'monthday' => $date->format('m').$date->format('d'),  // code jour
            'maxResults' => 1000,     // limitation 
            'onlyWarning' => 0,  // alertes
            'sort' => 'last',          // tri par défaut
            'retention' => 35,          // tri par défaut
            'outputFormat' => 'dhtmlx',
            'bundle' => '',   // Bundle en cours
            'repoId' => 'local', // repo en cours
            'machine' => null, // machine en cours
            'orderId' => null, // Ordre
            'jobChain' => null, // Chain
            'accept' => 'application/json'
        ];
        
        $request = Request::createFromGlobals();
        
        $User = $this->portal->getUserInterface();    
        // le jour est la reference
        $User['dayPast'] = $User['refPast'];
        
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
        $start->add(\DateInterval::createFromDateString(($User['dayPast']*86400+1).' seconds'));
        
        $User['start'] = $start;
        $User['end'] = $end;
        
        // forcer les entiers
        $User['day'] = $User['day']*1;
        $User['month'] = $User['month']*1;
        // 
        // Conflit avec app.request de twig
        $User['refPast'] = $User['dayPast'];
        $Filters = $this->portal->setUserInterface($User);
        
        $Filters['appl']=$Filters['app'];
        unset($Filters['app']);
        
        // Contexte
        foreach ([ 'maxResults', 'onlyWarning', 'sort', 'outputFormat', 'bundle', 'repoId', 'machine'] as $f)  {
            if ($request->query->get($f)!='')
                $Filters[$f] = $request->query->get($f);
        }
        
        // Complement
        foreach ([ 'id', 'job_id', 'job_name'] as $f)  {
            $Filters[$f] = $request->query->get($f);
        }

        // passage des infos par header
        $Header = $request->headers->all();
        if (isset($Header['accept'])) {
            // le premier
            list($Filters['accept']) = $Header['accept'];
            // Plus simple
            $Filters['outputFormat'] = substr($Filters['accept'],strpos($Filters['accept'],'/')+1);
        }
                
        return $Filters;
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
