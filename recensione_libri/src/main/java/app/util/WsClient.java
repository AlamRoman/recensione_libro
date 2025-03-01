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
import java.util.List;
import java.util.Map;
import java.lang.reflect.Type;

import app.errors.WsException;
import app.model.xml.Libro;
import app.model.xml.ListaLibri;
import app.model.xml.Recensione;
import app.model.xml.Recensioni;
import app.model.xml.RegistrationResponse;
import app.model.xml.Response;

import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

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
		
		
		HttpRequest req = HttpRequest.newBuilder().uri(uri).POST(HttpRequest.BodyPublishers.ofString(richiesta)).header("Content-Type", tipoDato).header("Auth-Token", authToken).build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());
		
		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		//String body = (String) res.body();
		
		
		return "Recensione creata con successo";
	}
	
	public String registerUser(String tipoDato,String authToken, String username, String nome, String cognome) throws Exception { //Funzione che invia una richiesta in POST per scrivere una recensione
		URI uri = new URI(this.baseUrl + "/register");
		
		String richiesta = "";
		
		if (tipoDato.equals("application/xml")) {
			richiesta = "<recensione>"
					+ "<username>"+username+"</username>"
					+ "<nome>"+nome+"</nome>"
					+ "<cognome>"+cognome+"</cognome>"
					+ "</recensione>";
		}else if (tipoDato.equals("application/json")) {
			richiesta = "{\"username\":\"" + username + "\","
		              + "\"nome\":\"" + nome + "\","
		              + "\"cognome\":\"" + cognome + "\"}";
		}
		
		
		HttpRequest req = HttpRequest.newBuilder().uri(uri).POST(HttpRequest.BodyPublishers.ofString(richiesta)).header("Content-Type", tipoDato).header("Auth-Token", authToken).build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());
		
		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		String responseBody = res.body();
		String token;

		if (tipoDato.equals("application/json")) {
		    Gson gson = new Gson();
		    RegistrationResponse response = gson.fromJson(responseBody, RegistrationResponse.class);
		    token = response.getToken();
		} else {
		    RegistrationResponse response = XmlUtils.unmarshal(RegistrationResponse.class, responseBody);
		    token = response.getToken();
		}
		
		return "Utente registrato, salva il tuo token : " + token;
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
		
		HttpRequest req = HttpRequest.newBuilder().uri(uri).PUT(HttpRequest.BodyPublishers.ofString(richiesta)).header("Content-Type", tipoDato).header("Auth-Token", authToken).build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());
		
		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		//String body = (String) res.body();

		return "Recensione modificata con successo";
	}
	
	public String deleteRecensione(String tipoDato, String authToken, int idRecensione) throws Exception {
		URI uri = new URI(this.baseUrl + "/delete_recensione?id_recensione="+idRecensione+"");
		
		HttpRequest req = HttpRequest.newBuilder().uri(uri).DELETE().header("Content-Type", tipoDato).header("Auth-Token", authToken).build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());
		
		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		//String body = (String) res.body();

		return "Recensione cancellata con successo";
	}
	
	public Recensioni getMieRecensioni(String tipoDato, String authToken) throws Exception { //Funzione che invia una richiesta in GET per visulizzare la lista dei libri
		URI uri = new URI(this.baseUrl + "/list_user_reviews");
		HttpRequest req = HttpRequest.newBuilder().uri(uri).header("Content-Type", tipoDato).header("Auth-Token", authToken).GET().build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());

		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		if (tipoDato.equals("application/json")) {
		    Gson gson = new Gson();
		    Type listType = new TypeToken<List<Recensione>>() {}.getType();
		    List<Recensione> lista = gson.fromJson(res.body(), listType);
		    return new Recensioni(lista);
		} else {
		    return XmlUtils.unmarshal(Recensioni.class, res.body());
		}

	}
	
	public Recensioni getRecensioniPerLibro(int id_libro, String content_type, String authToken) throws Exception { //Funzione che invia una richiesta in GET per visulizzare la lista dei libri
		URI uri = new URI(this.baseUrl + "/list_reviews_by_book?id_libro="+id_libro);
		HttpRequest req = HttpRequest.newBuilder().uri(uri).header("Content-Type", content_type).header("Auth-Token", authToken).GET().build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());

		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());

		if (content_type.equals("application/json")) {
		    Gson gson = new Gson();
		    Type listType = new TypeToken<List<Recensione>>() {}.getType();
		    List<Recensione> lista = gson.fromJson(res.body(), listType);
		    return new Recensioni(lista);
		} else {
		    return XmlUtils.unmarshal(Recensioni.class, res.body());
		}

	}
	
	public ListaLibri getListaLibri(String tipoDato, String authToken) throws Exception { //Funzione che invia una richiesta in GET per visulizzare la lista dei libri
		URI uri = new URI(this.baseUrl + "/list_books");
		HttpRequest req = HttpRequest.newBuilder().uri(uri).header("Content-Type", tipoDato).header("Auth-Token", authToken).GET().build();
		HttpResponse<String> res = this.client.send(req, BodyHandlers.ofString());

		if (res.statusCode() != 200)
			throw new WsException("HTTP status code: " + res.statusCode() + "\n"+ res.body());
		
		if (tipoDato.equals("application/json")) {
			Gson gson = new Gson();
			
			Type listType = new TypeToken<List<Libro>>() {}.getType();
			List<Libro> lista = gson.fromJson(res.body(), listType);
			return new ListaLibri(lista);

	    } else {
	        return XmlUtils.unmarshal(ListaLibri.class, res.body());
	    }
	}
	
	
	public boolean validateToken(String token, String contentType) throws Exception {
	    URI uri = new URI(baseUrl + "/validate_token?token=" + token);
	    HttpRequest request = HttpRequest.newBuilder()
	            .uri(uri)
	            .header("Content-Type", contentType)
	            .GET()
	            .build();

	    HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());
	    String responseBody = response.body();

	    if (response.statusCode() != 200) {
	        System.out.println("API Error: " + responseBody);
	        return false;
	    }

	    if ("application/json".equals(contentType)) {
	        Gson gson = new Gson();
	        Map<String, String> responseData = gson.fromJson(responseBody, Map.class);
	        return "success".equals(responseData.get("status"));
	    } else {
	        return responseBody.contains("<status>success</status>");
	    }
	}
}
