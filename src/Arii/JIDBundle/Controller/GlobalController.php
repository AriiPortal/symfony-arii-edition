<?php

namespace Arii\JIDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Parser;

class GlobalController extends Controller
{
    protected $images;
    protected $TZLocal;
    protected $TZSpooler;
    protected $TZOffset;
    protected $CurrentDate;

    public function __construct( )
    {
        $request = Request::createFromGlobals();
        $this->images = $request->getUriForPath('/../arii/images/wa');

        $this->CurrentDate = date('Y-m-d');
    }

    public function indexAction()
    {
        return $this->render('AriiJIDBundle:Global:index.html.twig', [ 'db' => 'ojs_db' ]);
    }
    
    public function ordersAction()
    {
        $Orders = $this->getDoctrine()->getRepository('AriiReportBundle:ActivityStatus')->findActivities();        
        $Grid = [];
        
        foreach ($Orders as $Order) {
            if (
                    (substr($Order['state'],0,1)=='!')
                    or (strtoupper($Order['state'])=='ERROR')
                ) {
                    $status = 'ERROR';
                }
            else if ($Order['endTime']==null) {
                $status = 'RUNNING';
            }
            else {
                $status = 'SUCCESS';
            }
            array_push($Grid, [
                'id'        =>  $Order['id'],
                'spoolerId' =>  $Order['spoolerId'],
                'folder'    =>  dirname($Order['jobChain']),
                'jobChain'  =>  basename($Order['jobChain']),
                'orderId'   =>  $Order['orderId'],
                'title'     =>  $Order['title'],
                'state'     =>  $Order['state'],
                'stateText' =>  $Order['stateText'],
                'startTime' =>  $Order['startTime'],
                'endTime'   =>  $Order['endTime'],
                'status'     => $status
            ]);
        }
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Grid,'spoolerId,folder,jobChain,orderId,title,state,stateText,startTime,endTime','status');
    }
        
}
