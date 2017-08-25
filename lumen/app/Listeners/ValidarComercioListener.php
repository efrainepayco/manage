<?php
namespace App\Listeners;

use App\Events\ValidarComercioEvent;
use App\Http\Validation\Validate;
use \App\Models\LlavesClientes;
use App\Http\Lib\DescryptObject as DescryptObject;
use App\Models\Clientes as Clientes;
use App\Helpers\Pago\HelperPago;
use Illuminate\Http\Request;

class ValidarComercioListener extends HelperPago
{
    private $publickey;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->public_key = $request->public_key;
        parent::__construct($request);
    }

    /**
     * Handle the event.
     *
     * @param ExampleEvent $event
     * @return void
     */
    public function handle(ValidarComercioEvent $event)
    {
        $arr_parametros = $event->arr_parametros;

        $validar = $this->validarComercio($this->public_key);
        $success = true;
        if ($validar['success'] == false) {
            return $validar;
        }
        $comercio = $this->LLavesclientes->cliente_id;
        $idcliente = $comercio;
        $comercio = Clientes::where('Id', $idcliente)->first();
        // Buscar el comercio de nuevo dentro de los clientes
        if (!is_object($comercio)) {
            $response = array(
                'success' => $arr_parametros['success'],
                'title_response' => $arr_parametros['title_response'],
                'text_response' => $arr_parametros['text_response'],
                'last_action' => $arr_parametros['last_action'],
                'data' => array()
            );
            return $this->crearRespuesta($response);
        }
        $arr_respuesta['comercio'] = $comercio;
        $arr_respuesta['idcliente'] = $idcliente;
        $arr_respuesta['success'] = $success;
        $arr_respuesta['LLavesclientes'] = $this->getLLavesclientes();

        return $arr_respuesta;
    }
}