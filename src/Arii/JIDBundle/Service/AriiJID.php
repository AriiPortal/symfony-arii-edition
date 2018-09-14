<?php
// src/Arii/JIDBundle/Service/AriiJID.php
/*
 * Service d'interface avec la base de données en ORM
 */
namespace Arii\JIDBundle\Service;

class AriiJID
{
    protected $portal;
    protected $doctrine;
    protected $date;
    protected $tools;

    public function __construct (
            \Arii\CoreBundle\Service\AriiPortal $portal,
            $doctrine, 
            \Arii\CoreBundle\Service\AriiDate $date,
            \Arii\CoreBundle\Service\AriiTools $tools ) {

        $this->portal = $portal;
        $this->date = $date;
        $this->tools = $tools;
        $this->doctrine = $doctrine;
    }
    
    public function getOrderLog($history_id, $db='', $output='html') {
        $em = $this->doctrine->getManager($db);        
        $OrderHistory = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->find( $history_id );
        
        if (!$OrderHistory)
            return;
        
        if (!$OrderHistory->getLog()) 
            return;

        $archive = stream_get_contents($OrderHistory->getLog());
        $log = gzinflate ( substr( $archive, 10, -8) );
        if ($output=='html')
            return $this->formatLog( $log ); 
        else
            return $log;
    }

    public function getOrderHistory( $spooler, $job_chain, $order_id, $db='', $output='xml') {
        $em = $this->doctrine->getManager($db);        
        $OrderHistory = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->findBy( [
                'spoolerId'  => $spooler,
                'jobChain' => $job_chain,
                'orderId' => $order_id 
            ] );
        
        if (!$OrderHistory)
            throw new \Exception("No history");
        
        return [
            'ID' => $OrderHistory->getId(),
            'SPOOLER_ID' => $OrderHistory->getSpoolerId(),
            'JOB_CHAIN' => $OrderHistory->getJobChain(),
            'START_TIME' => $OrderHistory->getStartTime(),
            'END_TIME' => $OrderHistory->getEndTime(),
            'STATE' => $OrderHistory->getState()
        ];
    }

    // Requetes sans jointure pour accélerer l'affichage
    public function getOrderParameters($em,$id) {
      
    }
    
    public function getTaskLog($history_id, $db='', $output='html') {
        $em = $this->doctrine->getManager($db);        
        $History = $em->getRepository("AriiJIDBundle:SchedulerHistory")->find( $history_id );
        if (!$History)
            throw new \Exception("Task id $history_id");

        $archive = stream_get_contents($History->getLog());
        $log = gzinflate ( substr( $archive, 10, -8) );
        if ($output=='html')
            return $this->formatLog( $log ); 
        else
            return $log;
    }
    
    public function formatLog($log, $output='html') {
        $Log = [];
        foreach (explode("\n",$log) as $line) {
            array_push($Log, "$line\n" );
        }
        return '<pre>'.implode('',$Log).'</pre>';
    }
}

