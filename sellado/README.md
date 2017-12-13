Pasos para sellar cadena original
===================




----------


Generación archivo pem
-------------

*openssl pkcs8 -inform DER -in recursos/LAN7008173R5.key -out key.pem -passin pass:12345678a



Firmando cadena
-------------

*openssl dgst -sha256 -out signature -sign key.pem cadena_original.txt

*openssl enc -in signature -a -A -out signB64.txt


Verificar firma
-------------------

*openssl x509 -in recursos/LAN7008173R5.pem -pubkey -noout > LAN7008173R5.pub
*openssl dgst -sha256  -verify  LAN7008173R5.pub -signature signature cadena_original.txt


