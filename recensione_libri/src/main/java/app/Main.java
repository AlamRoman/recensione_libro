package app;

import java.util.Scanner;

import app.util.WsClient;

public class Main {
	
	private static String content_type = "application/xml";
	private static WsClient ws = new WsClient("http://localhost/web_service/recensione_libro.php"); //Cambia link in base a dove stai avviando il web service
	
	public static void main( String[] args ){
		
		Scanner input = new Scanner(System.in);
		String commento = "5tt";
		float voto = 3;
		int idLibro = 7;
		String authToken ="7123a062ef08af773b5cff8ed91081d1dcc1d75c23cf99fbf72cacc8bb0aef12"; //Test Token
		
		boolean exit=false;
		int scelta;
		
		//selezione tipo di dati
		System.out.println("*** Recensione Libri ***");
		System.out.println("\nSeleziona il Content Type:");
		System.out.println("\t 1. XML (default)");
		System.out.println("\t 2. JSON");
		System.out.println("\t 3. Esci");
		System.out.print("\nInserisci scelta: ");
		
		scelta = input.nextInt();
		
		if (scelta == 2) {
			content_type = "application/json";
		}else if(scelta == 3) {
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
			
			scelta = input.nextInt();
			
			switch (scelta) {
			case 1:
				
				break;
				
			case 2:
				
				boolean ris = login(input);
				
				if (ris) {//token valido
					
					exit = true;
					
				}else {//token non valido
					
					System.out.println("\nError: token non valido\n");
				}
				
				break;
				
			case 3:
				
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
			System.out.println("\t 3. Inserisci una recensione");
			System.out.println("\t 4. Modifica una recensione");
			System.out.println("\t 5. Cancella una recensione");
			System.out.println("\t 6. Esci");
			System.out.print("\nInserisci scelta: ");
			
			scelta = input.nextInt();
			
			switch (scelta) {
			case 1:
				
				break;
				
			case 2:
				
				break;
				
			case 3:
				
				break;
			
			case 4:
				
				break;
				
			case 5:
				
				break;
				
			case 6:
				
				exit = true;
				break;

			default:
				
				System.out.println("\nErrore : input non valido\n");
				break;
			}
			
		} while (!exit);
		
		
		try {
			//System.out.println(ws.pubblicaRecensione(content_type, authToken, idLibro, voto, commento));
			//System.out.println(ws.updateRecensione(content_type, authToken, 1, 3f, ""));
			//System.out.println(ws.getListaLibri(content_type, authToken));
			//System.out.println(ws.getRecensioni(content_type, authToken));
			//System.out.println(ws.deleteRecensione(content_type, authToken, 1));
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
	
	private static void registra(Scanner scanner) {

		System.out.println("\nRegistra:\n");
		
		String username, nome, cognome;
		
		System.out.print("Inserisci l'username : ");
		username = scanner.next();
		
		System.out.print("Inserisci il nome : ");
		nome = scanner.next();
		
		System.out.print("Inserisci il cognome : ");
		cognome = scanner.next();
		
		//ws client request
	}
	
	private static boolean login(Scanner scanner) {
		
		System.out.println("\nFai login usando il tuo token:\n");
		
		String token;
		
		System.out.print("Inserisci il tuo token : ");
		scanner.nextLine();
		token = scanner.nextLine().trim();
		
		try {
			
			boolean ris = ws.validateToken(token, content_type);
			
			return ris;
			
		} catch (Exception e) {
			e.printStackTrace();
		}
		
		return false;
	}
}
