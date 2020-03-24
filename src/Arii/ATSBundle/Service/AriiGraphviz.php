<?php
namespace Arii\ATSBundle\Service;
/* Fonction de transformation de tableau en DOT 
 * 
 */
class AriiGraphviz
{
/*
Array
(
    [0] => Array
        (
            [jobId] => 260318
            [jobName] => GT.STAO.BOX.VerifIndexDocumentum
            [description] => Verification de l indexation documentum
            [children] => Array
                (
                    [0] => Array
                        (
                            [jobId] => 260319
                            [jobName] => GT.STAO.JOB.LstDocumentsQuittance
                            [description] => Creation de la liste des documents a quittancer
                        )

                    [1] => Array
                        (
                            [jobId] => 260320
                            [jobName] => GT.STAO.JOB.GrpDocumentsQuittance
                            [description] => Groupement des documents a quittancer
                        )
 */  
    protected $graphviz;
    protected $images_path;
    
    
    public function __construct (
        \Arii\CoreBundle\Service\AriiGraphviz $graphviz,
        $rootdir
    ) {        
        $this->graphviz = $graphviz;
        
        $images_path =  str_replace('/',DIRECTORY_SEPARATOR,$rootdir.'/../web/images');
        $this->images_path = $images_path;        
    }
            
    public function Jobs($Jobs,$Options) {   
        // Transformation du tableau en dot
        $Out = [];
        foreach ($Jobs as $Job ) {
            array_push($Out, $this->Job($Job,$Out));
        }        
        $out = implode("\n",$Out);
        $format = '
fontname=arial 
fontsize=9
node [shape=plaintext,fontname=arial,fontsize=9]
edge [fontname=arial,fontsize=8,decorate=true,compound=true]
bgcolor=white
imagescale=false
fixedsize=true
splines=polyline
';
        return $this->graphviz->dot("strict digraph Jobs { $format $out }",$Options);                
    }
    
    private function Job($Job) {
        // Noeud
        $Out = [];
        // Dependances
        if (isset($Job['dependencies'])) {
            foreach ($Job['dependencies'] as $Condition) {
                if ($Condition['type']=='d') {
                        $design='color=blue';
                }
                elseif ($Condition['type']=='s') { 
                        $design='color=green';
                }
                elseif ($Condition['type']=='f') { 
                        $design='color=red';
                }
                elseif ($Condition['type']=='t') { 
                        $design='color=purple';
                }
                elseif ($Condition['type']=='e') { 
                        $design='color=orange;label="e='.$Condition['value'].'"';
                }
                else {
                        $design='color=black';
                }
                if ($Condition['condMode']==2) {
                    $design .= ';style=dashed';
                }
                array_push($Out,'"'.$Condition['jobName'].'" -> "'.$Job['jobName'].'" ['.$design.']');
            }
        }
        // Enfant ?
        if ($Job['jobType']=='BOX') {
            array_push($Out,'subgraph cluster'.$Job['jobId'].' {');
            array_push($Out,'style=dashed');
            array_push($Out, '"'.$Job['jobName'].'" [fontcolor=blue;fontsize=10]');
            array_push($Out, $this->NodeBox($Job));
            if (isset($Job['children'])) {
                foreach($Job['children'] as $Child ) {
                    array_push($Out, $this->Job($Child));
                }
            }
            array_push($Out,'}');
        }
        else {
            array_push($Out, $this->NodeJob($Job));
        }
        return implode("\n",$Out);
    }

    private function NodeBox($Job) {        
        $label  = '<TABLE BORDER="1" CELLBORDER="0" COLOR="grey" CELLSPACING="0" BGCOLOR="lightyellow">';
        $label .= '<TR><TD><FONT POINT-SIZE="12">'.$Job['jobName'].'</FONT></TD><TD><IMG SCALE="FALSE" SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/box.png').'"/></TD></TR>';
        $label .= '<TR><TD COLSPAN="2">'.$Job['description'].'</TD></TR>';
        $label .= '</TABLE>';
        return '"'.$Job['jobName'].'" [URL="#'.$Job['jobName'].'";label=<'.$label.'>]';        
        
    }
    
    private function NodeJob($Job) {        
        $label  = '<TABLE BORDER="1" CELLBORDER="0" COLOR="grey" CELLSPACING="0" BGCOLOR="#eeeeee">';
        $label .= '<TR><TD>'.$Job['jobName'].'</TD><TD><IMG SCALE="FALSE" SRC="'.str_replace('/',DIRECTORY_SEPARATOR,$this->images_path.'/job.png').'"/></TD></TR>';
        $label .= '<TR><TD COLSPAN="2">'.$Job['description'].'</TD></TR>';
        $label .= '</TABLE>';
        return '"'.$Job['jobName'].'" [URL="#'.$Job['jobName'].'";label=<'.$label.'>]';        
        
    }
    
}
