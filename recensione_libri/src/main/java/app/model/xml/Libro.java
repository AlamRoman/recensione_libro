package app.model.xml;


import java.math.BigInteger;
import jakarta.xml.bind.annotation.XmlAccessType;
import jakarta.xml.bind.annotation.XmlAccessorType;
import jakarta.xml.bind.annotation.XmlElement;
import jakarta.xml.bind.annotation.XmlType;

// Classe inutilizzata

@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "Libro", propOrder = {
    "id",
    "titolo",
    "autore",
    "descrizione",
    "isbn",
    "genere",
    "anno_pubblicazione"
})

public class Libro {
	@XmlElement(required = true)
    protected BigInteger id;
	@XmlElement(required = true)
    protected String titolo;
    @XmlElement
    protected String autore;
    @XmlElement
    protected String descrizione;
    @XmlElement(required = true)
    protected String isbn;
    @XmlElement
    protected String genere;
    @XmlElement
    protected BigInteger anno_pubblicazione;
    
	public BigInteger getId() {
		return id;
	}
	public void setId(BigInteger id) {
		this.id = id;
	}
	public String getTitolo() {
		return titolo;
	}
	public void setTitolo(String titolo) {
		this.titolo = titolo;
	}
	public String getAutore() {
		return autore;
	}
	public void setAutore(String autore) {
		this.autore = autore;
	}
	public String getDescrizione() {
		return descrizione;
	}
	public void setDescrizione(String descrizione) {
		this.descrizione = descrizione;
	}
	public String getIsbn() {
		return isbn;
	}
	public void setIsbn(String isbn) {
		this.isbn = isbn;
	}
	public String getGenere() {
		return genere;
	}
	public void setGenere(String genere) {
		this.genere = genere;
	}
	public BigInteger getAnno_pubblicazione() {
		return anno_pubblicazione;
	}
	public void setAnno_pubblicazione(BigInteger anno_pubblicazione) {
		this.anno_pubblicazione = anno_pubblicazione;
	}
}
