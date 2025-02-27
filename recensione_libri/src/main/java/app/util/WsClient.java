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
import app.model.xml.Recensioni;

public class WsClient {
	private String baseUrl;
	private HttpClient client;

	public WsClient(String baseUrl) {
		this.baseUrl = baseUrl;
		this.client = HttpClient.newHttpClient();
	}
	
	// TODO Funzione per Login e Autenticazione con Token
	
	public String pubblicaRecensione(String tipoDato,String authToken, int idLibro, Float voto, String commento) throws Exception { //Funzione che invia una richiesta in POST per scrivere una recensione
		URI uri = new URI(this.baseUrl + "/create_recensione");
		
		String richiesta = "";
		
		if (tipoDato.equals("application/xml")) {
			richiesta = "<recensione>"
					+ "<id_libro>"+idLibro+"</id_libro>"
					+ "<voto>"+voto+"</voto>"
					+ "<commento>"+commento+"</commento>"
					+ "</recensione>";
		}else if (tipoDato.equals("application/json")) {
			richiesta = "{\"id_libro\":"+idLibro+",\"voto\":"+voto+",\"commento\":\""+commento+"\"}";
		}
		
		
		HttpRequest req = HttpRequest.newBuilder().uri(uri).POST(HttpRequest.BodyPublishers.ofString(richiesta)).header("Accept", tipoDato).header("Content-Type", tipoDato).header("Auth-Token", authToken).build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());
		
		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		String body = (String) res.body();

		return body;
	}
	
	public String updateRecensione(String tipoDato, String authToken, int idRecensione, Float voto, String commento) throws Exception { //Funzione che invia una richiesta in PUT per modificare una recensione
		URI uri = new URI(this.baseUrl + "/update_recensione");
		
		String richiesta = "";

		if (tipoDato == "application/xml") {
			String elementoVoto = (voto == null) ? "<voto></voto>" : "<voto>"+voto+"</voto>";
			richiesta = "<recensione>"
					+ "<id_recensione>"+idRecensione+"</id_recensione>"
					+ elementoVoto
					+ "<commento>"+commento+"</commento>"
					+ "</recensione>";
		}else if (tipoDato == "application/json") {
			String elementoVoto = (voto == null) ? "null" : voto.toString();
			richiesta = "{\"id_recensione\":"+idRecensione+",\"voto\":"+elementoVoto+",\"commento\":\""+commento+"\"}";
		}
		
		HttpRequest req = HttpRequest.newBuilder().uri(uri).PUT(HttpRequest.BodyPublishers.ofString(richiesta)).header("Accept", tipoDato).header("Content-Type", tipoDato).header("Auth-Token", authToken).build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());
		
		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		String body = (String) res.body();

		return body;
	}
	
	public String deleteRecensione(String tipoDato, String authToken, int idRecensione) throws Exception {
		URI uri = new URI(this.baseUrl + "/delete_recensione?id_recensione="+idRecensione+"");
		
		HttpRequest req = HttpRequest.newBuilder().uri(uri).DELETE().header("Accept", tipoDato).header("Auth-Token", authToken).build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());
		
		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		String body = (String) res.body();

		return body;
	}
	
	public Recensioni getRecensioni(String tipoDato, String authToken) throws Exception { //Funzione che invia una richiesta in GET per visulizzare la lista dei libri
		URI uri = new URI(this.baseUrl + "/list_user_reviews");
		HttpRequest req = HttpRequest.newBuilder().uri(uri).header("Accept", tipoDato).header("Auth-Token", authToken).GET().build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());

		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		Recensioni recensioni = XmlUtils.unmarshal(Recensioni.class, res.body());

		return recensioni;
	}
	
	public ListaLibri getListaLibri(String tipoDato, String authToken) throws Exception { //Funzione che invia una richiesta in GET per visulizzare la lista dei libri
		URI uri = new URI(this.baseUrl + "/list_books");
		HttpRequest req = HttpRequest.newBuilder().uri(uri).header("Accept", tipoDato).header("Auth-Token", authToken).GET().build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());

		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		ListaLibri lista = XmlUtils.unmarshal(ListaLibri.class, res.body());
		
		return lista;
	}
	
}
