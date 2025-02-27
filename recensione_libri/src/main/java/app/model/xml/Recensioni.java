package app.model.xml;

import java.util.ArrayList;
import java.util.List;
import jakarta.xml.bind.annotation.XmlAccessType;
import jakarta.xml.bind.annotation.XmlAccessorType;
import jakarta.xml.bind.annotation.XmlElement;
import jakarta.xml.bind.annotation.XmlRootElement;
import jakarta.xml.bind.annotation.XmlType;

@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "", propOrder = { "recensioni" }) 
@XmlRootElement(name = "Recensioni") 
public class Recensioni {

    @XmlElement(name = "Recensione")
    private List<Recensione> recensioni;

    public Recensioni() {
        this.recensioni = new ArrayList<>();
    }

    public void stampaRecensioni() {
        for (Recensione recensione : recensioni) {
            System.out.println(recensione.toString());
        }
    }

}