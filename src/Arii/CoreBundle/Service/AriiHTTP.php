<?php
/*
 * Gere les parametres passés en http (Utile pour REST)
 */
namespace Arii\CoreBundle\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\AcceptHeader;

class AriiHTTP
{
    public function __construct () {
    }
    
    public function Accept($request) {
        $acceptHeader = AcceptHeader::fromString($request->headers->get('Accept'));
        $SimpleMap= [
            'application/json' => 'json',
            'application/xml'  => 'xml',
            'image/png'        => 'png',
            'text/csv'         => 'csv',
            'text/html'        => 'html'
        ];
        $Accept = [];
        foreach ($SimpleMap as $k=>$v) {
            if ($acceptHeader->has($k)) {
                $Accept['outputFormat'] = $v;
                break;
            }
        }
        return $Accept;
    }

    # Filtre pour mapping REST->DB
    public function Filter($request) {
        $Mapping = [
            'environment' => 'env'
        ];
        $Filter = [];
        foreach ($request->query->all() as $k=>$v) {       
            if ($request->get($k))
                if (isset($Mapping[$k]))
                    $val = $Mapping[$k];
                else 
                    $val = $k;
                
            $Filter[$k] = $request->get($k);
        }
        return $Filter;
    }
    
}
