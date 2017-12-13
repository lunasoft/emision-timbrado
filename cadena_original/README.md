Pasos para generar cadena original
===================




Transformando .cer a .pem
-------------------------

*openssl x509 -inform der -in ../sellado/recursos/LAN7008173R5.cer -out ../sellado/recursos/LAN7008173R5.pem

Obteniendo numero de certificado
-------------------------------

*openssl x509 -in ../sellado/recursos/LAN7008173R5.pem -serial -noout

*node tools/serial_getter -s [numero certificado]


Obtener datos del subject certificado
-------------------------------------

*openssl x509 -in ../sellado/recursos/LAN7008173R5.pem -text


Convirtiendo .cer a base64
--------------------------

*openssl enc -in ../sellado/recursos/LAN7008173R5.cer -a -A -out certB64.txt


Transformando xml
-------------
*xslt basico.xml recursos/cadenaoriginal_3_3.xslt ../sellado/cadena_original.txt

