package app.util;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.nio.charset.StandardCharsets;

import jakarta.xml.bind.JAXBContext;
import jakarta.xml.bind.JAXBException;
import jakarta.xml.bind.Marshaller;
import jakarta.xml.bind.Unmarshaller;

public class XmlUtils {

	private XmlUtils() {

	}

	public static <T> String marshal(T x) throws JAXBException {
		JAXBContext context = JAXBContext.newInstance(x.getClass());
		Marshaller marshaller = context.createMarshaller();
		marshaller.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, false);
		ByteArrayOutputStream baos = new ByteArrayOutputStream();
		marshaller.marshal(x, baos);
		String xml = baos.toString(StandardCharsets.UTF_8);
		return xml;
	}

	public static <T> T unmarshal(Class<T> klass, String xml) throws JAXBException {
		JAXBContext context = JAXBContext.newInstance(klass);
		Unmarshaller unmarshaller = context.createUnmarshaller();
		byte[] bytes = xml.getBytes(StandardCharsets.UTF_8);
		InputStream stream = new ByteArrayInputStream(bytes);
		@SuppressWarnings("unchecked")
		T obj = (T) unmarshaller.unmarshal(stream);
		return obj;
	}

}
