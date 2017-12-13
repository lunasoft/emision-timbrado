using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text;
using System.Xml;
using System.Xml.Xsl;

namespace EJEMPLO_CSHARP
{
    public class ObtenerCadenaOriginal
    {
        public static string CadenaOriginalCFDIv33(string strXml,string path_xslt)
        {
            try
            {
                var xslt_cadenaoriginal_3_3 = new XslCompiledTransform();                
                xslt_cadenaoriginal_3_3.Load(path_xslt);
                string resultado = null;
                StringWriter writer = new StringWriter();
                XmlReader xml = XmlReader.Create(new StringReader(strXml));
                xslt_cadenaoriginal_3_3.Transform(xml, null, writer);
                resultado = writer.ToString().Trim();
                writer.Close();

                return resultado;
            }
            catch (Exception ex)
            {

                throw new Exception("El XML proporcionado no es válido.", ex);
            }


        }
    }
}
