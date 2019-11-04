<?php
namespace Arii\MFTBundle\Service;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AriiSummaries
{
    public function __construct ( ) { }

/*********************************************************************
 * Summaries
 *********************************************************************/    
    public function Summaries($em,$Filter) {   
        $Summaries = $em->getRepository("AriiMFTBundle:Summary")->findSummaries();        
        return $Summaries;
    }

}
