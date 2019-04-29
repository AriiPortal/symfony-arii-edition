<?php

namespace Arii\JIDBundle\Controller\Components;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ConfigController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiJIDBundle:Components\Config:index.html.twig' );
    }

    public function configAction()
    {
        $portal = $this->container->get('arii_core.portal');    
        $repoId = $portal->getDatabase();
        $DB = $this->getDoctrine()->getRepository('AriiCoreBundle:Repo')->findOneBy(['name' => $repoId ]);
        $Repo = [];
        if ($DB)
            $Repo['name'] = $DB->getName();
        else
            $Repo['name'] = $repoId;
        
        // On complete avec les infos 
        $em = $this->getDoctrine()->getManager($repoId);
        $Params = $em->getConnection()->getParams();
        foreach (['driver','host','port','user','dbname','service','servicename','charset'] as $k) {
            if (isset($Params[$k])) 
                $Repo[$k] = $Params[$k];
        } 
        
        return $this->render('AriiJIDBundle:Components\Config:config.html.twig', $Repo );
    }

}
