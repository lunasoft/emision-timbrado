using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Security.Cryptography.X509Certificates;
using System.Text;
using System.Xml;

namespace EJEMPLO_CSHARP
{
    class Program
    {
        static void Main(string[] args)
        {
            //Obtenemos el xml a sellar.
            Console.WriteLine(@"Obtenemos el XML a Sellar: basico.xml" + Environment.NewLine);
            var xml = Encoding.UTF8.GetString(File.ReadAllBytes(@"Recursos\basico.xml"));
            //Removemos los caracteres invalidos
            xml = Utilerias.RemoverCaracteresInvalidosXml(xml);
            //Obtenemos el PFX 
            Console.WriteLine(@"Obtenemos el PFX: Recursos\LAN7008173R5.pfx" + Environment.NewLine);
            var pfx = File.ReadAllBytes(@"Recursos\LAN7008173R5.pfx");
            var contraseña_pfx = "12345678a";
            //Leemos el PFX 
            X509Certificate2 x509Certificate = new X509Certificate2(pfx, contraseña_pfx
                , X509KeyStorageFlags.MachineKeySet | X509KeyStorageFlags.Exportable);

            //Cambiamos el valor del Certificiado y NoCertificado en el XML previo al sellado de la factura.
            Console.WriteLine(@"Cambiamos el NoCertificado y Certificiado el XML a Sellar: basico.xml" + Environment.NewLine);
            XmlDocument doc = new XmlDocument();
            doc.LoadXml(xml);
            doc.DocumentElement.SetAttribute("NoCertificado", SellarDocumento.ObtenerNumeroCertificado(x509Certificate));
            doc.DocumentElement.SetAttribute("Certificado", Convert.ToBase64String(x509Certificate.GetRawCertData()));
            //Obtenemos el string xml de la factura para obtener la cadena original de la factura ( xml )
            using (MemoryStream ms = new MemoryStream())
            {
                doc.Save(ms);
                ms.Seek(0, SeekOrigin.Begin);
                xml = Utilerias.RemoverCaracteresInvalidosXml(Encoding.UTF8.GetString(ms.ToArray()));
            }
            Console.WriteLine(@"Obtenemos la cadena original de la factura xml" + Environment.NewLine);
            //Obtenemos la cadena original de la factura ( xml )
            var cadena_original = ObtenerCadenaOriginal.CadenaOriginalCFDIv33(xml, @"Recursos\xslt\cadenaoriginal_3_3.xslt");
            Console.WriteLine(cadena_original + Environment.NewLine);
            //Obtenemos el sello de la factura
            Console.WriteLine(@"Obtenemos el sello de la factura xml utilizando la cadena original." + Environment.NewLine);
            var sello_factura = SellarDocumento.ObtenerSello(pfx, contraseña_pfx, cadena_original);
            Console.WriteLine(sello_factura + Environment.NewLine);
            //Cambiamos el valor del sello en el xml 
            doc = new XmlDocument();
            doc.LoadXml(xml);
            doc.DocumentElement.SetAttribute("Sello", sello_factura);
            //Obtenemos el string xml de la factura con el valor del sello
            using (MemoryStream ms = new MemoryStream())
            {
                doc.Save(ms);
                ms.Seek(0, SeekOrigin.Begin);
                xml = Utilerias.RemoverCaracteresInvalidosXml(Encoding.UTF8.GetString(ms.ToArray()));
            }
            Console.WriteLine("Factura sellada: " + Environment.NewLine);
            Console.WriteLine(xml + Environment.NewLine);
            Console.WriteLine("presione cualquier tecla para terminar...");
            Console.ReadLine();
        }
    }
}
