package app;

import java.util.Scanner;

import app.model.xml.ListaLibri;
import app.model.xml.Recensioni;
import app.util.WsClient;

public class Main {
	
	private static String content_type = "application/json";
	private static WsClient ws = new WsClient("http://localhost/web_service/recensione_libro.php"); //Cambia link in base a dove stai avviando il web service
	private static String authToken = "7123a062ef08af773b5cff8ed91081d1dcc1d75c23cf99fbf72cacc8bb0aef12"; //Test Token
	private static Scanner scanner = new Scanner(System.in);
	
	public static void main( String[] args ){
		
		boolean exit=false;
		int scelta;
		
		
		//selezione tipo di dati
		System.out.println("*** Recensione Libri ***");
		System.out.println("\nSeleziona il Content Type:");
		System.out.println("\t 1. XML (default)");
		System.out.println("\t 2. JSON");
		System.out.println("\t 3. Esci");
		System.out.print("\nInserisci scelta: ");
		
		scelta = scanner.nextInt();
		
		if (scelta == 2) {
			content_type = "application/json";
		}else if(scelta == 3) {
			
			System.out.println("\n\nProgramma terminato");
			
			return;
		}
		
		
		//login/registra
		do {
			
			System.out.println("\n*** Recensione Libri ***");
			System.out.println("\nRegistra/Login:");
			System.out.println("\t 1. Registra");
			System.out.println("\t 2. Login");
			System.out.println("\t 3. Esci");
			System.out.print("\nInserisci scelta: ");
			
			scelta = scanner.nextInt();
			
			switch (scelta) {
			case 1:
				
				registra();
				
				break;
				
			case 2:
				
				boolean ris = login();
				
				if (ris) {//token valido
					
					exit = true;
					
				}else {//token non valido
					
					System.out.println("\nError: token non valido\n");
				}
				
				break;
				
			case 3:
				
				System.out.println("\n\nProgramma terminato");
				
				return;

			default:
				
				System.out.println("\nErrore : input non valido\n");
				break;
			}
			
		} while (!exit);
		
		exit = false;
		
		//menu principale
		do {
			
			System.out.println("\n*** Recensione Libri ***");
			System.out.println("\nMenu:");
			System.out.println("\t 1. Mostra tutti i libri");
			System.out.println("\t 2. Mostra le mie recensioni");
			System.out.println("\t 3. Mostra tutte le recensioni di un libro");
			System.out.println("\t 4. Inserisci una recensione");
			System.out.println("\t 5. Modifica una recensione");
			System.out.println("\t 6. Cancella una recensione");
			System.out.println("\t 7. Esci");
			System.out.print("\nInserisci scelta: ");
			
			scelta = scanner.nextInt();
			
			switch (scelta) {
			case 1:
				
				mostraTuttiLibri();
				
				break;
				
			case 2:
				
				mostraMieRecensioni();
				
				break;
				
			case 3:
				
				mostraRecensioniPerLibro();
				
				break;
			
			case 4:
				
				inserisciRecensione();
				
				break;
				
			case 5:
				
				modificaRecensione();
				
				break;
				
			case 6:
				
				cancellaUnaRecensione();
				
				break;
				
			case 7:
				
				exit = true;
				
				break;

			default:
				
				System.out.println("\nErrore : input non valido\n");
				break;
			}
			
		} while (!exit);
		
		System.out.println("\n\nProgramma terminato");
		

	}
	
	private static void registra() {
		
		try {
			
			System.out.println("\nRegistra:\n");
			
			String username, nome, cognome;
			
			System.out.print("Inserisci l'username : ");
			username = scanner.next();
			
			System.out.print("Inserisci il nome : ");
			nome = scanner.next();
			
			System.out.print("Inserisci il cognome : ");
			cognome = scanner.next();
			
			String ris = ws.registerUser(content_type, authToken, username, nome, cognome);
			
			System.out.println("\n" + ris);
			
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
	
	private static boolean login() {
		
		System.out.println("\nFai login usando il tuo token:\n");
		
		String token;
		
		System.out.print("Inserisci il tuo token : ");
		scanner.nextLine();
		token = scanner.nextLine().trim();
		
		try {
			
			boolean ris = ws.validateToken(token, content_type);
			
			return ris;
			
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
		
		return false;
	}
	
	private static void mostraTuttiLibri() {
		
		try {
			
			System.out.println("\n-> Lista tutti libri:");
			
			ListaLibri lista = ws.getListaLibri(content_type, authToken);
			
			System.out.println("\n" + lista.stampaLibri());
			
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
	
	private static void mostraMieRecensioni() {
		
		try {
			
			System.out.println("\n-> Le mie recensioni: ");
			
			Recensioni lista = ws.getMieRecensioni(content_type, authToken);
			
			System.out.println("\n" + lista.stampaRecensioni());
			
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
	
	private static void mostraRecensioniPerLibro() {
		
		try {
			
			System.out.println("\nLista libri:");
			
			ListaLibri lista = ws.getListaLibri(content_type, authToken);
			
			System.out.println(lista.stampaLibri());
			
			System.out.print("\nInserisci id libro: ");
			
			int id = scanner.nextInt();
			
			Recensioni recensioni = ws.getRecensioniPerLibro(id, content_type, authToken);
			
			System.out.println("\n" + recensioni.stampaRecensioni());
			
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
	
	private static void inserisciRecensione() {
		
		try {
			
			System.out.println("\nLista libri:");
			
			ListaLibri lista = ws.getListaLibri(content_type, authToken);
			
			System.out.println(lista.stampaLibri());
			
			int id_libro;
			float voto;
			String commento;
			
			System.out.print("\nInserisci id del libro che vuoi recensionare: ");
			id_libro = scanner.nextInt();
			
			System.out.print("inserisci il voto: ");
			voto = scanner.nextFloat();
			
			System.out.print("inserisci un commento: ");
			scanner.nextLine();
			commento = scanner.nextLine();
			
			String ris = ws.pubblicaRecensione(content_type, authToken, id_libro, voto, commento);
			
			System.out.println(ris + "\n");
			
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
	
	private static void cancellaUnaRecensione() {
		try {
			
			System.out.println("\nLe tue recensioni:");
			
			Recensioni lista = ws.getMieRecensioni(content_type, authToken);
			
			System.out.println("\n" + lista.stampaRecensioni());
			
			System.out.print("Inserisci l'id della recensione che vuoi cancellare: ");
			
			int id_recensione = scanner.nextInt();
			
			String ris = ws.deleteRecensione(content_type, authToken, id_recensione);
			
			System.out.println(ris +  "\n");
			
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
	
	private static void modificaRecensione() {
		
		try {
			
			System.out.println("\nLe tue recensioni:");
			
			Recensioni lista = ws.getMieRecensioni(content_type, authToken);
			
			System.out.println("\n" + lista.stampaRecensioni());
			
			int id_recensione;
			float voto;
			String commento;
			
			System.out.print("Inserisci l'id della recensione che vuoi modificare: ");
			
			id_recensione = scanner.nextInt();
			
			System.out.print("Inserisci il nuovo voto: ");
			
			voto = scanner.nextFloat();
			
			System.out.println("Inserisci il nuovo commento: ");
			
			scanner.nextLine();
			commento = scanner.nextLine();
			
			String ris = ws.updateRecensione(content_type, authToken, id_recensione,voto, commento);
			
			System.out.println(ris + "\n");
			
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
}
