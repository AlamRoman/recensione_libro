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
                
                //get all books from the database
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

        }else{
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

        }else if ($OPERATION == "create_recensione") {

            if ($token !== null && validate_token($token)) {

                $input = file_get_contents("php://input");

                $id_user = "";
                $id_libro = "";
                $voto = "";
                $commento = "";
                $data_ultima_modifica = "";

                //prendi l'id del user con il suo token
                $sql = "SELECT * FROM users WHERE token = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                $id_user = $user["id"];

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

                    $id_libro = (string) $xml->id_libro;
                    $voto = (string) $xml->voto;
                    $commento = (string) $xml->commento;
                
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

        }else {// not found
            $status_code = 404;

            $responseData = [
                "status"  => "error",
                "message" => "Operation not found"
            ];
        }

    }else if ($METHOD == "PUT") { //update
        # code...
    }else if ($METHOD == "DELETE") { //delete
        # code...
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