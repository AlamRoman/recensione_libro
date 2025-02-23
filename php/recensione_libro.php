<?php 

    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "recensione_libro";

    $conn = new mysqli( $hostname, $username, $password, $database);

    header('Content-Type: application/xml');

    $status_code = "";

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

    $CONTENT_TYPE = $_SERVER["CONTENT_TYPE"] ?? "application/xml"; // default content type xml
    $METHOD = $_SERVER['REQUEST_METHOD'];
    $OPERATION = ws_operation($_SERVER['REQUEST_URI']);


    if ($METHOD == "GET") { //read
        # code...
    }else if ($METHOD == "POST") { //create

        if ($OPERATION == "login") {
            
            $input = file_get_contents("php://input");

            $username = null;
            $password = null;

            $responseData = [];
            $responseType = "";

            if ($contentType === "application/xml") {// xml data

                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($rawInput);

                if (!$xml) {
                    http_response_code(400); // bad request
                    header("Content-Type: application/xml");
                    echo "<response><status>error</status><message>Invalid XML format</message></response>";
                    exit;
                }

                $username = (string)$xml->username;
                $password = (string)$xml->password;
                $responseType = "xml";
                
            }else if($contentType === "application/json"){ //json data
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

                    $token = base64_encode($username . ':' . time());

                    $responseData = [
                        "status"  => "success",
                        "message" => "Login successful",
                        "token"   => $token
                    ];
                    $statuscode = 200; //OK
                    
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

        }else{// not found
            $statuscode = 404;
        }

    }else if ($METHOD == "PUT") { //update
        # code...
    }else if ($METHOD == "DELETE") { //delete
        # code...
    }else {
        $status_code = 405; //method not allowed
    }

    http_response_code($status_code);

    if ($responseType == "xml") {
        header("Content-Type: application/xml");
        $xmlResponse = new SimpleXMLElement('<response/>');
        array_to_xml($responseData, $xmlResponse);
        echo $xmlResponse->asXML();
    } else {
        header("Content-Type: application/json");
        echo json_encode($responseData);
    }

?>