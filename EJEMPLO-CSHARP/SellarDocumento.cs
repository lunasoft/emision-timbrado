using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Security.Cryptography;
using System.Security.Cryptography.X509Certificates;
using System.Text;
using System.Xml;

namespace EJEMPLO_CSHARP
{
    public class SellarDocumento
    {
        public static string ObtenerSello(byte[] certificatePfx, string password, string cadenaOriginal)
        {
            //Sellamos la factura con el CSD y la cadena original aplicando el algoritmo SHA256
            var signData = string.Empty;
            RSACryptoServiceProvider rsa = default(RSACryptoServiceProvider);
            byte[] signatureBytes = default(byte[]);
            X509Certificate2 certX509 = new X509Certificate2(certificatePfx, password
                 , X509KeyStorageFlags.MachineKeySet | X509KeyStorageFlags.Exportable);

            rsa = new RSACryptoServiceProvider();
            rsa.FromXmlString(certX509.PrivateKey.ToXmlString(true));

            byte[] data = Encoding.UTF8.GetBytes(cadenaOriginal);

            signatureBytes = rsa.SignData(data, CryptoConfig.MapNameToOID("SHA256"));
            return Convert.ToBase64String(signatureBytes);

        }
        public static string ObtenerNumeroCertificado(X509Certificate2 cert)
        {
            string hexadecimalString = cert.SerialNumber;
            StringBuilder sb = new StringBuilder();
            for (int i = 0; i <= hexadecimalString.Length - 2; i += 2)
            {
                sb.Append(Convert.ToString(Convert.ToChar(Int32.Parse(hexadecimalString.Substring(i, 2), System.Globalization.NumberStyles.HexNumber))));
            }
            return sb.ToString();
        }

    }
}
