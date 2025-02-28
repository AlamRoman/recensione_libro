package app.model.xml;

import java.util.ArrayList;
import java.util.List;
import jakarta.xml.bind.annotation.XmlAccessType;
import jakarta.xml.bind.annotation.XmlAccessorType;
import jakarta.xml.bind.annotation.XmlElement;
import jakarta.xml.bind.annotation.XmlRootElement;
import jakarta.xml.bind.annotation.XmlType;

@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "", propOrder = { "libri" }) 
@XmlRootElement(name = "Libri") 
public class ListaLibri {

    @XmlElement(name = "Libro")
    private List<Libro> libri;

    public ListaLibri() {
        this.libri = new ArrayList<>();
    }
    
    public ListaLibri(List<Libro> libri) {
        this.libri = libri;
    }

    public String stampaLibri() {
    	
    	String ris = "";
    	
        for (Libro libro : libri) {
            ris += libro.toString() + "\n";
        }
        
        return ris;
    }

}
