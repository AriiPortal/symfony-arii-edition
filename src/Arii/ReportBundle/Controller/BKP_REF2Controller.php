<?php

namespace Arii\ReportBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Arii\ReportBundle\Entity\BKP_REF;
use Arii\ReportBundle\Form\BKP_REFType;

/**
 * BKP_REF2 controller.
 * Addition du CRUD de base
 *
 */
class BKP_REF2Controller extends Controller
{

    /**
     * Mety a jour l'instance à supprimer
     *
     */
    public function updateAction(Request $request)
    {
        $ref  = $request->query->get( 'ref' );
        $inst = strtolower($request->query->get( 'inst' ));
        $db   = $request->query->get( 'db' );

        print "REF: $ref\n";
        print "INS: $inst\n";
        print "DB:  $db\n";

        $em = $this->getDoctrine()->getManager();
        $Reference = $em->getRepository('AriiReportBundle:BKP_REF')->findOneBy(
            [   'db_instance' => $inst,
                'db_name' => $db ] );

        $response = new Response(); 
        if (!$Reference) {
            print "ERROR!!";
            $response->setStatusCode( '417' );
            return $response;
        }
        print "ENV: ".$Reference->getDbEnv()."\n";
        print "REF: ".$Reference->getDbDesc()."\n";
        print "DEL: ".($Reference->getDeleted()?$Reference->getDeleted()->format('Y-m-d H:i'):'')."\n";

        $Reference->setDeleted(new \DateTime());
        $Reference->setDbDesc($ref);
        
        $em->persist($Reference);
        $em->flush();
        
        return $response;
    }
}
