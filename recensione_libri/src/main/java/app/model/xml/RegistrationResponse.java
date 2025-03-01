package app.model.xml;

import jakarta.xml.bind.annotation.XmlAccessType;
import jakarta.xml.bind.annotation.XmlAccessorType;
import jakarta.xml.bind.annotation.XmlElement;
import jakarta.xml.bind.annotation.XmlRootElement;
import jakarta.xml.bind.annotation.XmlType;

@XmlRootElement(name = "response")
public class RegistrationResponse {
    private String status;
    private String message;
    private String username;
    private String nome;
    private String cognome;
    private String token;

    public String getStatus() {
        return status;
    }

    @XmlElement(name = "status")
    public void setStatus(String status) {
        this.status = status;
    }

    public String getMessage() {
        return message;
    }

    @XmlElement(name = "message")
    public void setMessage(String message) {
        this.message = message;
    }

    public String getUsername() {
        return username;
    }

    @XmlElement(name = "username")
    public void setUsername(String username) {
        this.username = username;
    }

    public String getNome() {
        return nome;
    }

    @XmlElement(name = "nome")
    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getCognome() {
        return cognome;
    }

    @XmlElement(name = "cognome")
    public void setCognome(String cognome) {
        this.cognome = cognome;
    }

    public String getToken() {
        return token;
    }

    @XmlElement(name = "token")
    public void setToken(String token) {
        this.token = token;
    }
}
