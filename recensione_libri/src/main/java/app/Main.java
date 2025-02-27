package app;

import java.util.Scanner;

import app.util.WsClient;

public class Main 
{
	public static void main( String[] args ){
		WsClient c = new WsClient("http://localhost/web_service/recensione_libro.php"); //Cambia link in base a dove stai avviando il web service
		
		Scanner input = new Scanner(System.in);
		String tipoDato = "application/xml";
		String commento = "5tt";
		float voto = 3;
		int idLibro = 7;
		String authToken ="7123a062ef08af773b5cff8ed91081d1dcc1d75c23cf99fbf72cacc8bb0aef12"; //Test Token
		
		boolean exit=false;
		int scelta;
		
		//selezione tipo di dati
		System.out.println("*** Recensione Libri ***");
		System.out.println("\nSeleziona tipo di dati:");
		System.out.println("\t 1. XML (default)");
		System.out.println("\t 2. JSON");
		System.out.println("\t 3. Esci");
		System.out.print("\nInserisci scelta: ");
		
		scelta = input.nextInt();
		
		if (scelta == 2) {
			tipoDato = "application/json";
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
				
				break;
				
			case 3:
				
				exit = true;
				
				break;

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
		
		/*do {  Menu delle opzioni provvisorio
			System.out.println("Scegli l'operazione da effettuare:\n1) Login\n2) Visualizza la lista di libri\n9) Esci\n-> ");
			switch (input.nextInt()) {
			case 1:
				// TODO
				break;
			case 2:
				try {
					System.out.println(c.getListaLibri(tipoDato, authToken));
				} catch (Exception e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				break;
			case 9:
				uscita = true;
				break;
			default:
				System.out.println("Operazione non disponibile, ritenta...");
				break;
			}
			
		} while (!uscita);*/
		
		try {
			//System.out.println(c.pubblicaRecensione(tipoDato, authToken, idLibro, voto, commento));
			//System.out.println(c.updateRecensione(tipoDato, authToken, 1, 3f, ""));
			//System.out.println(c.getListaLibri(tipoDato, authToken));
			//System.out.println(c.getRecensioni(tipoDato, authToken));
			//System.out.println(c.deleteRecensione(tipoDato, authToken, 1));
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
}
