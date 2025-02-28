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
    $token = $headers["Auth-Token"] ?? null; //get the auth token from the header

    $responseData = [];
    $status_code = 405;

    /**
     * Methods of the web service that handles the requested operations
     * 
     * @param string $METHOD Request method (GET, POST, PUT, DELETE)
     * @param string $OPERATION Operation to perform
     */

    if ($METHOD == "GET") { //read
        
        /**
         * Operation: list_books
         * 
         * Returns all books in the database.
         * 
         * @return array List of books
         * @response 200 OK
         * @response 401 Unauthorized
         */
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

        /**
         * Operation: list_user_reviews
         * 
         * Returns all reviews from a specified user via token.
         * 
         * @return array List of user reviews
         * @response 200 OK
         * @response 401 Unauthorized
         */
        } else if ($OPERATION == "list_user_reviews"){

            if ($token !== null && validate_token($token)){

                //get user id
                $id_user = get_user_id_by_token($token);
                
                //get all reviews of an user
                $sql = "SELECT r.* FROM recensione r JOIN users u ON r.id_user = u.id WHERE u.id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_user);
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
        /**
         * Operation: list_reviews_by_book
         * 
         * Returns all reviews associated with a book identified by ID.
         * 
         * @param int $id_libro ID of the book to return reviews for
         * @return array List of book reviews
         * @response 200 OK
         * @response 400 Bad Request
         * @response 401 Unauthorized
         * @response 404 Not Found
         */
        } else if ($OPERATION == "list_reviews_by_book"){

            if ($token !== null && validate_token($token)){

                if (isset($_GET["id_libro"])) {

                    $id_libro = $_GET["id_libro"];

                    $sql = "SELECT * FROM libro WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id_libro);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {

                        //get all reviews of a book
                        $sql = "SELECT r.* FROM recensione r JOIN libro l ON r.id_libro = l.id WHERE l.id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $id_libro);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        //put all the reviews in the response
                        while($book = $result->fetch_assoc()){
                            $responseData[] = $book;
                        }

                        $status_code = 200; //OK
                        
                    }else{
                        $responseData = [
                            "status"  => "error",
                            "message" => "Libro non trovato"
                        ];
                        $status_code = 400;
                    }
                    
                }else{
                    $status_code = 400; // bad request

                    $responseData = [
                        "status"  => "error",
                        "message" => "Token non presente nella richiesta"
                    ];
                }

            } else{
                $responseData = [
                    "status"  => "error",
                    "message" => "Unauthorized"
                ];
                $status_code = 401; // unauthorized
            }
        
            /**
             * Operation: validate_token
             * 
             * Checks whether a provided token is valid.
             * 
             * @param string $token Token to validate
             * @response 200 OK
             * @response 400 Bad Request
             */
        } else if ($OPERATION == "validate_token"){

            if (isset($_GET["token"])) {

                $token = $_GET["token"];

                if (validate_token($token)) {

                    $status_code = 200; // OK

                    $responseData = [
                        "status"  => "success",
                        "message" => "Token valido"
                    ];

                }else{

                    $status_code = 200; // OK

                    $responseData = [
                        "status"  => "error",
                        "message" => "Token non valido"
                    ];
                }

            }else{

                $status_code = 400; // bad request

                $responseData = [
                    "status"  => "error",
                    "message" => "Token non presente nella richiesta"
                ];
            }

        } else {
            $status_code = 404; //operation not found

            $responseData = [
                "status"  => "error",
                "message" => "Operation not found"
            ];
        }

    }else if ($METHOD == "POST") { //create

        /**
         * Operation: register
         * 
         * Register a new user
         * Parameters must be provided in XML or JSON format.
         * 
         * @param string $username Username to register
         * @param string $nome Name of the user
         * @param string $cognome Surname of the user
         * @return array The user login token
         * @response 200 OK
         * @response 400 Bad Request
         * @response 409 Conflict
         * @response 500 Internal Server Error
         */
        if ($OPERATION == "register") {
            
            $input = file_get_contents("php://input");

            $username = null;
            $nome = null;
            $cognome = null;

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
                $nome = (string)$xml->nome;
                $cognome = (string)$xml->cognome;

                if(empty($username) || empty($nome) || empty($cognome)){
                    http_response_code(400); // bad request
                    header("Content-Type: application/xml");
                    echo "<response><status>error</status><message>I parametri della richiesta sono vuoti</message></response>";
                    exit;
                }
                
            }else if($CONTENT_TYPE === "application/json"){ //json data
                
                $data = json_decode($input, true);

                if ($data === null) {
                    http_response_code(400); //bad request
                    echo json_encode(["status" => "error", "message" => "Invalid JSON format"]);
                    exit;
                }

                $username = $data["username"];
                $nome = $data["nome"];
                $cognome = $data["cognome"];

                if(empty($username) || empty($nome) || empty($cognome)){

                    http_response_code(400); // bad request
                    echo json_encode(["status" => "error", "message" => "Invalid JSON format"]);
                    exit;
                }
            }

            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user == null) {

                //crea il token usando hash
                $token = hash('sha256', $username . ':' . time());

                $sql = "INSERT INTO users(username, nome, cognome, token) VALUES(?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss",  $username, $nome, $cognome, $token);
                
                try {
                    $stmt->execute();

                    $responseData = [
                        "status"  => "success",
                        "message" => "Registrazione completata con successo",
                        "username" => $username,
                        "nome" => $nome,
                        "cognome" => $cognome,
                        "token"   => $token
                    ];
                    $status_code = 200; //OK
                } catch (mysqli_sql_exception $e) {
                    $responseData = [
                        "status"  => "error",
                        "message" => "Errore durante la registrazione dell'utente"
                    ];
                    $status_code = 500; // internal server error
                }
                    

            }else{ // username non disponibile

                $responseData = [
                    "status"  => "error",
                    "message" => "username non disponibile"
                ];
                $status_code = 409; // conflict
            }
        
        /**
         * Operation: create_recensione
         * 
         * Create a new review for a book from a user.
         * 
         * @param int $id_libro ID of the book
         * @param string $voto Rating given by the reviewer
         * @param string $commento Comment of the review
         * @response 200 OK
         * @response 400 Bad Request
         * @response 401 Unauthorized
         * @response 409 Conflict
         */
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

                    $data = json_decode($input, true);

                    if ($data === null) {
                        http_response_code(400); //bad request
                        echo json_encode(["status" => "error", "message" => "Invalid JSON format"]);
                        exit;
                    }

                    $id_libro = $data["id_libro"];
                    $voto = $data["voto"];
                    $commento = $data["commento"];

                    if(empty($id_libro) || empty($voto) || empty($commento)){
                        
                        http_response_code(400); // bad request
                        echo json_encode(["status" => "error", "message" => "I parametri della richiesta sono vuoti"]);
                        exit;
                    }
                }

                // controlla se ci sono recensioni da questo utente per questo libro
                $sql = "SELECT * FROM recensione WHERE id_user = ? AND id_libro = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $id_user, $id_libro);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) {

                    $sql = "INSERT INTO recensione(id_user, id_libro, voto, commento, data_ultima_modifica) VALUES(?,?,?,?,?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iisss", $id_user,$id_libro, $voto, $commento, $data_ultima_modifica);
                    
                    try {
                        $stmt->execute();

                        $result = $stmt->get_result();

                            $responseData = [
                                "status"  => "success",
                                "message" => "Recensione inserito con successo"
                            ];
                            $status_code = 200; //OK
                    } catch (mysqli_sql_exception $e) {
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
        /**
         * Operation: update_recensione
         * 
         * Edit an existing review. The user can update the rating and/or comment of a review already present in the system.
         * 
         * @param int $id_recensione ID of the review to update
         * @param string $voto Rating to update (optional)
         * @param string $commento Comment to update (optional)
         * @response 200 OK
         * @response 400 Bad Request
         * @response 401 Unauthorized
         * @response 500 Internal Server Error
         */
        if($OPERATION == "update_recensione"){

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

                    $data = json_decode($input, true);

                    if ($data === null) {
                        http_response_code(400); //bad request
                        echo json_encode(["status" => "error", "message" => "Invalid JSON format"]);
                        exit;
                    }

                    $id_recensione = $data["id_recensione"];
                    $voto = $data["voto"] ?? null;
                    $commento = $data["commento"] ?? null;

                    if(empty($id_recensione)){
                        
                        http_response_code(400); // bad request
                        echo json_encode(["status" => "error", "message" => "I parametri della richiesta sono vuoti"]);
                        exit;
                    }
                }

                //controlla se la recensione fatta dall'utente esiste
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
    
        /**
         * Operation: delete_recensione
         * 
         * Delete a review from the system. Users can only delete their own reviews.
         *
         *  @param int $id_recensione ID of the review to delete
         * @response 200 OK
         * @response 400 Bad Request
         * @response 401 Unauthorized
         * @response 500 Internal Server Error
         */
    }else if ($METHOD == "DELETE") { //delete

        if($OPERATION == "delete_recensione"){

            if ($token !== null && validate_token($token)){

                $id_recensione = $_GET["id_recensione"];

                $sql = "SELECT * FROM recensione WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id_recensione);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {

                    $sql = "DELETE FROM recensione WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id_recensione);

                    try {
                        $stmt->execute();

                        $responseData = [
                            "status"  => "success",
                            "message" => "Recensione eliminata con successo"
                        ];
                        $status_code = 200; //OK
                        
                    } catch (mysqli_sql_exception $e) {
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
                    $status_code = 400;
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

        if ($OPERATION == "list_books" && $status_code == 200) { //check type of operation
            $xmlResponse = new SimpleXMLElement('<Libri/>'); //create libri xml and adapts child element to be tipe libro

            foreach ($responseData as $libro) {
                $libroNode = $xmlResponse->addChild('Libro'); 
                foreach ($libro as $key => $value) {
                    $libroNode->addChild($key, htmlspecialchars($value));
                }
            }
            echo $xmlResponse->asXML(); //print the response
        }else if ($OPERATION == "list_user_reviews" && $status_code == 200) { //check type of operation
            $xmlResponse = new SimpleXMLElement('<Recensioni/>'); //create recensioni xml and adapts child element to be tipe recensione

            foreach ($responseData as $recensione) {
                $recensioneNode = $xmlResponse->addChild('Recensione'); 
                foreach ($recensione as $key => $value) {
                    $recensioneNode->addChild($key, htmlspecialchars($value));
                }
            }
            echo $xmlResponse->asXML(); //print the response
        }else{
            header("Content-Type: application/xml"); //set content type
            $xmlResponse = new SimpleXMLElement('<response/>'); //create response xml
            array_to_xml($responseData, $xmlResponse); //convert data to xml

            echo $xmlResponse->asXML(); //print the response
        }
        

    } else {

        header("Content-Type: application/json"); //set content type

        echo json_encode($responseData); // encode and print the response
    }

?>
