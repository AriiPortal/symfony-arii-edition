<?php
// src/Sdz/BlogBundle/Service/SdzAntispam.php
 
namespace Arii\CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/*
$Data = [
    [   "startTime"  => 1565190002,
        "runTime" => 1,
        "status" => 'SUCCESS'
    ],
    [   "startTime"  => 1565276402,
        "runTime" => 1,
        "status" => 'FAILURE'
    ],
    [   "startTime"  => 1565276402,
        "runTime" => 1,
        "status" => 'FAILURE'
    ],
    [   "startTime"  => 1565366802,
        "runTime" => 1,
        "status" => 'SUCCESS'
    ]
];
 */

class AriiGD
{
    private $Colors = [
        'white'  => '#FFFFFF',
        'silver' => '#C0C0C0',
        'gray'   => '#808080',
        'black'  => '#000000',
        'red'    => '#FF0000',
            'failure'=> '#FF0000',
        'maroon' => '#800000',
        'yellow' => '#FFFF00',
        'olive'  => '#808000',
        'lime'   => '#00FF00',
        'green'  => '#008000',
            'success'=> '#008000',
        'aqua'   => '#00FFFF',
        'teal'   => '#008080',
        'blue'   => '#0000FF',
        'navy'   => '#000080',
        'fuchsia'=> '#FF00FF',
        'purple' => '#800080'
    ];
    
    public function __construct( ContainerInterface $container, \Arii\CoreBundle\Service\AriiPortal $portal )
    {   
    }
    
    // Fonctions de simplification
    // Heure en timestamp
    private function getTime($time) {
        if (is_numeric($time)) 
            return $time;
        return $time->format('U');
    }

    // gestion des couleurs
    private function Color($im, $text) {        
        if (substr($text,0,1)!='#') {
            $text = strtolower($text);
            if (isset($this->Colors[$text]))
                $text = $this->Colors[$text];
            else 
                $text = '#000000';
        }        
        return imagecolorallocate($im, hexdec(substr($text,1,2)), hexdec(substr($text,3,2)), hexdec(substr($text,5,2)));
    }
        
    public function StatusByPeriod( $Data, 
            $period = 4,  // Periode de 6 heures
            $height = 24, // Hauteur du graphique
            $col    = 5   // Largeur de colonne
            ) {
        /* la hauteur représente une journée de 24h 
         * chaque colonne est un nouveau jour 
         * chaque point est une heure
         * chaque heure synthetise le statut:
         * - rouge:  tout est en erreur
         * - orange: au moins une erreur dans l'heure
         * - vert:   tout est ok
         */
        /* On traite les datas pour obtenir un statut par heure */
        /* le tableau est trié par date */        
/*
$Data = [
    [   "startTime"  => 1565190002,
        "runTime" => 1,
        "status" => 'SUCCESS'
    ],
    [   "startTime"  => 1565276402,
        "runTime" => 1,
        "status" => 'FAILURE'
    ],
    [   "startTime"  => 1565276402,
        "runTime" => 1,
        "status" => 'FAILURE'
    ],
    [   "startTime"  => 1565366802,
        "runTime" => 1,
        "status" => 'SUCCESS'
    ]
];
 */
        if (empty($Data))
            exit();
        
        // regroupement
        $group = $period; // Heure
        $ghour = 3600*$group;
        $gday  = 24/$group;
        
        $nb = count($Data);        
        $first = $this->getTime($Data[0]['startTime']);
        // $last = $Data[$nb-1]['startTime']+$Data[$nb-1]['runTime'];
        $last = $this->getTime($Data[$nb-1]['endTime']);
        
        // Heure de départ a ajouter 
        $hour = round(($first  % ($gday*$ghour))/$ghour, 0, PHP_ROUND_HALF_DOWN);
        
        // Nombre de jours 
        $days = round(($last - $first)/($gday*$ghour), 0, PHP_ROUND_HALF_DOWN) + 1;

        # On regroupe par heure
        $Graph = [];
        $Success = [];
        foreach ($Data as $D) {
            // On ne prend que les heures             
            $startTime = $this->getTime($D['startTime']);
            $endTime = $this->getTime($D['endTime']);
            $start = round(($startTime -$first)/$ghour, 0, PHP_ROUND_HALF_DOWN)+$hour;
            $runTime = $endTime - $startTime ;
            for($i=0;$i<=round($runTime/$ghour, 0, PHP_ROUND_HALF_DOWN);$i++) {
                if (isset($Graph[$start+$i])) {
                    $Graph[$start+$i]++;
                    $Success[$start+$i] += 1;
                }
                else {
                    $Graph[$start+$i]=1;             
                    $Success[$start+$i]= 1;                
                }
            }
        }
        /* Les jours sont sur l'absice
         * Les heures sur l'ordonnée
         */
        // Taille du graphique a intégrer dans un tableau
        $width = $days * $col; // Nombre de jours par a largeur de colonne
        $scale = $height/$gday; // Echelle 

        $im = imagecreate($width,$height);
        $green = imagecolorallocate($im, 50, 120, 40);
        $red = imagecolorallocate($im, 255, 0, 0);
        $orange = imagecolorallocate($im, 255, 200, 70);
        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 220, 220, 220);
        imagefilledrectangle($im, 0,0,$width,$height,$white);
        imagecolortransparent($im, $white);
        // on dessine les jours 
        for($i=0;$i<$days;$i++) {
             imageline($im, $i*$col, 0, $i*$col, $height , $grey );
        }
        // et midi
        imageline($im, 0, $height/2, $days*$col, $height/2 , $grey );
        
        foreach ($Graph as $date=>$nb) {
            $d = round($date/$gday, 0, PHP_ROUND_HALF_DOWN);
            $h = ($date) % $gday;
            if ($Success[$date]==$nb)
                $color = $green;
            elseif ($Success[$date]==0)
                $color = $red;
            else
                $color = $orange;
            imagefilledrectangle($im,($d-1)*$col,$height-($h-1)*$scale,$d*$col-1,$height-$h*$scale-1,$color);
        }        
        return $im;
    }
    
    /* Data est un tableau contenant:
     *  - start
     *  - runtime
     *  - color
     */
    public function StatusByPeriodOld( $Data, 
            $period = 4,  // Periode de 6 heures
            $height = 24, // Hauteur du graphique
            $col    = 5   // Largeur de colonne
            ) {
        /* la hauteur représente une journée de 24h 
         * chaque colonne est un nouveau jour 
         * chaque point est une heure
         * chaque heure synthetise le statut:
         * - rouge:  tout est en erreur
         * - orange: au moins une erreur dans l'heure
         * - vert:   tout est ok
         */
        /* On traite les datas pour obtenir un statut par heure */
        /* le tableau est trié par date */        
/*
$Data = [
    [   "startTime"  => 1565190002,
        "runTime" => 1,
        "success" => 0
    ],
    [   "startTime"  => 1565276402,
        "runTime" => 1,
        "success" => 0
    ],
    [   "startTime"  => 1565276402,
        "runTime" => 1,
        "success" => 1
    ],
    [   "startTime"  => 1565366802,
        "runTime" => 1,
        "success" => 1
    ]
];
 */
        if (empty($Data))
            exit();
        
        // regroupement
        $group = $period; // Heure
        $ghour = 3600*$group;
        $gday  = 24/$group;
        
        $nb = count($Data);
        $first = $Data[0]['startTime'];
        // $last = $Data[$nb-1]['startTime']+$Data[$nb-1]['runTime'];
        $last = $Data[$nb-1]['endTime'];
        
        // Heure de départ a ajouter 
        $hour = round(($first  % ($gday*$ghour))/$ghour, 0, PHP_ROUND_HALF_DOWN);
        
        // Nombre de jours 
        $days = round(($last - $first)/($gday*$ghour), 0, PHP_ROUND_HALF_DOWN) + 1;

        # On regroupe par heure
        $Graph = [];
        $Success = [];
        foreach ($Data as $D) {
            // On ne prend que les heures             
            $start = round(($D['startTime']-$first)/$ghour, 0, PHP_ROUND_HALF_DOWN)+$hour;
            $runTime = $D['endTime'] - $D['startTime'];
            for($i=0;$i<=round($runTime/$ghour, 0, PHP_ROUND_HALF_DOWN);$i++) {
                if (isset($Graph[$start+$i])) {
                    $Graph[$start+$i]++;
                    $Success[$start+$i] += $D['success'];
                }
                else {
                    $Graph[$start+$i]=1;                
                    $Success[$start+$i]= $D['success'];                
                }
            }
        }
        /* Les jours sont sur l'absice
         * Les heures sur l'ordonnée
         */
        // Taille du graphique a intégrer dans un tableau
        $width = $days * $col; // Nombre de jours par a largeur de colonne
        $scale = $height/$gday; // Echelle 

        $im = imagecreate($width,$height);
        $green = imagecolorallocate($im, 50, 120, 40);
        $red = imagecolorallocate($im, 255, 0, 0);
        $orange = imagecolorallocate($im, 255, 200, 70);
        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 220, 220, 220);
        imagefilledrectangle($im, 0,0,$width,$height,$white);
        imagecolortransparent($im, $white);
        // on dessine les jours 
        for($i=0;$i<$days;$i++) {
             imageline($im, $i*$col, 0, $i*$col, $height , $grey );
        }
        // et midi
        imageline($im, 0, $height/2, $days*$col, $height/2 , $grey );
        
        foreach ($Graph as $date=>$nb) {
            $d = round($date/$gday, 0, PHP_ROUND_HALF_DOWN);
            $h = ($date) % $gday;
            if ($Success[$date]==$nb)
                $color = $green;
            elseif ($Success[$date]==0)
                $color = $red;
            else
                $color = $orange;
            imagefilledrectangle($im,($d-1)*$col,$height-($h-1)*$scale,$d*$col-1,$height-$h*$scale-1,$color);
        }        
        return $im;
    }

    // ne fait que générer un graphique a partir des données calculées
    // Parametre n,value,color
    public function runtimes( $Data, $width=120,$height=20, $l=-1 ) {
        $im = imagecreate($width,$height);
        // Un tri ?        
        $Runtime = [];
        $n = 0;
        foreach ($Data as $k=>$D) {
            if (isset($D['duration']))
                $Runtime[$n] = $D['duration'];
            else 
                $Runtime[$n] = $this->getTime($D['endTime'])-$this->getTime($D['startTime']); 
            $Color[$n] = $this->Color($im,$D['status']);
            $n++;
        }
        $max = max($Runtime);
        $min = min($Runtime);
        
        // largeur des barres
        if ($l<0)
            $l = round($width/$nb,0, PHP_ROUND_HALF_DOWN)-1;
        // ratio hauteur
        if ($max-$min>0) 
            $r = $height/($max-$min);
        else 
            $r = $height;

        $white = $this->Color($im,'white');
        imagecolortransparent($im, $white);
        imagefilledrectangle($im, 0, 0, $width, $height, $white);
        foreach($Runtime as $n=>$v) {
            if ($v==0) {
                imagefilledrectangle($im, $n*$l+2, $height-1, ($n+1)*$l, $height-2, $Color[$n]);
            }
            else if (($v-$min)==0) {
                imagefilledrectangle($im, $n*$l+2, $height, ($n+1)*$l, $height-round($r,0, PHP_ROUND_HALF_DOWN), $Color[$n]);
            }
            else {
                imagefilledrectangle($im, $n*$l+2, $height, ($n+1)*$l, $height-round(($v-$min)*$r,0, PHP_ROUND_HALF_DOWN), $Color[$n]);
            }
        }               
        imagepng($im);
        imagedestroy($im);
        return $im;
    }
    
    // ne fait que générer un graphique a partir des données calculées
    // Parametre n,value,color
    public function microBar( $Values, $width=120,$height=20, $l=-1 ) {
        
        $im = imagecreate($width,$height);
        // Premiere passe pour identifier les elements
        // Calcule des couleurs et du max
        $max = 0;
        $nb = 0;
        $Col['white'] = $this->Color($im, 'white');
        foreach($Values as $V) {
            $v = $V['value'];
            if ($v>$max) $max=$v;
            $c = $V['color'];
            if (!isset($Col[$c])) 
                $Col[$c] = $this->Color($im, $c);
            $nb++;
        }  
        // largeur des barres
        if ($l<0)
            $l = round($width/$nb,0, PHP_ROUND_HALF_DOWN)-1;
        // ratio hauteur
        $r = $height/$max;

        imagecolortransparent($im, $Col['white']);
        imagefilledrectangle($im, 0, 0, $width, $height, $Col['white'] );
        $n=0;
        foreach($Values as $V) {
            $v = $V['value'];
            $c = $V['color'];
            imagefilledrectangle($im, $n*$l+2, $height, ($n+1)*$l, $height-round($v*$r,0, PHP_ROUND_HALF_DOWN), $Col[$c]);
            $n++;
        }       
        
        imagepng($im);
        imagedestroy($im);
        return $im;
    }
    
    public function percentAction($percent=100,$color='LEGEND') {        
        $request = Request::createFromGlobals();
        // Heure locale
        if ($request->query->get( 'percent' )!='') {
            $percent = $request->query->get( 'percent' );
        }
        if ($request->query->get( 'color' )!='')
            $color = $request->query->get( 'color' );

        $width = 50; $height = 16;
        if ($percent<2) $percent=2;
        $im = imagecreate(round($percent/2),$height);
        if (substr($color,0,1)=='#') {
            $col = imagecolorallocate($im, hexdec(substr($color,1,2)), hexdec(substr($color,3,2)), hexdec(substr($color,5,2)));
        }
        else {
            $white = imagecolorallocate($im, 255, 255, 255);
            $black = imagecolorallocate($im, 0, 0, 0);
            $green = imagecolorallocate($im, 50, 120, 40);
            $red = imagecolorallocate($im, 255, 0, 0);
            $orange = imagecolorallocate($im, 255, 200, 70);
            $grey = imagecolorallocate($im, 200, 200, 200);
            $color_rgb = array( 'READY' => $green,
                            'green' => $green,
                            'black' => $black,
                            'red' => $red,
                            'orange' => $orange );
            if (isset($color_rgb[$color]))
                $col = $color_rgb[$color];
            else 
                $col = $color_rgb['black'];
        }
            header ("Content-type: image/png");
        // imagefilledrectangle($im, 100-$percent, 1, $width, $height-2, $col);
        imagefilledrectangle($im, 0, 1, round($percent/2), $height-2, $col);
        imagepng($im);        
        exit();
    }

    
    public function Gantt($start='0000',$end='2359',$days=0,$status='LEGEND') {
        $request = Request::createFromGlobals();
        // Heure locale
        $start = $this->Hours($request->query->get( 'start' ));
        if ($request->query->get( 'end' )!='') {
            $end = $this->Hours($request->query->get( 'end' ));
        }
        else {
            $Now = localtime(time(), true);
            $end = $Now['tm_hour']+$Now['tm_min']/60;
        }
       // print "(($start $end))";
        if ($request->query->get( 'status' )!='')
            $status = $request->query->get( 'status' );
        if ($request->query->get( 'days' )>0)
            $days = $request->query->get( 'days' );
        if ($days>7) $days=7;
        
        $step = 20;
        $width = $step*24; $height = 16;
        $im = imagecreate($width,$height);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $green = imagecolorallocate($im, 50, 120, 40);
        $red = imagecolorallocate($im, 255, 0, 0);
        $orange = imagecolorallocate($im, 255, 200, 70);
        $grey = imagecolorallocate($im, 200, 200, 200);
        imagecolortransparent($im, $white);
        header ("Content-type: image/png");
        // Legende
        if ($status == 'LEGEND') {
            $font = dirname(__FILE__) .'/../Resources/fonts/arial.ttf';
            for($i=0;$i<24;$i++) {
                imagettftext($im, 8, 0, $i*$step , 10, $black, $font, $i);
            }            
            imagepng($im);
            exit();            
        }
        $color = array( 'READY' => $green,
                        'SUCCESS' => $green,
                        'STOPPED' => $black,
                        'FAILURE' => $red,
                        'RUNNING' => $orange );
        for($i=0;$i<24;$i++) {
            imageline ($im , $i*$step , 0 , $i*$step , $height , $grey );
        }
        if (isset($color[$status]))
            $col = $color[$status];
        else
            $col = $black;
        
        if ($start>$end) {
            $h = ($height/2)-$days-2;
            imagefilledrectangle($im, $start*$step, 1, 24*$step, $h+1, $col);            
            imagefilledrectangle($im, 0,$days*2+$h+2, $end*$step, ($h+$days)*2+1, $col);
            for($i=0;$i<$days;$i++) {
                imageline($im, 0, $h+3+($i*2), $width, $h+3+($i*2), $black);
            }
        }
        elseif ($days==0) {
            imagefilledrectangle($im, $start*$step, 1, $end*$step, $height-2, $col);
        }
        else {
            $h = ($height/2)-$days-2;
            imagefilledrectangle($im, $start*$step, 1, 24*$step, $h+1, $col);            
            imagefilledrectangle($im, 0,$days*2+$h+2, $end*$step, ($h+$days)*2+1, $col );
            for($i=0;$i<$days;$i++) {
                imageline($im, 0, $h+3+($i*2), $width, $h+3+($i*2), $black );
            }
        }
        imagepng($im);        
        exit();
    }
 
    private function Hours($date) {
        return substr($date,0,2)+substr($date,2,2)/60;
    }
    
}
