package app.model.xml;

import jakarta.xml.bind.annotation.XmlAccessType;
import jakarta.xml.bind.annotation.XmlAccessorType;
import jakarta.xml.bind.annotation.XmlElement;
import jakarta.xml.bind.annotation.XmlType;

@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "Recensione", propOrder = {
    "id",
    "id_user",
    "id_libro",
    "voto",
    "commento",
    "data_ultima_modifica"
})
public class Recensione {

    @XmlElement(required = true)
    private int id;
    @XmlElement(required = true)
    private int id_user;
    @XmlElement(required = true)
    private int id_libro; 
    @XmlElement(required = true)
    private float voto;
    @XmlElement
    private String commento;
    @XmlElement(required = true)
    private String data_ultima_modifica;

    public Recensione() {
    }

    public Recensione(int id, int id_user, int id_libro, Float voto, String commento, String data_ultima_modifica) {
        this.id = id;
        this.id_user = id_user;
        this.id_libro = id_libro;
        this.voto = voto;
        this.commento = commento;
        this.data_ultima_modifica = data_ultima_modifica;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getIdUser() {
        return id_user;
    }

    public void setIdUser(int idUser) {
        this.id_user = idUser;
    }

    public int getIdLibro() {
        return id_libro;
    }

    public void setIdLibro(int idLibro) {
        this.id_libro = idLibro;
    }

    public Float getVoto() {
        return voto;
    }

    public void setVoto(Float voto) {
        this.voto = voto;
    }

    public String getCommento() {
        return commento;
    }

    public void setCommento(String commento) {
        this.commento = commento;
    }

    public String getDataUltimaModifica() {
        return data_ultima_modifica;
    }

    public void setDataUltimaModifica(String dataUltimaModifica) {
        this.data_ultima_modifica = dataUltimaModifica;
    }

    @Override
    public String toString() {
        return "Recensione [id=" + id + ", id_user=" + id_user + ", id_libro=" + id_libro + ", voto=" + voto
                + ", commento=" + commento + ", data_ultima_modifica=" + data_ultima_modifica + "]";
    }
}
