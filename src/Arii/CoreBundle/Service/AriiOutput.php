<?php
namespace Arii\CoreBundle\Service;

/* Simples fonctions de conversion de tableau */
class AriiOutput
{
    public function Csv($Data, $sep=";", $eol="\n" ) {
        $n=0;
        $Csv='';
        foreach ($Data as $line=>$Infos) {
            if ($n==0) {
                // Ajout de la ligne d'entete
                $Csv .= join(";",array_keys($Infos)).$eol;
            }
            $Line = [];
            foreach ($Infos as $k=>$cell) {
                if (is_a($cell,'DateTime'))
                    array_push($Line,$cell->Format("Y-m-d H:i:s"));
                else if (is_a($cell,'DateInterval'))
                    array_push($Line,$cell->Format("%d %H:%i:%s"));
                else if (is_a($cell,'Array'))
                    {} // rien pour le moment                
                else 
                    array_push($Line,$cell);
            }
            $Csv .= join($sep,$Line).$eol;
            $n++;
            
        }
        return $Csv;
    }
    
    public function iCal($Data,$Mapping=[]) {
        // Mapping
        foreach (['id','startTime','endTime','summary','description'] as $var) {
            $Mapping[$var]  = (isset($Mapping[$var])?$Mapping[$var]:$var);
        }
        $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//sos/arii//NONSGML v1.0//EN
";
        $gmt = new \DateTimeZone('GMT');
        foreach ($Data as $d) {
            $ical .= "BEGIN:VEVENT\n";
            $ical .= "UID: ".(isset($d[$Mapping['id']])?$d[$Mapping['id']]:uniqid())."\n";
            $ical .= "DTSTART:".$d[$Mapping['startTime']]->setTimezone($gmt)->format('Ymd\THis\Z')."\n";
            if (isset($d[$Mapping['endTime']]))
                $ical .= "DTEND:".$d[$Mapping['endTime']]->setTimezone($gmt)->format('Ymd\THis\Z')."\n";
            else {
                $ical .= "DTEND:".$d[$Mapping['startTime']]->setTimezone($gmt)->format('Ymd\THis\Z')."\n";
                $ical .= "X-MICROSOFT-CDO-ALLDAYEVENT:FALSE\n";
            }
            $ical .= "SUMMARY:".$d[$Mapping['summary']]."\n";
            if (isset($d[$Mapping['description']]))
                $ical .= "DESCRIPTION:".$d[$Mapping['description']]."\n";
            $ical .= "END:VEVENT\n";
                
// DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
        }
$ical .= "END:VCALENDAR";

    return $ical;
    }
}
