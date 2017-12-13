Pasos para sellar cadena original
===================




----------


Generación archivo pem
-------------
*openssl pkcs8 -inform DET -in recursos/LAN7008173R5.key -passin pass:12345678a -out key.pem



Firmando cadena
-------------
*openssl dgst -sha256 -out sello.bin -sign key.pem cadena_original.txt

*openssl enc -in sello.bin -a -A -out cadena_sellada.txt

Obteniendo numero de certificado
-------------------------------

*openssl x509 -in recursos/LAN7008173R5.pem -serial -noout

*node tools/serial_getter -s <numero certificado>

Convirtiendo .cer a base64
--------------------------

*openssl enc -in recursos/LAN7008173R5.cer -a -A -out certB64.txt