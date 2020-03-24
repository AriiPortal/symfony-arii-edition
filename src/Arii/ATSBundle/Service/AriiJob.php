<?php
namespace Arii\ATSBundle\Service;

class AriiJob
{
    protected $em;
    protected $db;
    protected $Instances;
    
    public function __construct (
            \Doctrine\ORM\EntityManager $entityManager,
            \Arii\ATSBundle\Service\AriiJobDB $db,
            $Instances) {
        
        $this->em = $entityManager;
        $this->db = $db;
        $this->Instances = $Instances;
    }

    // Transformation des données utilisateur en éléments de DB
    public function getEmByInstance($instanceId) {  
        $instance = strtoupper($instanceId);
        $repoId = $this->Instances[$instance];  
        return $this->em->getDoctrine()->resetEntityManager($repoId);        
    }

/*********************************************************************
 * Documentation
 *********************************************************************/
    // Les jobs d'une boite
    public function Doc($instanceId,$jobName,$Filter) {     
        list($em,$jobId) = $this->db->getJobDB($instanceId,$jobName);
        return $this->db->Status($em,$jobId,$Filter);
    }    

/*********************************************************************
 * Simplification
 *********************************************************************/

    // Les jobs d'une boite
    public function Status($instanceId,$jobName,$Filter) {     
        list($em,$jobId) = $this->db->getJobDB($instanceId,$jobName);
        return $this->db->Status($em,$jobId,$Filter);
    }
        
    public function Runs($instanceId,$jobName,$Filter) {         
        $em = $this->getEmByInstance($instanceId);
        $jobId = $this->db->getJobIdByName($em,$jobName);
        return $this->db->Runs($em,$jobId,$Filter);
    }
    
    public function Audit($instanceId,$jobName,$Filter) {      
        list($em,$jobId) = $this->db->getJobDB($instanceId,$jobName);
        return $this->db->Audit($em,$jobId,$Filter);
    }
    
    
}
