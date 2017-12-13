<?php

$file = file_get_contents('../sellado/recursos/LAN7008173R5.pem');
$Certificado = openssl_x509_parse($file);


//echo json_encode($Certificado);
$xml = file_get_contents('basico.xml');

try{
    $xml_doc = new DOMDocument();
    $xml_doc->loadXML($xml);
    $c = $xml_doc->getElementsByTagName('Emisor');
    var_dump($c[0]);
   // $c->setAttribute('Rfc', $rfc);
    //$c->setAttribute('Nombre', $name);
    //return $xml_doc->saveXML();
   }catch(Exception $e){
        echo var_dump($c);
   }

   

?>