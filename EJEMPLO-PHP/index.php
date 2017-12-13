<?php


//funcion para obtener la cadena original
function cadenaOriginal($xml){
    try{
        $xml_doc = new DOMDocument();
        $xml_doc->loadXML($xml);
        // XSLT
        $xsl_doc = new DOMDocument();
        $xsl_doc->load("../cadena_original/recursos/cadenaoriginal_3_3.xslt");

        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl_doc);
        $newdom = $proc->transformToDoc($xml_doc);
        $c = $newdom->saveXML();
        $c = str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$c);
        $c = eregi_replace("[\n\r]", '', $c);
        return $c;

    }
    catch(Exception $e){
        header("HTTP/1.0 500");
        die($e->getMessage());
    }
    
}


//funcion para sellar 
function signXML($xml_string, $cadena){
    try{
        
    $xml_doc = new DOMDocument();
    $xml_doc->loadXML($xml_string);

    $private = openssl_pkey_get_private(file_get_contents('key.pem'));
   
    openssl_sign($cadena, $sig, $private, OPENSSL_ALGO_SHA256);
    $sello = base64_encode($sig);

    $c = $xml_doc->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0); 
    $c->setAttribute('Sello', $sello);
    
    
    return $xml_doc->saveXML();
    }catch(Exception $e){
        header("HTTP/1.0 500");
        die($e->getMessage());
    }

}

function insertarDatosCertificado($xml, $noCertificado){
    $certificado = str_replace(array('\n', '\r'), '', base64_encode(file_get_contents('cer.cer')));
    $xml_doc = new DOMDocument();
    $xml_doc->loadXML($xml);
    $c = $xml_doc->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0);
}


//leemos archivo xml
$xml = file_get_contents('basico.xml');

//obtenemos cadena original
$cadena = cadenaOriginal($xml);



?>