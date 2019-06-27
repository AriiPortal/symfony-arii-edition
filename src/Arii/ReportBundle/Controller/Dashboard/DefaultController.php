<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Parser;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        $Filters['header']=$Filters['footer']=0;
        return $this->render('AriiReportBundle:Dashboard:index.html.twig', $Filters ); 
    }
}
