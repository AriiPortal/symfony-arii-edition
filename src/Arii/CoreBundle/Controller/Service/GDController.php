<?php
namespace Arii\CoreBundle\Controller\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class GDController extends Controller
{

    public function microbarAction()
    {
        $gd = $this->container->get('arii_core.gd');
        $response = new Response();
        $response->headers->set('Content-Type','image/png');
        $im = $gd->microbar([
            [ 'value' => 10, 'color' => 'red' ],
            [ 'value' => 15, 'color' => 'green' ],
            [ 'value' => 36, 'color' => 'red' ],
            [ 'value' => 12, 'color' => 'green' ],
            [ 'value' => 5, 'color' => 'yellow' ],
            [ 'value' => 8, 'color' => 'red' ],
            [ 'value' => 6, 'color' => 'green' ],
            [ 'value' => 10, 'color' => 'green' ],
            [ 'value' => 18, 'color' => 'green' ]            
        ], 500, 10, 4);
        return $response;
    }
    
}