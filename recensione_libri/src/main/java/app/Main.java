package app;

import java.util.Scanner;

import app.util.WsClient;

public class Main 
{
	public static void main( String[] args ){
		WsClient c = new WsClient("http://localhost/webservice/recensioni/recensione_libro.php"); //Cambia link in base a dove stai avviando il web service
		
		boolean uscita=false;
		Scanner input = new Scanner(System.in);
		String tipoDato = "application/xml", commento = "5tt";
		float voto = 3;
		int idLibro = 7;
		String authToken ="e2ed49857e2074ce8ad9f1074684db20558aeef7b2870bf6cb0a32ebfeb81884"; //Test Token
		
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
			System.out.println(c.deleteRecensione(tipoDato, authToken, 1));
		} catch (Exception e) {
			System.out.println(e.getMessage());
		}
	}
}
