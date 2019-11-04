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
}
