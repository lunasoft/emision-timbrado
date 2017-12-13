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
function sellaCFDI($xml_string, $cadena){
    try{
        
    $xml_doc = new DOMDocument();
    $xml_doc->loadXML($xml_string);

    $private = openssl_pkey_get_private(file_get_contents('../sellado/key.pem'));
   
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

function insertarDatosCertificado($xml, $noCertificado, $base64cer){
    
    $xml_doc = new DOMDocument();
    $xml_doc->loadXML($xml);
    $c = $xml_doc->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0);
    $c->setAttribute('Certificado', $base64cer);
    $c->setAttribute('NoCertificado', $noCertificado);
    return $xml_doc->saveXML();
}

function insertaDatosEmisor($rfc, $name, $xml){
   try{
    $xml_doc = new DOMDocument();
    $xml_doc->loadXML($xml);
    $c = $xml_doc->getElementsByTagName('Emisor')[0];
    $c->setAttribute('Rfc', $rfc);
    $c->setAttribute('Nombre', $name);
    return $xml_doc->saveXML();
   }catch(Exception $e){
        echo var_dump($c);
   }
}


//leemos archivo xml
$xml = file_get_contents('basico.xml');

//obtenemos numero de certificado
$file = file_get_contents('../sellado/recursos/LAN7008173R5.pem');
$Certificado = openssl_x509_parse($file);
$res = shell_exec('openssl x509 -in ../sellado/recursos/LAN7008173R5.pem -serial -noout');
$parse_res = str_replace('serial=','',$res);
$serialNumber = '';
for($i=1;$i<strlen($parse_res);$i=$i+2){
    $serialNumber = $serialNumber.''.$parse_res[$i];
}

//obtenemos rfc y razon social del csd
$rfc = split('[/.-]', $Certificado['subject']['x500UniqueIdentifier'])[0];
$razon = $Certificado['subject']['name'];

$xml_con_datos_emisor = insertaDatosEmisor($rfc,$razon,$xml);

//convertimos certificado a base64
$b4cer = shell_exec('openssl enc -in ../sellado/recursos/LAN7008173R5.cer -a -A');

//CFDI FINAL
$cfdi_final = insertarDatosCertificado($xml_con_datos_emisor,$serialNumber,$b4cer);

//obtenemos cadena original
$cadena = cadenaOriginal($cfdi_final);

//sellamos cadena original
$sellado = sellaCFDI($cfdi_final,$cadena);



header('Content-type: text/plain');
require_once 'vendor/autoload.php';
use SWServices\Stamp\StampService as StampService;
 try{
    header('Content-type: application/json');

    $params = array(
        "url"=>"http://services.test.sw.com.mx",
        "user"=>"demo",
        "password"=> "123456789"
        );
    
    $stamp = StampService::Set($params);
    $result = $stamp::StampV1($sellado);
    if($result->status != "success"){
        echo json_encode(array('cadena'=>$cadena,'final'=>$cfdi_final,'noCertificado'=>$serialNumber,'sello'=>$sellado,'b64cer'=>$b4cer,"error"=>$result));
    }else{
        echo json_encode($result);
    }
    
    

}
catch(Exception $e){
    header('Content-type: text/plain');
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}


?>