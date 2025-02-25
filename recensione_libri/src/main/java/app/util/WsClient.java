package app.util;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.net.http.HttpResponse.BodyHandlers;

import app.errors.WsException;
import app.model.xml.ListaLibri;

public class WsClient {
	private String baseUrl;
	private HttpClient client;

	public WsClient(String baseUrl) {
		this.baseUrl = baseUrl;
		this.client = HttpClient.newHttpClient();
	}
	
	// TODO Funzione per Login e Autenticazione con Token
	
	public String getListaLibri() throws Exception { // TODO Autenticazione con Token | Classi Libro e ListaLibri provvisorie per ora
		URI uri = new URI(this.baseUrl + "/list_books");
		HttpRequest req = HttpRequest.newBuilder().uri(uri).header("Accept", "application/xml").GET().build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());

		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode());

		//String body = (String) res.body();
		//return XmlUtils.unmarshal(ListaLibri.class, body); //Provvisorio
		return res.body();
	}

}
