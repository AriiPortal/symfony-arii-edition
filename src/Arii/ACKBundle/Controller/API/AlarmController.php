<?php
namespace Arii\ACKBundle\Controller\API;

use Arii\ACKBundle\Entity\Alarm;

use Arii\CoreBundle\Exception\ResourceValidationException;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use TimeInc\SwaggerBundle\Swagger\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

use JMS\Serializer\SerializationContext;

/**
 * 
 * @SWG\Parameter(
 *   parameter="alarm_id_in_path_required",
 *   name="alarm_id",
 *   description="The ID of the alarm",
 *   type="integer",
 *   format="int64",
 *   in="path",
 *   required=true
 * )
 *
 * @SWG\Parameter(
 *   parameter="alarm_in_body",
 *   in="body",
 *   name="alarm",
 *   @SWG\Schema(ref="#/definitions/Alarm")
 * )
 *  
 * @SWG\Path(
 *   path="/alarms/{alarm_id}",
 *   @SWG\Parameter(ref="#/parameters/alarm_id_in_path_required")
 * )
 * 
 * @SWG\Response(
 *   response="alarm",
 *   description="All information about an alarm",
 *   @SWG\Schema(ref="#/definitions/Alarm")
 * )
 * 
 * @Rest\View(StatusCode = 200)
 */

class AlarmController extends FOSRestController
{
    /**
     * @SWG\Get(
     *   path="/alarms",
     *   summary="list alarms",
     *   @SWG\Response(
     *     response=200,
     *     description="A list with alarms"
     *   )
     * )
     */
    public function searchAction()
    {
        $Alarms = $this->getDoctrine()->getRepository('AriiACKBundle:Alarm')->findAll();
        
        $data = $this->get('jms_serializer')->serialize($Alarms, 'json', SerializationContext::create()->setGroups(array('list')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Rest\Post("/alarmes")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter(
     *      "Alarm", 
     *      converter="fos_rest.request_body",
     *      options={
     *         "validator"={ "groups"="Create" }
     *     }
     * )
     *
     * @Rest\View(StatusCode = 201)
     */
    public function createAction(Alarm $Alarm, ConstraintViolationList $violations)
    {      
        if (count($violations)) {
            return $this->view($violations, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($Alarm);
        $em->flush();

        return $Alarm;
    }
    
    /**
     * @SWG\Get(
     *   path="/alarms/{alarm_id}",
     *   summary="Affiche les informations pour une alarm identifiée par son id.",
     *   @SWG\Response(
     *          response=200,
     *          description="All information about an alarm",
     *          @SWG\Schema(ref="#/definitions/Alarm")
     *   )
     * )
     */    
    public function retrieveAction(Alarm $Alarm)
    {
        $data = $this->get('jms_serializer')->serialize($Alarm, 'json', SerializationContext::create()->setGroups(array('detail')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @SWG\Put(
     *     path="/alarms/{alarm_id}",
     *     summary="Add a new alarm.",
     *     description="ERIC",
     *     consumes={"alarm/json", "alarm/xml"},
     *     produces={"alarm/xml", "alarm/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Alarm Probe that needs to be added to the repository.",
     *         required=true,
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  maximum=64
     *              ),
     *              @SWG\Property(
     *                  property="description",
     *                  type="string"
     *              )     
     *         ),
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Example extended response",
     *          ref="$/responses/Json",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Alarm"
     *              )
     *          )
     *     )
     * )
     */
    public function updateAction($id)
    {
        $Alarm = $this->getDoctrine()->getRepository("AriiCoreBundle:Alarm")->find($id);
        return $Alarm;        
    }

    /**
     * @SWG\Delete(
     *   path="/alarms/{alarm_id}",
     *   summary="Delete an alarm",
     *   @SWG\Response(
     *     response=200,
     *     description="L'alarm"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="une erreur ""bizarre"""
     *   )
     * )
     */    
    public function deleteAction($id)
    {
        $Alarm = $this->getDoctrine()->getRepository("AriiCoreBundle:Alarm")->find($id);
        return $Alarm;        
    }
    
}
