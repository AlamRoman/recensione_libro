package app;

import java.util.Scanner;

import app.util.WsClient;

public class Main 
{
	public static void main( String[] args ){
		WsClient c = new WsClient("http://localhost/webservice/recensioni/recensione_libro.php"); //Cambia link in base a dove stai avviando il web service
		
		boolean uscita=false;
		Scanner input = new Scanner(System.in);
		
		/*do {  Menu delle opzioni
			System.out.println("Scegli l'operazione da effettuare:\n1) Login\n2) Visualizza la lista di libri\n9) Esci\n-> ");
			switch (input.nextInt()) {
			case 1:
				// TODO
				break;
			case 2:
				try {
					System.out.println(c.getListaLibri());
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
			System.out.println(c.getListaLibri());
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
}
