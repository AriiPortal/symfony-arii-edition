<?php

namespace Arii\ATSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FlowchartController extends Controller
{
    private $image_path;
    private $Color = array(
        's' => 'green',
        'f' => 'red',
        'd' => 'blue',
        'n' => 'orange',
        't' => 'purple',
        'e' => 'cyan',
        'B' => 'black'
    );
    
    public function generateAction($output = 'svg',$level=1, 
            $Show = array(            
            'BOX_NAME',
            'COMMAND',
            'STD_IN_FILE',
            'STD_OUT_FILE',
            'STD_ERR_FILE',
            'DAYS_OF_WEEK',
            'RUN_CALENDAR',
            'EXCLUDE_CALENDAR',
            'START_TIMES',
            'START_MINS',
            'RUN_WINDOWS',
            'LAST_START',
            'LAST_END',
            'OWNER',
            'RUN_MACHINE',
            'NEXT_START',
            'PROFILE'
        ) 
    )
    {

        $request = Request::createFromGlobals();
        $return = 0;

        $request = Request::createFromGlobals();
        $joid = $request->query->get( 'id' );

        $flowchart = $this->container->get('arii_core.flowchart');
        $Options = [];
        return $flowchart->sequence('TEST',$Options);
    }
}
