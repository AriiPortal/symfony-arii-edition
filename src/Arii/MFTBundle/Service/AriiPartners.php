<?php
// src/Arii/JIDBundle/Service/AriiHistory.php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\MFTBundle\Service;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AriiPartners
{
    public function __construct (  
            \Arii\CoreBundle\Service\AriiPortal $portal ) {
    }

/*********************************************************************
 * Partners
 *********************************************************************/    
    public function PartnerIdbyName($em,$partnerId) {
      
        if (is_numeric($partnerId))
            return $partnerId;
        
        // On complete avec les ordres stockés
        $Partner = $em->getRepository("AriiMFTBundle:Partners")->findOneBy([ 'name' => $partnerId]);
        if (!$Partner)
            throw new HttpException(404, "Partner '$partnerId' unknown!");
        return $Partner->getId(); 
    }    
    
    public function Partners($em,$Filter) {       
        $Partners = $em->getRepository("AriiMFTBundle:Partners")->findPartners();        
        return $Partners;
    }

    public function Partner($em,$partnerId,$Filter) { 

        $Partner = $em->getRepository("AriiMFTBundle:Partners")->find($this->PartnerIdbyName($em,$partnerId));
        if (!$Partner)
            throw new \Exception('ERROR');
        $Details = [
            'id'    => $Partner->getId(),
            'name'  => $Partner->getName(),
            'title'  => $Partner->getTitle(),
            'description' => $Partner->getDescription(),
            'transfers' => $this->PartnerTransfers($em,$partnerId,$Filter)
        ];
        return $Details;
    }
    
    // Les transferts d'un partenaire
    public function PartnerTransfers($em,$partnerId,$Filter=[]) {
        $Transfers = $em->getRepository("AriiMFTBundle:Transfers")->findTransfersByPartner($this->PartnerIdbyName($em,$partnerId),$Filter);
        foreach ($Transfers as $k=>$Transfer) {
            $Transfers[$k]['operations'] = $this->TransferOperations($em,$Transfer['id']);
        }
        return $Transfers;
    }

    public function partnerDeliveries($em,$partnerId,$Filter=[]) {
        $Deliveries = $em->getRepository("AriiMFTBundle:Deliveries")->findDeliveriesByPartner($this->PartnerIdbyName($em,$partnerId),$Filter);
        return $Deliveries;
    }
        
    // les operations d'un transfert
    public function TransferOperations($em,$transferId,$Filter=[]) {
       
        $Operations = $em->getRepository("AriiMFTBundle:Operations")->findOperationsByTransfer($transferId);
        # On complete en conservant les connections (ce sont des allers et retours)
        $Connections = [];
        foreach ($Operations as $k=>$Operation) {
            $sourceId = $Operation['source_id'];
            if (isset($Connections[$sourceId]))
                $Source = $Connections[$sourceId];
            else 
                $Source = $em->getRepository("AriiCoreBundle:Connection")->find($sourceId);
            $Operations[$k]['source'] = $this->getConnectionInfo($Source);
            $Connections[$sourceId] = $Source;
            
            $targetId = $Operation['target_id'];
            if (isset($Connections[$targetId]))
                $Target = $Connections[$targetId];
            else 
                $Target = $em->getRepository("AriiCoreBundle:Connection")->find($targetId);
            $Operations[$k]['target'] = $this->getConnectionInfo($Target);
            $Connections[$targetId] = $Target;
            
            // Parametres
            $Parameters = $em->getRepository("AriiMFTBundle:Parameters")->find($Operation['parameters_id']);
            $Operations[$k]['parameters'] = [
                'name' => $Parameters->GetName(),
                'title' =>  $Parameters->GetTitle(),
                'description' =>  $Parameters->GetDescription(),
                'recursive' =>  $Parameters->GetRecursive(),
                'error_when_no_files' =>  $Parameters->GetErrorWhenNoFiles(),
                'overwrite_files' =>  $Parameters->GetOverwriteFiles(),
                'append_files' =>  $Parameters->GetAppendFiles(),
                'transactional' =>  $Parameters->GetTransactional(),
                'atomic_prefix' =>  $Parameters->GetAtomicPrefix(),
                'atomic_suffix' =>  $Parameters->GetAtomicSuffix(),
                'concurrent_transfer' =>  $Parameters->GetConcurrentTransfer(),
                'max_concurrent_transfers' =>  $Parameters->GetMaxConcurrentTransfers(),
                'zero_byte_transfer' =>  $Parameters->GetZeroByteTransfer(),
                'force_files' =>  $Parameters->GetForceFiles(),
                'remove_files' =>  $Parameters->GetRemoveFiles(),
                'compress_files' =>  $Parameters->GetCompressFiles(),
                'compressed_file_extension' =>  $Parameters->GetCompressedFileExtension(),
                'source_replace' =>  $Parameters->GetSourceReplace(),
                'source_replacing' =>  $Parameters->GetSourceReplacing(),
                'source_replacement' =>  $Parameters->GetSourceReplacement(),
                'target_replace' =>  $Parameters->GetTargetReplace(),
                'target_replacing' =>  $Parameters->GetTargetReplacing(),
                'target_replacement' =>  $Parameters->GetTargetReplacement(),
                'pre_command' =>  $Parameters->GetPreCommand(),
                'pre_command_str' =>  $Parameters->GetPreCommandStr(),
                'post_command' =>  $Parameters->GetPostCommand(),
                'post_command_str' =>  $Parameters->GetPostCommandStr(),
                'polling' => [],
                'mail' => []
            ];
            if ($Parameters->GetPolling()) {
                $Operations[$k]['parameters']['polling'] = [
                    'poll_interval' =>  $Parameters->GetPollInterval(),
                    'poll_timeout' =>  $Parameters->GetPollTimeout()
                ];
            }
            if ($Parameters->GetMailOnError() or $Parameters->GetMailOnSuccess() ) {
                $Operations[$k]['parameters']['mail'] = [
                    'on_error'   =>  [],
                    'on_success' =>  []
                ];
                if ($Parameters->GetMailOnError() )
                    $Operations[$k]['parameters']['mail']['on_error'] = [
                        'to'        =>  $Parameters->GetMailOnErrorTo(),
                        'subject'   =>  $Parameters->GetMailOnErrorSubject()
                    ];
                if ($Parameters->GetMailOnSuccess() )
                    $Operations[$k]['parameters']['mail']['on_success'] = [
                        'to'        =>  $Parameters->GetMailOnSuccessTo(),
                        'subject'   =>  $Parameters->GetMailOnSuccessSubject()
                    ];
            }
            
        }
        return $Operations;
   }
   
   private function getConnectionInfo($Connection) {
        $Infos = [
            'id'       => $Connection->getId(),
            'name'     => $Connection->getName(),
            'title'    => $Connection->getTitle(),
            'protocol' => $Connection->getProtocol(),
            'host'     => $Connection->getHost(),
            'port'     => $Connection->getPort()
        ];
        return $Infos;
   }
   
}
