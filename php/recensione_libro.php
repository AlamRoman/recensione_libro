<?php 

    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "recensione_libro";

    $conn = new mysqli( $hostname, $username, $password, $database);

    function ws_operation($uri){
        $uri_arr = parse_url($uri);
        $path = explode("/", $uri_arr['path']);
        return $path[count($path)-1];
    }

    function array_to_xml($data, &$xml_data) {
        foreach($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = 'item';
                }
                $subnode = $xml_data->addChild($key);
                array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    function validate_token($token){
        global $conn;

        $sql = "SELECT * FROM users WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        }

        return false;
    }

    function get_user_id_by_token($token){
        global $conn;

        $sql = "SELECT * FROM users WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows >= 0){
            $user = $result->fetch_assoc();

            return $user["id"];
        }

        return -1;
    }

    $CONTENT_TYPE = $_SERVER["CONTENT_TYPE"] ?? "application/xml"; // default content type xml
    $METHOD = $_SERVER['REQUEST_METHOD'];
    $OPERATION = ws_operation($_SERVER['REQUEST_URI']);

    $headers = getallheaders();
    $token = $headers["Auth-Token"] ?? null; //get the auth token frim the header

    $responseData = [];
    $status_code = 405;

    if ($METHOD == "GET") { //read
        
        if ($OPERATION == "list_books") {// list all books
            
            if ($token !== null && validate_token($token)) {

                //get all reviews of an user
                $sql = "SELECT * FROM libro";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                //put all the books in the response
                while($book = $result->fetch_assoc()){
                    $responseData[] = $book;
                }

                $status_code = 200; //OK

            }else{
                $responseData = [
                    "status"  => "error",
                    "message" => "Unauthorized"
                ];
                $status_code = 401; // unauthorized
            }

        } else if ($OPERATION == "list_user_reviews"){

            if ($token !== null && validate_token($token)){

                //get user id
                $id_user = get_user_id_by_token($token);
                
                //get all reviews of an user
                $sql = "SELECT r.* FROM recensione r JOIN users u ON r.id_user = u.id";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                //put all the reviews in the response
                while($book = $result->fetch_assoc()){
                    $responseData[] = $book;
                }

                $status_code = 200; //OK

            } else{
                $responseData = [
                    "status"  => "error",
                    "message" => "Unauthorized"
                ];
                $status_code = 401; // unauthorized
            }
        } else {
            $status_code = 404; //operation not found

            $responseData = [
                "status"  => "error",
                "message" => "Operation not found"
            ];
        }

    }else if ($METHOD == "POST") { //create

        if ($OPERATION == "login") {
            
            $input = file_get_contents("php://input");

            $username = null;
            $password = null;

            if ($CONTENT_TYPE === "application/xml") {// xml data

                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($input);

                if (!$xml) {
                    http_response_code(400); // bad request
                    header("Content-Type: application/xml");
                    echo "<response><status>error</status><message>Invalid XML format</message></response>";
                    exit;
                }

                $username = (string)$xml->username;
                $password = (string)$xml->password;

                if(empty($username) || empty($password)){
                    http_response_code(400); // bad request
                    header("Content-Type: application/xml");
                    echo "<response><status>error</status><message>I parametri della richiesta sono vuoti</message></response>";
                    exit;
                }
                
            }else if($CONTENT_TYPE === "application/json"){ //json data
                // TODO
            }

            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user !== null) {
            
                if (password_verify($password, $user["password"])) {

                    //create auth token by hashing username and current time
                    $token = hash('sha256', $username . ':' . time());

                    $sql = "UPDATE users SET token = ? WHERE username = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $token, $username);
                    $stmt->execute();

                    $responseData = [
                        "status"  => "success",
                        "message" => "Login successful",
                        "token"   => $token
                    ];
                    $status_code = 200; //OK
                    
                }else{
                    $responseData = [
                        "status"  => "error",
                        "message" => "username o password errate"
                    ];
                    $status_code = 401; // unauthorized
                }

            }else{
                $responseData = [
                    "status"  => "error",
                    "message" => "username non trovato"
                ];
                $status_code = 401; // unauthorized
            }

        } else if ($OPERATION == "create_recensione") {

            if ($token !== null && validate_token($token)) {

                $input = file_get_contents("php://input");

                $id_user = "";
                $id_libro = "";
                $voto = "";
                $commento = "";
                $data_ultima_modifica = "";

                //prendi l'id del user con il suo token
                $id_user = get_user_id_by_token($token);

                $data_ultima_modifica = date('Y-m-d H:i:s');

                if ($CONTENT_TYPE === "application/xml") {// xml data

                    libxml_use_internal_errors(true);
                    $xml = simplexml_load_string($input);

                    if (!$xml) {
                        http_response_code(400); // bad request
                        header("Content-Type: application/xml");
                        echo "<response><status>error</status><message>Invalid XML format</message></response>";
                        exit;
                    }

                    $id_libro = (int) $xml->id_libro;
                    $voto = (string) $xml->voto;
                    $commento = (string) $xml->commento;

                    if (empty($id_libro) || empty($voto) || empty($commento)) {

                        http_response_code(400); // bad request
                        header("Content-Type: application/xml");
                        echo "<response><status>error</status><message>I parametri della richiesta sono vuoti</message></response>";
                        exit;
                    }
                
                }else if($CONTENT_TYPE === "application/json"){ //json data
                    // TODO
                }

                $sql = "SELECT * FROM recensione WHERE id_user = ? AND id_libro = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $id_user, $id_libro);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) {

                    $sql = "INSERT INTO recensione(id_user, id_libro, voto, commento, data_ultima_modifica) VALUES(?,?,?,?,?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iisss", $id_user,$id_libro, $voto, $commento, $data_ultima_modifica);
                    
                    if ($stmt->execute()) {
                        
                        $result = $stmt->get_result();

                        $responseData = [
                            "status"  => "success",
                            "message" => "Recensione inserito con successo"
                        ];
                        $status_code = 200; //OK

                    }else{

                        $responseData = [
                            "status"  => "error",
                            "message" => "Errore nell'inserimento nel database"
                        ];
                        $status_code = 500; // internal server error
                    }
                    
                }else{
                    $responseData = [
                        "status"  => "error",
                        "message" => "Recensione gia presente per questo libro da questo utente"
                    ];
                    $status_code = 409; // conflict
                }

            }else{
                $responseData = [
                    "status"  => "error",
                    "message" => "Unauthorized"
                ];
                $status_code = 401; // unauthorized
            }

        } else {// not found
            $status_code = 404;

            $responseData = [
                "status"  => "error",
                "message" => "Operation not found"
            ];
        }

    }else if ($METHOD == "PUT") { //update

        if($OPERATION == "update_review"){

            if ($token !== null && validate_token($token)){

                $input = file_get_contents("php://input");

                $id_recensione = "";
                $id_user = "";
                $voto = "";
                $commento = "";

                //prendi l'id del user con il suo token
                $id_user = get_user_id_by_token($token);

                $data_ultima_modifica = date('Y-m-d H:i:s');

                if ($CONTENT_TYPE === "application/xml") {// xml data

                    libxml_use_internal_errors(true);
                    $xml = simplexml_load_string($input);

                    if (!$xml) {
                        http_response_code(400); // bad request
                        header("Content-Type: application/xml");
                        echo "<response><status>error</status><message>Invalid XML format</message></response>";
                        exit;
                    }

                    $id_recensione = (int) $xml->id_recensione;
                    $voto = (string) $xml->voto;
                    $commento = (string) $xml->commento;

                    if (empty($id_recensione)) {
                        http_response_code(400); // bad request
                        header("Content-Type: application/xml");
                        echo "<response><status>error</status><message>I parametri della richiesta sono vuoti</message></response>";
                        exit;
                    }
                    
                }else if($CONTENT_TYPE === "application/json"){ //json data
                    // TODO
                }

                $sql = "SELECT * FROM recensione WHERE id = ? AND id_user = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $id_recensione, $id_user);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {

                    $msg = "";
                    $no_error = true;

                    if (!empty($voto)) { //update voto

                        $sql = "UPDATE recensione SET voto = ?, data_ultima_modifica = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssi", $voto, $data_ultima_modifica, $id_recensione);

                        if ($stmt->execute()) {
                            $msg = "voto modificato con successo";
                        }else{
                            $msg = "voto non modificato";
                            $no_error = false;
                        }
                    }

                    if (!empty($commento)) { //update commento

                        if(!empty($voto)){
                            $msg .= ", ";
                        }
                        
                        $sql = "UPDATE recensione SET commento = ?, data_ultima_modifica = ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssi", $commento, $data_ultima_modifica, $id_recensione);

                        if ($stmt->execute()) {
                            $msg .= "commento modificato con successo";
                        }else{
                            $msg .= "commento non modificato";
                            $no_error = false;
                        }
                    }

                    if ($no_error) {

                        $responseData = [
                            "status"  => "error",
                            "message" => "Modifica recensione: $msg"
                        ];
                        $status_code = 200; // OK
                        
                    }else{
                        $responseData = [
                            "status"  => "error",
                            "message" => "Modifica recensione: $msg"
                        ];
                        $status_code = 500; // internal server error
                    }
                    
                }else{// recensione inesistente o utente non autorizzato

                    $sql = "SELECT * FROM recensione WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id_recensione);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {

                        $responseData = [
                            "status"  => "error",
                            "message" => "La recensione non è stata fatta da te"
                        ];
                        $status_code = 401; // unauthorized
                        
                    }else{

                        $responseData = [
                            "status"  => "error",
                            "message" => "Recensione inesistente"
                        ];
                        $status_code = 400; // bad request

                    }

                }

            } else{//token non valido

                $responseData = [
                    "status"  => "error",
                    "message" => "Unauthorized"
                ];
                $status_code = 401; // unauthorized
            }

        } else {// operation not found
            $status_code = 404;

            $responseData = [
                "status"  => "error",
                "message" => "Operation not found"
            ];
        }
        
    }else if ($METHOD == "DELETE") { //delete

        if($OPERATION == "delete_review"){

            if ($token !== null && validate_token($token)){

                $input = file_get_contents("php://input");

                $id_recensione = "";

                if ($CONTENT_TYPE === "application/xml"){ //xml

                    libxml_use_internal_errors(true);
                    $xml = simplexml_load_string($input);

                    if (!$xml) {
                        http_response_code(400); // bad request
                        header("Content-Type: application/xml");
                        echo "<response><status>error</status><message>Invalid XML format</message></response>";
                        exit;
                    }

                    $id_recensione = (int) $xml->id_recensione;

                    if (empty($id_recensione)) {

                        http_response_code(400); // bad request
                        header("Content-Type: application/xml");
                        echo "<response><status>error</status><message>I parametri della richiesta sono vuoti</message></response>";
                        exit;
                    }

                } else if($CONTENT_TYPE === "application/json"){ //json data
                    // TODO
                }

                $sql = "SELECT * FROM recensione WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_recensione);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {

                    $sql = "DELETE FROM recensione WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id_recensione);

                    if ($stmt->execute()) {

                        $responseData = [
                            "status"  => "success",
                            "message" => "Recensione eliminata con successo"
                        ];
                        $status_code = 200; //OK
                        
                    }else{

                        $responseData = [
                            "status"  => "error",
                            "message" => "Errore durante l'eliminazione della recensione"
                        ];
                        $status_code = 500; // internal server error
                    }

                }else{
                    $responseData = [
                        "status"  => "error",
                        "message" => "Recensione inesistente"
                    ];
                    $status_code = 400; // internal server error
                }

            } else{
                $responseData = [
                    "status"  => "error",
                    "message" => "Unauthorized"
                ];
                $status_code = 401; // unauthorized
            }

        } else {// not found
            $status_code = 404;

            $responseData = [
                "status"  => "error",
                "message" => "Operation not found"
            ];
        }

    }else {
        $status_code = 405; //method not allowed

        $responseData = [
            "status"  => "error",
            "message" => "Method not allowed"
        ];
    }

    //set response code
    http_response_code($status_code);

    if ($CONTENT_TYPE === "application/xml") {

        header("Content-Type: application/xml"); //set content type
        $xmlResponse = new SimpleXMLElement('<response/>'); //create response xml
        array_to_xml($responseData, $xmlResponse); //convert data to xml

        echo $xmlResponse->asXML(); //print the response

    } else {

        header("Content-Type: application/json"); //set content type

        echo json_encode($responseData); // encode and print the response
    }

?>