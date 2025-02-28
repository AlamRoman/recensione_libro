package app.model.xml;

import jakarta.xml.bind.annotation.XmlRootElement;
import jakarta.xml.bind.annotation.XmlElement;

@XmlRootElement(name = "response")
public class Response {
    
    @XmlElement(name = "status") 
    private String status;
    
    @XmlElement(name = "message") 
    private String message;

    public Response() {}

    public Response(String status, String message) {
        this.status = status;
        this.message = message;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    @Override
    public String toString() {
        return "ResponseData{" +
                "status='" + status + '\'' +
                ", message='" + message + '\'' +
                '}';
    }
}

