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
            'image/svg+xml'    => 'svg',
            'image/png'        => 'png',
            'image/jpg'        => 'jpg',
            'image/jpeg'       => 'jpg',
            'text/csv'         => 'csv',
            'text/html'        => 'html',
            'text/plain'       => 'txt',
            'text/calendar'    => 'ics',
            'text/vnd.graphviz'=> 'dot',
            'text/cmap'        => 'cmap'
        ];
        $Accept = [];
        foreach ($SimpleMap as $k=>$v) {
            if ($acceptHeader->has($k)) {
                $Accept['outputFormat'] = $v;
                break;
            }
        }
        $language = AcceptHeader::fromString($request->headers->get('Accept-Language'));
        switch(strtolower($language)) {
            case 'fr':
                $Accept['language'] = 'fr_FR';
                break;
            default: 
                $Accept['language'] = 'en_EN';
                break;
        }
        return $Accept;
    }

    # Filtre pour mapping REST->DB
    public function Filter($request) {
        // Utile ?!
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

    # jsonData
    public function jsonData($request) {
        $Parameters = [];
        if (( "POST" === $request->getMethod()) or ( "PUT" === $request->getMethod())) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')){
                $Parameters = json_decode($request->getContent(), true);
            }
        }
        return $Parameters;
    }
    
}
