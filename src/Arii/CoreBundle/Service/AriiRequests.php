<?php
namespace Arii\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Parser;
use Doctrine\ORM\Query\ResultSetMapping;
use \Dompdf\Dompdf;
use \Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;

# Gestion des requêtes
class AriiRequests
{
    protected $portal;
    protected $date;
    protected $doctrine;
    private $templating;

    # Contexte du portail
    public function __construct(AriiPortal $portal, $doctrine, AriiDate $date, $templating )
    {
        $this->portal = $portal;
        $this->date = $date;
        $this->doctrine = $doctrine;
        $this->templating = $templating;
    }

    public function readStatus($bundle='Core') {

        // On récupère les statuts en cours
        $em = $this->doctrine->getManager();        
        $Checks = $em->getRepository("AriiCoreBundle:Request")->findBy( [ 'bundle' => $bundle ] );
        $State = [];
        foreach ($Checks as $Check) {
            $request = $Check->getBundle().':'.$Check->getName();
            $State[$request] = [
                'status'  => $Check->getStatus(),
                'executed'  => $Check->getExecuted()->format('U'),
                'results'  => $Check->getResults()
            ];            
        }
        return $State;        
    }

    public function writeStatus($Infos,$bundle='Core') {
        // on peut mettre a jour la table des sondes
        $em = $this->doctrine->getManager();
        $Check = $em->getRepository("AriiCoreBundle:Request")->findOneBy( 
            [   'bundle' => $bundle,
                'name' => $Infos['name'] 
            ] 
        );

        if (!$Check)
            $Check = new \Arii\CoreBundle\Entity\Request();  

        $Check->setBundle($bundle);
        $Check->setName($Infos['name']);
        $Check->setTitle($Infos['title']);
        $Check->setResults($Infos['results']);
        if (isset($Infos['runtime']))
            $Check->setRunTime($Infos['runtime']);
        if (isset($Infos['min_success']))
            $Check->setMinSuccess($Infos['min_success']);
        if (isset($Infos['max_success']))
            $Check->setMaxSuccess($Infos['max_success']);
        if (isset($Infos['status']))
            $Check->setStatus($Infos['status']);
        $Check->setExecuted(new \Datetime());

        $em->persist($Check);
        $em->flush();

    }

    public function Summary($dir,$bundle='Core',$output='html') {

        $yaml = new Parser();
        $value['title'] = 'Summary';
        $value['description'] ='List of requests';
        $value['columns'] = array(
            'Title',
            'Description' 
        );
        
        $Status = $this->readStatus($bundle);
        $Paths = $this->portal->getWorkPaths($dir,$bundle);
        $layer=0;
        $Docs = [];
        foreach ($Paths as $basedir) {
            if ($dh = opendir($basedir)) {
                while (($file = readdir($dh)) !== false) {
                    if (substr($file,-4)=='.yml') {
                        $content = file_get_contents("$basedir/$file");
                        $v = $yaml->parse($content);
                        $title = $v['title'];
                        if (!isset($Docs[$title])) {
                            $url = $file;
                            $name = $bundle.':'.$dir.'/'.str_replace('.yml','',$file);
                            // On complète avec la base de données
                            $nb = $delay = $status = '';
                            if (isset($Status[$name])) {
                                $nb     = $Status[$name]['results'];
                                $status = $Status[$name]['status'];
                                // $delay = round((time() - $State[$name]['executed'])/3600);
                                
                            }
                            // Toutes les infos
                            $Docs[$title] = [ 
                                'name'       => $name,
                                'title'       => $title,
                                'description' => $v['description'],
                                'layer' => $layer,
                                'request' => $name,
                                'count' => $nb,
                                'status' => $status
                            ];                            
                        }
                    }                
                }
                
            }
            $layer++;
        }
        ksort($Docs);
        $nb=0;
        foreach ($Docs as $k=>$v) {
            $nb++;
            $value['lines'][$nb]['cells'] = $v;
        }
        $value['count'] = $nb;   
        return $value;
    }
    
    public function Grid($dir,$bundle='Core') {
        
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<rows>';
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        $ColorStatus = $this->portal->getColors();        
        $Data = $this->Summary($dir,$bundle);
        if (isset($Data['lines'])) {
            foreach ($Data['lines'] as $title=>$Infos) {
                switch ($Infos['cells']['status']) {
                     case 1:
                         $bg = ' style="background-color: '.$ColorStatus['INACTIVE']['bgcolor'].';"';
                         break;
                     case 2:
                         $bg = ' style="background-color: '.$ColorStatus['SUCCESS']['bgcolor'].';"';
                         break;
                     case 4:
                         $bg = ' style="background-color: '.$ColorStatus['FAILURE']['bgcolor'].';"';
                         break;
                     default:
                         $bg = '';
                }
                if ($Infos['cells']['layer']==1)
                    $title = $Infos['cells']['title'];
                else
                    $title = '<b>'.$Infos['cells']['title'].'</b>';
                $list .= '<row id="'.$Infos['cells']['name'].'"'.$bg.'><cell><![CDATA['.$title.']]></cell><cell>'.$Infos['cells']['count'].'</cell></row>';
            }
        }
        $list .= '</rows>';
        return $list;
    }
    
    public function Display($req,$output,$Args,$bundle='Core',$header=true,$footer=true) {
        if (($p=strpos($req,':'))>0) {
            $bundle = substr($req,0,$p);
            $req = substr($req,$p+1);
        }
        
        $file = $this->portal->getWorkFile($req,$bundle);
        if (!$file)
            throw new \Exception("$req not found!");
        else 
            $content = file_get_contents($file);
        
        $yaml = new Parser();
        try {
            $value = $yaml->parse($content);
            
        } catch (ParseException $e) {
            $error = array( 'text' =>  "Unable to parse the YAML string: %s<br/>".$e->getMessage() );
            return $this->templating->render('AriiReportBundle:Requests:ERROR.html.twig', array('error' => $error));
        }

        $runtime = microtime();

        $value['request'] = str_replace('.yml','',$req);
        $value['output'] = $output;
        $value['bundle'] = $bundle;
        
        // Test de json
        if (isset( $value['curl'])) {
            $url = $value['curl']['url'];
            $ch = curl_init();
            
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            //execute post
            $content = curl_exec($ch);
            if ($content === FALSE) {
                throw new \Exception( sprintf("cUrl error (#%d): %s", curl_errno($ch),htmlspecialchars(curl_error($ch))));
            }
            curl_close($ch);
            
            $Content = json_decode($content, true);
            
            $root = $value['curl']['root'];
            if (isset($Content[$root])) {
                $Content = $Content[$root];                
            }
            
            // On cree le tableau des consoles et des formats
            $Values = $Format = array();
            foreach(explode(',',$value['curl']['fields']) as $col) {
                list($f,$v) = explode('=',$col);
                $Values[$f] = $v;
            }
            
            // On recupere les valeurs
            $Data = [];
            if ($Content) {
            // print_r($Content);
            foreach ($Content as $content) {
                $line = [];
                foreach ($Values as $f=>$v ) {
                    if (isset($content[$v])) 
                        $line[$f] = $content[$v];
                    else 
                        $line[$f] = '';
                }
                array_push($Data,$line);
            }
            print_r($Data);
            }
            exit();
        }
        else {
            if (isset($value['connection']))
                $em = $this->doctrine->getManager($value['connection']);
            else
                $em = $this->doctrine->getManager();
        
           // Connexion additionelle ?
            $driver = $em->getConnection()->getDriver()->getName();
                        
            // simplification
            // ?! devrait être le protocole
            switch($driver) {
                case 'mysqli':
                    $driver='mysql';
                    break;
                case 'oci8':
                    $driver='oracle';
                    break;
            }

            // On regarde si le rapport peut être argumenté
            if (isset($value['args'])) {
                foreach (explode('&',$value['args']) as $arg) {
                    // valeur par défaut ?
                    $def = '';
                    if (($p=strpos($arg,'('))>0) {
                       $a = substr($arg,0,$p);
                       if (($e=strpos($arg,')',$p))>0)
                           $def = substr($arg,$p+1,$e-$p-1);
                    }
                    else $a = $arg;
                    
                    // Passé en url ?
                    if (!isset($Args[$a]))
                        $Args[$a] = $def;
                }
                $value['args'] = $Args;
            }

            if (!isset($value['sql'][$driver]))
                return array('error' => array( 'text' => "Section '$driver' manquante dans '$req' !"));

            $sql = $this->Replace($value['sql'][$driver],$Args);

            // On cree le tableau des consoles et des formats
            $value['columns'] = $Format = array();
            $rsm = new ResultSetMapping();

            // Entete 
            $head = str_replace('/',',',$value['header']);
            // Si on a / comme séparateur, c'est un pivot
            if (strpos($value['header'],'/')>0) {
                $format = 'pivot';
            }
            else {
                $format = 'list';
            }

            foreach (explode(',',$head) as $c) {
                if (($p = strpos($c,'('))>0) {
                    $h = substr($c,0,$p);
                    $Format[$h] = substr($c,$p+1,strpos($c,')',$p)-$p-1);
                    $c = $h;
                }
                $rsm->addScalarResult( strtoupper($c), $c);
                array_push($value['columns'],$c);
            }

            // bibliothèques
            $date = $this->date;
            $query = $em->createNativeQuery($sql, $rsm);
            $Data = $query->getResult();
        }
        
        // Mise en tableau
        $nb=0;
        foreach ($Data as $line) 
        {
            $r = array();
            $status = 'unknown';
            foreach ($value['columns'] as $h) {
                if (isset($line[$h])) {
                    // format special
                    $value['status'] = '';
                    if (isset($Format[$h])) {
                        switch ($Format[$h]) {
                            case 'localtime':
                                $dt = new \DateTime($line[$h], new \DateTimeZone('UTC'));
                                $dt->setTimezone(new \DateTimeZone('Europe/Paris')); // a integrer au portail
                                $val = $dt->format('Y-m-d H:i:s');
                                break;
                            case 'timestamp':
                                $val = $date->Time2Local($line[$h]);
                                break;
                            case 'duration':
                                $val = $date->FormatTime($line[$h]);
                                break;
                            case 'event':
                                $val = $ats->Event($line[$h]);
                                break;
                            case 'alarm':
                                $val = $ats->Alarm($line[$h]);
                                break;
                            case 'br':
                                $val = str_replace(array("\t","\n"),array("     ","<br/>"),$line[$h]);
                                break;
                            case 'ping':
                                $val = $this->ping($h);
                                break;
                            case 'host':
                                $val = gethostbyname($h);
                                break;
                            case 'log':
                                switch ($driver) {
                                    case 'oracle':
                                        $Res = gzinflate ( substr( $line[$h], 10, -8) );
                                        break;
                                }
                                // on ne prend que le batch
                                $New = [];
                                foreach (explode("\n",$Res) as $line) {
                                    if (!preg_match('/\d\d\d\d-.*?\[(.*?)\]   \(.*?\) /',$line,$Matches)) continue;
                                    if ($Matches[1]=='ERROR') {
                                        $before = '<font color="red">';
                                        $after = '</font>';
                                    }
                                    elseif ($Matches[1]=='WARN') {
                                        $before = '<font color="orange">';
                                        $after = '</font>';
                                    }
                                    else {
                                        $before = $after = '';
                                    }
                                    array_push($New,$before.preg_replace('/\d\d\d\d-.*?\[(.*?)\]   \(.*?\) /','',$line).$after);                                    
                                }
                                $val = '<pre>'.implode("<br/>",$New).'</pre>';
                            break;
                            case 'stdout':
                                switch ($driver) {
                                    case 'oracle':
                                        $Res = gzinflate ( substr( $line[$h], 10, -8) );
                                        break;
                                }
                                // on ne prend que le batch
                                $New = [];
                                foreach (explode("\n",$Res) as $line) {
                                    // print "($line)\n";
                                    if (preg_match('/SCHEDULER-\d\d\d/',$line)) continue;
                                    if (!preg_match('/\d\d\d\d-.*?\[(.*?)\]   (.*)/',$line,$Matches)) continue;
                                    // print_r($Matches);
                                    if ($Matches[1]=='ERROR') {
                                        $before = '<font color="red">';
                                        $after = '</font>';
                                    }
                                    elseif ($Matches[1]=='WARN') {
                                        $before = '<font color="orange">';
                                        $after = '</font>';
                                    }
                                    else {
                                        $before = $after = '';
                                    }
                                    array_push($New,$before.preg_replace('/\d\d\d\d-.*?\[(.*?)\]   /','',$line).$after);                                    
                                }
                                $val = '<pre>'.implode("<br/>",$New).'</pre>';
                            break;
                            default:
                                if (strpos($Format[$h],':')>0) {                                    
                                    list($class,$function) = explode(':',$Format[$h]);
                                    $my = $this->container->get($class);
                                    $val = $my->$function($line[$h]);
                                }
                                else {
                                    $val = $line[$h].'('.$Format[$h].')';
                                }
                                break;
                        }
                    }
                    else {
                        $val = $line[$h];
                    }
                }
                else  $val = '';
                array_push($r,$val);
            }
            $nb++;
            $value['lines'][$nb]['cells'] = $r;
            $value['lines'][$nb]['status'] = $status;
         }
        $value['count'] = $nb;
        
        // Statut 
        $status = 0; // inconnu
        $min=0;
        $max=999999999;
        if (!isset($value['expected'])) {
            $status=1; // info
        }
        else {
            $expected = $value['expected'];
            $Limits = explode("-",$expected);
            if (isset($Limits[0]) and (trim($Limits[0])!=''))
                $min=$Limits[0];
            if (isset($Limits[1]) and (trim($Limits[1])!=''))
                $max=$Limits[1];
            if (($nb>=$min) and ($nb<=$max))
                $status = 2; // success
            else 
                $status = 4; // error
        }
        
        $runtime = microtime() - $runtime;
        $this->writeStatus([
            'name'          => str_replace('.yml','',$req),
            'title'         => $value['title'],
            'runtime'       => $runtime,
            'results'       => $nb,
            'min_success'   => $min,
            'max_success'   => $max,
            'status'        => $status
        ], $bundle );
        
        // Pour l'instant on considère que 
        // la dernière colonne est la valeur
        // l'avant-dernière est l'entête
        if (($format=='pivot') and (isset($value['lines'])))
            list($value['columns'],$value['lines']) = $this->Pivot($value['columns'],$value['lines']);
            
        // Remplacement ?
        if (isset($value['title']))
            $value['title'] = $this->Replace($value['title'],$Args);
        if (isset($value['description']))
            $value['description'] = $this->Replace($value['description'],$Args);

//            if (isset($Data['error']))
//                return $this->render('AriiCoreBundle:Requests:ERROR.html.twig', $Data);
        
        switch ($output) {
                case 'html':
                // Valeurs par defaut en html
                if (!isset($Args['header'])) $Args['header']=true;
                if (!isset($Args['footer'])) $Args['footer']=true;
                $Data = [
                    'result' => $value, 
                    'query' => $Args,
                    'infos' => [ 
                        'results' => $nb,
                        'min_success' => $min,
                        'max_success' => $max,
                        'min_warning' => $min,
                        'max_warning' => $max,
                        'status' => $status,
                        'runtime' => $runtime,
                        'request' => $req
                    ]
                ];
                return new Response( $this->templating->render('AriiCoreBundle:Requests:report.html.twig', $Data )) ;        
                break;
            case 'rss':
                $Data = [
                    'result' => $value, 
                    'infos' => [ 
                        'results' => $nb,
                        'min_success' => $min,
                        'max_success' => $max,
                        'min_warning' => $min,
                        'max_warning' => $max,
                        'status' => $status,
                        'runtime' => $runtime,
                        'request' => $req
                    ]
                ];
                print_r($Data);
                return new Response( $this->templating->render('AriiCoreBundle:RSS:default.rss.twig', $Data ) );
                break;
            case 'check':
                $sep = ";";
                $response = new Response();
                $response->headers->set('Content-type', 'text/plain');
                $csv = implode($sep,$value['columns'])."\n";
                if (isset($value['lines'])) {
                    foreach ($value['lines'] as $k=>$l) {
                        $csv .= implode($sep,$l['cells'])."\n";
                    }
                }
                print "(($min <= $nb <= $max) => $status)";
                if ($status==4)
                    $response->setStatusCode( '417' );
                $response->setContent( utf8_decode( $csv ) );

                return $response;                      
                break;
            case 'csv':
                $response = new Response();
                if ($nb>0) {
                    $sep = ";";
                    $response->headers->set('Content-type', 'text/plain');
                    $response->headers->set("Content-disposition", "attachment; filename=$req.csv"); 
                    $csv = implode($sep,$value['columns'])."\n";
                    if (isset($value['lines'])) {
                        foreach ($value['lines'] as $k=>$l) {
                            $csv .= implode($sep,$l['cells'])."\n";
                        }
                    }
                    $response->setContent( utf8_decode( $csv ) );
                }
                else {
                    $response->setStatusCode( '204' );
                }
                return $response;                   
                break;
            case 'excel':
            case 'xls':
                $response = new Response();
                if ($nb>0) {
                    $sep = "\t";
                    $response->headers->set('Content-type', 'application/vnd.ms-excel; charset=utf-8');
                    $response->headers->set("Content-disposition", "attachment; filename=$req.xls"); 

                    $csv = implode($sep,$value['columns'])."\n";
                    if (isset($value['lines'])) {
                        foreach ($value['lines'] as $k=>$l) {
                            $csv .= implode($sep,$l['cells'])."\n";
                        }
                    }
                    $response->setContent( utf8_decode( $csv ) );
                }
                else {
                    $response->setStatusCode( '204' );
                }
                return $response;                      
                break;
            case 'pdf':
                $twig = file_get_contents('../src/Arii/CoreBundle/Resources/views/Requests/dompdf.pdf.twig');
                $content = $this->get('arii_ats.twig_string')->render( $twig, array('result' => $value ) );      
                require_once('../vendor/dompdf/autoload.inc.php');
               
                set_time_limit(300);
                header('Content-Type: application/pdf');
                $dompdf = new DOMPDF();
                $dompdf->load_html($content);
                $dompdf->set_option('isHtml5ParserEnabled', true);
                $dompdf->set_paper('a4', 'landscape');
                $dompdf->render();
                $dompdf->stream("$req.pdf");
                exit();
                break;
            case 'md': //markdown                
                $response = new Response();
                if ($nb>0) {
                    $sep = " | ";
                    $response->headers->set('Content-type', 'text/plain');
                    $csv = "| ".implode($sep,$value['columns'])." |\n";
                    $csv .= preg_replace('/\w/','-',$csv);
                    if (isset($value['lines'])) {
                        foreach ($value['lines'] as $k=>$l) {
                            $csv .= "| ".implode($sep,$l['cells'])." |\n";
                        }
                    }
                    $response->setContent( utf8_decode( $csv ) );
                }
                else {
                    $response->setStatusCode( '204' );
                }
                return $response;                   
                break;
            case 'jira': //syntaxe particulière
                $response = new Response();
                if ($nb>0) {
                    $sep = "|";
                    $response->headers->set('Content-type', 'text/plain');
                    $csv = "||".implode('||',$value['columns'])."||\n";
                    if (isset($value['lines'])) {
                        foreach ($value['lines'] as $k=>$l) {
                            $csv .= "|".implode($sep,$l['cells'])."|\n";
                        }
                    }
                    $response->setContent( utf8_decode( $csv ) );
                }
                else {
                    $response->setStatusCode( '204' );
                }
                return $response;                   
                break;
            default:
                return $this->templating->render('AriiReportBundle:Requests:default.rss.twig', array('result' => $value ));
        }
    }

    private function pivot($Columns,$Lines) {
        $c = count($Columns);
        // Premier passage pour connaitre la nouvelle entete et les valeurs croisees
        $Items = [];
        foreach ($Lines as $Line) {
            // Attention si le nombre de colonne est variable !
            $V = $Line['cells'];
            $value = array_pop($V);
            $head  = array_pop($V);
            $fixe = implode('/',$V);
            $Items[$fixe][$head] = $value;
            if (isset($H[$head]))
                $H[$head]++;
            else
                $H[$head]=1;
        }
        $Head = array_keys($H);
        sort($Head);

        // On recree un nouveau tableau
        $New = [];
        $n=0;        
        foreach ($Items as $k=>$v) {
            $Line['cells'] = explode('/',$k);
            // et on complète
            foreach ($Head as $h) {
                if (isset($Items[$k][$h]))
                    array_push($Line['cells'],$Items[$k][$h]);
                else
                    array_push($Line['cells'],'');
            }
            $n++;
            $New[$n] = $Line;
        }
        
        // On recrée l'entête
        array_pop($Columns);
        array_pop($Columns);
        return array(array_merge($Columns,$Head),$New);
    }

    private function Replace($str,$Args) {
        foreach ($Args as $k=>$v) {
            $str = str_replace('{'.$k.'}',$v,$str);
        }
        return $str;
    }
    
    private function ping ($host) {
        $log = `ping -a $host -n 1`;
        return $log;
    }
    
}
