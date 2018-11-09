<?php
namespace Arii\ACKBundle\Controller\Import;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JiraController extends Controller
{

    public function IssuesAction() {
        // On traite le log
        if (isset($_FILES['xml']['tmp_name']))
            $log = file_get_contents($_FILES['xml']['tmp_name']);
        else 
            $log = file_get_contents('../workspace/ACK/Input/Jira/bloquants.xml');
        
        return $this->XML2DB($log,'ISSUE');
    }

    public function ChangesAction() {
        // On traite le log
        if (isset($_FILES['xml']['tmp_name']))
            $log = file_get_contents($_FILES['xml']['tmp_name']);
        else 
            $log = file_get_contents('../workspace/ACK/Input/Jira/changes.xml');
        
        return $this->XML2DB($log,'CHANGE');
    }
    
    public function XML2DB($log,$event_type)
    {  
        set_time_limit(300);
        $time = time();
        
        $tools = $this->container->get('arii_core.tools');
        # Tester que c'est bien un XML
        $array = $tools->xml2array( $log );
        
        if (!isset($array['rss']['channel']['item'])) 
            return new Response(sprintf( "# %d\n%d s\n%s\n",0,(time()-$time),"EMPTY!"));
            
        $Infos = $array['rss']['channel']['item'];
        
        $em = $this->getDoctrine()->getManager();
        $n = 0;        
        foreach ($Infos as $Issue) {
            
            $name= $Issue['key'];
            
            $Event = $em->getRepository("AriiACKBundle:Event")->findOneBy([ 'name' => $name ]);            
            if (!$Event) {
                $Event = new \Arii\ACKBundle\Entity\Event();
                $Event->setState($this->MyState($Issue['status']));
            }
            
            $Event->setName($name);
            $Event->setTitle($Issue['summary']);
            if (is_array($Issue['description']))
                $description = join("\n",$Issue['description']);
            else 
                $description = $Issue['description'];
            $Event->setEvent($description); 
            
            // Est ce que le change est vraiement utile ?
            $Event->setStartTime(new \DateTime($Issue['created']));
            $Event->setEndTime(new \DateTime($Issue['updated']));
            
            // Custom fields
            if (isset($Issue['customfields']['customfield'])) {
                foreach ($Issue['customfields']['customfield'] as $k=>$CF) {
                    if (!isset($CF['customfieldname'])) continue;
                    // Mise en production
                    $value = $CF['customfieldvalues']['customfieldvalue'];
                    switch ($CF['customfieldname']) {
                        case 'Mise en production':
                            $Event->setChangeDate(new \DateTime($value));
                            break;
                        case 'Start date':
                            $Event->setChangeStart(new \DateTime($value));
                            $Event->setStartTime(new \DateTime($value));
                            break;
                        case 'End date':
                            $Event->setChangeEnd(new \DateTime($value));
                            $Event->setEndTime(new \DateTime($value));
                            break;
                        default:
                            break;
                    }
                }
            }
                    
            // A voir comment paramétrer cette partie           
            // Injection dependances ou DB ?
            $Event->setStatus($Issue['status']);            
            $Event->setEventSource('JIRA');
            $Event->setEventType($event_type);
            
            $em->persist($Event);

            // On sauvegarde les commentaires
            if (isset($Issue['comments'])) {                
                $Comments = $Issue['comments']['comment'];
                print_r($Comments);
                $c = 0;
                while (isset($Comments[$c])) {                
                    $Action = $em->getRepository("AriiACKBundle:Action")->findOneBy([ 'event' => $Event, 'num' => $c ]);   
                    if (!$Action)
                        $Action = new \Arii\ACKBundle\Entity\Action();

                    $Action->setEvent($Event);
                    $Action->setNum($c);
                    
                    $Action->setName(sprintf("%s-%03d",$name,$c));
                    $Action->setTitle($this->getTitle($Comments[$c]));

                    if (isset($Comments[$c.'_attr'])) {
                        if (isset($Comments[$c.'_attr']['created']))
                            $Action->setDateTime(new \DateTime($Comments[$c.'_attr']['created']));   
                        if (isset($Comments[$c.'_attr']['author']))
                            $Action->setUser($Comments[$c.'_attr']['author']);
                    }
                    
                    $Action->setDescription($Comments[$c]);

                    $em->persist($Action);
                    $c++;
                }
            }            
            if ($n++ % 100 == 0)
                $em->flush();        
        }
        $em->flush();
        return new Response(sprintf( "# %d\n%d s\n%s\n",$n,(time()-$time),"success"));
        
    }
    
    private function getTitle($title) {
        $title = ltrim(strip_tags($title));
        // Prochain .
        if (($p=strpos($title,'.'))>0)
            $title = substr($title,0,$p+1);
        if (strlen($title)>252)
            $title = substr($title,0,252).'...';
        return $title;        
    }
    
    private function MyState($status) {
        $Conv = [
            'Fermée' => 'CLOSE',
            'Ouverte'=> 'OPEN',
            'Rouverte'=> 'OPEN',
            'En cours'=> 'OPEN'
        ];
        if (isset($Conv[$status])) 
            return $Conv[$status]; 
        return $status;
    }
}

