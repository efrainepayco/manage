<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

class Controller extends BaseController {
    public $rest;
    public function __construct() {
        $this->rest = 'https://secure.payco.co/apprest/';
    }

    public function crearRespuesta($datos) {
        return response()->json(['data' => $datos]);
    }

    public function crearRespuestaSymfony($datos) {
        
          $respuesta=new SymfonyJsonResponse();
                $respuesta->setData($datos);
           return $respuesta;
    }

    public function crearRespuestaError($mensaje, $codigo) {
        return response()->json(['message' => $mensaje, 'code' => $codigo], $codigo);
    }

    public static function crearMsgRespuesta($cod) {
        $response = new \stdClass();
        $response->cod = "";
        $response->message = "";

        switch ($cod) {
            case 0:
                $response->cod = 0;
                $response->message = "Exitoso";
                break;
            case 1:
                $response->cod = 1;
                $response->message = "Error Referencia no existe";
                break;
            case 2:
                $response->cod = 2;
                $response->message = "Error Referencia ya ha sido pagada";
                break;
            case 3:
                $response->cod = 3;
                $response->message = "Error Referencia ha caducado";
                break;
            case 4:
                $response->cod = 4;
                $response->message = "Error No hay Conexión con la bd";
                break;
            case 5:
                $response->cod = 5;
                $response->message = "Errror el valor no coincide";
                break;
            case 6:
                $response->cod = 6;
                $response->message = "Error el documento no coincide";
                break;
            case 7:
                $response->cod = 7;
                $response->message = "Error Inesperado";
                break;
            default:
                # code...
                break;
        }
        return $response;
    }  
}
