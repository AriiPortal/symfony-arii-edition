<?php

namespace Arii\JIDBundle\Controller\Scheduler;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class GlobalController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiJIDBundle:Default:index.html.twig', [ 'db' => 'ojs_db' ]);
    }
        
}
