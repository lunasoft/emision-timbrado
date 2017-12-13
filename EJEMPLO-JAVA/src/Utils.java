

import assets.Sign;
import org.apache.commons.ssl.Base64;
import org.w3c.dom.Document;
import org.xml.sax.InputSource;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import java.io.*;
import java.text.Normalizer;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Random;
import java.util.UUID;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import static javax.xml.parsers.DocumentBuilderFactory.newInstance;

/**
 * Created by asalvio on 16/02/2017.
 */
public class Utils {


    public boolean getRandomBoolean() {
        Random random = new Random();
        return random.nextBoolean();
    }




    public String changeDate(String xml) {
        SimpleDateFormat date = new SimpleDateFormat("yyyy-MM-dd");
        SimpleDateFormat time = new SimpleDateFormat("HH:mm:ss");
        String datetime;
        datetime = date.format(new Date())+"T"+time.format(new Date());

        DocumentBuilderFactory factory = newInstance();
        DocumentBuilder builder;
        TransformerFactory tf = TransformerFactory.newInstance();
        Transformer transformer;

        try
    {       UUID uuid = UUID.randomUUID();
            String randomUUIDString = uuid.toString().replace("-","");
            long unixTime = System.currentTimeMillis() / 1000L;
            builder = factory.newDocumentBuilder();
            Document doc = builder.parse( new InputSource( new StringReader( xml ) ) );
            doc.getDocumentElement().setAttribute("Fecha",datetime);
            if(getRandomBoolean()){
                doc.getDocumentElement().setAttribute("Folio",unixTime+"k");
            }else{
                doc.getDocumentElement().setAttribute("Folio",randomUUIDString+"k");
            }

            transformer = tf.newTransformer();
            StringWriter writer = new StringWriter();
            transformer.transform(new DOMSource(doc), new StreamResult(writer));
            String output = writer.getBuffer().toString();

            return output;
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }

    }

    public  String signXML(String xml){
        Sign si = new Sign();
        try{
            String xmlDateChanged = changeDate(xml);
            String cadena = si.getCadena(xmlDateChanged);
            String sello = si.getSign(cadena,"12345678a");

            return putsSign(xmlDateChanged,sello);

        }catch (Exception e){

        }
        return null;
    }

    public  String putsSign(String xml, String sello){

        try{


            DocumentBuilderFactory factory = newInstance();
            DocumentBuilder builder;
            TransformerFactory tf = TransformerFactory.newInstance();
            Transformer transformer;

            builder = factory.newDocumentBuilder();
            Document doc = builder.parse( new InputSource( new StringReader( xml ) ) );
            doc.getDocumentElement().setAttribute("Sello",sello);
            transformer = tf.newTransformer();
            StringWriter writer = new StringWriter();
            transformer.transform(new DOMSource(doc), new StreamResult(writer));
            String output = writer.getBuffer().toString();


            return  output;

        }catch (Exception e){

        }

        return null;
    }






    }
