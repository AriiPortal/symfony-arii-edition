<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class CommonController extends Controller
{
    public function ribbonAction($bundle='Core')
    {
        $request = $this->container->get('request');
        if ($request->get('bundle')!='') 
            $bundle = $request->get('bundle');

        // Les cellules à activer
        $activated = $request->get('activated');
        
        $route = 'arii_'.$bundle.'_index';
        $here = $url = $this->generateUrl($route);
                
        $locale =  $this->get('request')->getLocale();
        foreach (array('en','fr') as $l ) {
            if ($l == $locale) continue;            
            $lang[$l]['string'] = $this->get('translator')->trans("lang.$l");     
            $lang[$l]['url'] = $this->generateUrl($route,array('_locale' => $l)); 
        }

        $portal = $this->container->get('arii_core.portal');
        $Modules = $portal->getModules(); 

        # Les utilisateur non authentifiés sont dans public
        $sc = $this->get('security.authorization_checker');
        $authenticated = ($sc->isGranted('IS_AUTHENTICATED_FULLY') or $sc->isGranted('IS_AUTHENTICATED_REMEMBERED')); 

        # On retrouve l'url active 
        $current = array( 
            'mod' => strtolower($bundle),
            'module' => $bundle,
            'class' => '',
            'url'    => $this->generateUrl($route),
            'img'  => strtolower($bundle),
            'title' => $this->get('translator')->trans('module.'.$bundle)
        );
        
        $liste = array();
        foreach ($Modules as $name => $Module) {
            // Modules limites à un droit ?
            if (($Module['role'] !='ANONYMOUS') and ((!$authenticated) or !$sc->isGranted($Module['role'])))
                continue;
            
            $url = $this->generateUrl('arii_'.$name.'_index');

            $test = 'arii_'.$name;
            if (substr($route,0,strlen($test))==$test) {
                $class='selected';
            }
            else {
                $class='';
            }
            if ($name!='Core')
                array_push($liste, 
                    array( 
                        'mod' => strtolower($name), 
                        'module' => $name, 
                        'url' => $url, 
                        'class' => $class, 
                        'title' => 'module.'.$name ) );
        }   

        // Heure locale à revoir
        $tz = array();

        // Informations utilisateur
        $repos = $portal->getReposByType($bundle);
           
        $userInfos = $portal->getUserInfo();
        $userInterface = $portal->getUserInterface();
        $userFilters = $portal->getUserFilters();

        // Informations en cours        
        $Activated = [
            'filters' => 0,
            'databases' => 0,
            'filters_ojs' => 0,
            'filters_ats' => 0,
            'history' => 0,            
            'refresh' => 0,
            'messaging' => 0,
            'help' => 0
        ];
        foreach (explode(',',$activated) as $act) {
            $Activated[$act] = 1;
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');   
        return $this->render('AriiCoreBundle:Common:ribbon.json.twig',
            [      
                'REPOS' => $repos,
                'MENU' => $liste, 
                'LANG' => $lang, 
                'BUNDLE' => $current, 
                'TZ' => $tz,
                'USER' => $userInfos,
                'ACTIVATED' => $Activated,
                'INTERFACE' => $userInterface,
                'FILTERS' => $userFilters
            ],
            $response 
        );
    }
    
    
    public function toolbarAction(
        $refresh=0,
        $export=0
    )
    {
        $request = $this->container->get('request');
        if ($request->get('refresh')>0)
            $refresh=1;
        if ($request->get('export')>0)
            $export=1;
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiCoreBundle:Common:toolbar.xml.twig', 
        [
            'refresh' => $refresh,
            'export'  => $export  
        ], 
        $response );
    }

}
