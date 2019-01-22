<?php
// src/Sdz/BlogBundle/Service/SdzAntispam.php
 
namespace Arii\CoreBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class AriiGD
{
    private $Colors = [
        'white'  => '#FFFFFF',
        'silver' => '#C0C0C0',
        'gray'   => '#808080',
        'black'  => '#000000',
        'red'    => '#FF0000',
        'maroon' => '#800000',
        'yellow' => '#FFFF00',
        'olive'  => '#808000',
        'lime'   => '#00FF00',
        'green'  => '#008000',
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
            // print "(".($n*$l+2).")(".($height).")(".(($n+1)*$l).")(".($height-($v*$r)).")(".$c.")";
            imagefilledrectangle($im, $n*$l+2, $height, ($n+1)*$l, $height-round($v*$r,0, PHP_ROUND_HALF_DOWN), $Col[$c]);
            $n++;
        }        
        imagepng($im);
        imagedestroy($im);
        return $im;
    }
    
    // gestion des couleurs
    private function Color($im, $text) {
        if (substr($text,0,1)!='#') {
            if (isset($this->Colors[$text]))
                $text = $this->Colors[$text];
            else 
                $text = '#000000';
        }        
        return imagecolorallocate($im, hexdec(substr($text,1,2)), hexdec(substr($text,3,2)), hexdec(substr($text,5,2)));
    }
}
