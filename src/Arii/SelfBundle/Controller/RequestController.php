<?php

namespace Arii\SelfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class RequestController extends Controller
{
    public function indexAction($req='')
    {
        $request = Request::createFromGlobals();
        $req = $request->get('request');
        $mode = $request->get('mode');
        return $this->render('AriiSelfBundle:Request:index.html.twig',[ 'request' => $req, 'mode' => $mode ]);
    }

    public function editAction($req='')
    {
        $request = Request::createFromGlobals();
        if ($req=='')   $req = $request->get('request');
        $mode = $request->get('mode');
        return $this->render('AriiSelfBundle:Request:edit.html.twig',[ 'request' => $req, 'mode' => $mode ]);
    }
    
    public function menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiSelfBundle:Request:menu.xml.twig',[], $response);
    }
    
    public function menu2Action()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiSelfBundle:Request:menu2.xml.twig',[], $response);
    }
    
    public function formAction($form='') {
        $request = Request::createFromGlobals();
        if ($request->get('form')!='')
            $form = $request->get('form');
        
        $yaml = new Parser();
        $lang = $this->getRequest()->getLocale();

        $portal = $this->container->get('arii_core.portal');
        $file = $portal->getWorkspace().'/Self/'.$lang.'/'.$form.'.yml';    
        
        $content = file_get_contents($file);
        $v = $yaml->parse($content);
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');
        $form = 
'[                
    {
        "type": "settings",
        "position": "label-left",
        "labelWidth": 100,
        "inputWidth": 240,
        "labelAlign": "right",
        "position": "label-left"
    },
    {
        "type": "fieldset",
        "margin-left": 50,
        "width": 900,
        "label": "'.$v['title'].'",
        "list": [
            {
                "type": "hidden",
                "name": "id"
            },            
            {
                "type": "input",
                "name": "REFERENCE",
                "label": "Référence",
                "required": "true",
                "note": {
                    "text": "Code ou Url pour lier cette demande à un processus externe" 
                }            
            },
'.$v['form'].',
            {
                "type": "hidden",
                "name": "CALL",
                "value": "'.$form.'"
            },
            {
                "type": "input",
                "name": "DESCRIPTION",
                "label": "Description",
                "rows": 3,
                "note": {
                    "text": "Décrire la raison de la demande" 
                }            
            },
            {
                "type": "newcolumn"
            },
            {
                "type": "calendar",
                "name": "PLANNED",
                "label": "Date de déclenchement", 
                "dateFormat": "%Y-%m-%d %H:%i",
                "readonly": 1,
                "enableTime": "true"
            },
            {
                "type": "checkbox",
                "name": "TEMPLATE",
                "label": "Template"
            },
            {
                "type": "button",
                "name": "SUBMIT",
                "value": "Go!",
                "inputLeft": 100
            }
        ]
    }
]';

        $response->setContent( $form );
        return $response;                
    }

    public function saveAction()
    {
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        $id = $request->get('id');
        $req = $this->getDoctrine()->getRepository('AriiSelfBundle:Request')->find($id);
        if (!$req)
            $req = new \Arii\SelfBundle\Entity\Request();
        
        // Les parametres
        $Params = [];
        foreach ($_POST as $k=>$v)  {
            if (strtoupper($k)==$k) continue;
            if ($k=='id') continue;
            $Params[$k] = $v;                
        }
        
        // MODEL
        $model = $request->get('CALL');
                
        // on complete avec les infos internes qui risquent de changer
        $yaml = new Parser();
        $lang = $this->getRequest()->getLocale();
        $portal = $this->container->get('arii_core.portal');        
        $file = $portal->getWorkspace().'/Self/'.$lang.'/'.$model.'.yml';
        
        $content = file_get_contents($file);
        $v = $yaml->parse($content);

        $req->setName($model);        
        $req->setTitle($v['title']);
        if (isset($v['type']))
            $req->setRunType($v['type']);
        if (isset($v['call']))
            $req->setRunCommand($v['call']);
        $req->setReference($request->get('REFERENCE'));        
        $req->setDescription($request->get('DESCRIPTION'));
        $req->setPlanned(new \DateTime($request->get('PLANNED')));        

        $req->setParameters($Params);
        $req->setReqStatus('QUEUED');   
        $req->setCreated(new \DateTime());

        $username = $portal->getUsername();
        $req->setUsername($username);

        $user = $this->getDoctrine()->getRepository('AriiUserBundle:User')->findOneBy([ 'username' => $username ]);

        // seulement si authentifié
        $req->setRequester($user);

        $em->persist($req);
        $em->flush();
        
        return new Response("success");
    }

    public function loadAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $Status = $this->getDoctrine()->getRepository('AriiSelfBundle:Request')->find($id);
        $Data = $Status->getParameters();
        
        $Data['id'] = $id;
        $Data['REFERENCE'] = $Status->getReference();
        $Data['DESCRIPTION'] = $Status->getDescription();
        $Data['PLANNED'] = ($Status->getPlanned()?$Status->getPlanned()->format('Y-m-d h:i'):'');
        
        $render = $this->container->get('arii_core.render');
        return $render->form($Data);        
    }

    public function parametersAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<rows>';
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';

        $Status = $this->getDoctrine()->getRepository('AriiSelfBundle:Request')->find($id);
        if ($Status) {
            $Data = $Status->getParameters();

            foreach($Data as $k=>$v) {
                $list .= '<row><cell>'.$k.'</cell><cell>'.$v.'</cell></row>'; 
            }
        }
        $list .= '</rows>';

        $response->setContent( $list );
        return $response;        
    }

    public function historyAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<rows>';
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';

        $Request = $this->getDoctrine()->getRepository('AriiSelfBundle:Request')->find($id);
        $History = $this->getDoctrine()->getRepository('AriiSelfBundle:History')->findBy( [ 'request' => $Request ], [ 'processed' => 'desc' ]);
        foreach($History as $H) {
            $list .= '<row>'
                    . '<cell>'.$H->getRunStatus().'</cell><cell>'.$H->getStarted()->format('Y-m-d H:i').'</cell>'
                    . '<cell>'.($H->getProcessed()?$H->getProcessed()->format('Y-m-d H:i'):'').'</cell>'
                    . '</row>'; 
        }
        $list .= '</rows>';

        $response->setContent( $list );
        return $response;        
    }
    
    // Remise en file d'attente
    public function queueingAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        // On historise pour ne pas perdre l'information de reprise
        // on historise tout ce qui est terminé
        $Request = $this->getDoctrine()->getRepository("AriiSelfBundle:Request")->find($id);

        $Request->setReqStatus('QUEUED');
        $Request->setProcessed(null);
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($Request);
        $em->flush();
        
        return new Response("success");
    }

    public function cancelingAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        // On historise pour ne pas perdre l'information de reprise
        // on historise tout ce qui est terminé
        $Request = $this->getDoctrine()->getRepository("AriiSelfBundle:Request")->find($id);        

        $em = $this->getDoctrine()->getManager();
        $em->remove($Request);
        $em->flush();
        
        return new Response("success");
    }

    public function statusAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        // Demande terminée
        $Request = $this->getDoctrine()->getRepository("AriiSelfBundle:Request")->find($id);
                
        
        return $this->render('AriiSelfBundle:Request:bootstrap.html.twig', [
            'name'  => $Request->getName(),
            'title' => $Request->getTitle(),
            'code'  => $Request->getRunCommand(),
            'status' =>   $Request->getRunStatus(),
            'exit' =>   $Request->getRunExit(),
            'log'  => $Request->getRunLog(),
            'client_ip' =>   $Request->getClientIp(),
            'requester' =>   $Request->getRequester(),
            'planned' =>   $Request->getPlanned()->format('Y-m-d h:i:s'),
            'created' =>   $Request->getCreated()->format('Y-m-d h:i:s'),
            'processed' => ($Request->getProcessed()?$Request->getProcessed()->format('Y-m-d H:i:s'):''),
            'parameters' => $Request->getParameters()
        ]);
    }
    
}
