<?php
error_log("Handlers file loaded.");
namespace ClaudeToGPTAPI\Handlers;

require_once __DIR__ . '/../vendor/autoload.php';
use function ClaudeToGPTAPI\ApiHelpers\validateRequestBody;
use function ClaudeToGPTAPI\ApiHelpers\getAPIKey;
use function ClaudeToGPTAPI\ApiHelpers\makeClaudeRequest;

/**
 * Handles API requests.
 *
 * @param mixed $vars - Variables passed to the handler.
 */
class RequestHandler {
    public static function handle($vars) {
        try {
            $input = file_get_contents("php://input");
            $requestBody = json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON");
            }
            $validationErrors = validateRequestBody($requestBody);
            if (!empty($validationErrors)) {
                http_response_code(400);
                echo json_encode(["errors" => $validationErrors]);
                return;
            }
            $apiKey = getAPIKey($_SERVER);
            $response = makeClaudeRequest($apiKey, $requestBody);
            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo "Server Error: " . $e->getMessage();
        }
    }
}

/**
 * Handles requests for the "/v1/models" route.
 * This class is responsible for returning a JSON response containing the available models.
 */
class ModelsHandler {
    /**
     * Handles the incoming request and sends a JSON response with the models list.
     * 
     * @param array $vars Variables passed to the handler, not used in this context.
     */
    public static function handle($vars) {
        header('Content-Type: application/json');  // Sets the header for content type to JSON

        // Accessing the global variable containing models information
        global $modelsList;

        // Echoing out the JSON encoded list of models
        echo json_encode($modelsList);
    }
}