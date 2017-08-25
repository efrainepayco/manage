<?php

namespace App\Http\Controllers;
use App\Events\ExampleEvent;
use App\Http\Lib\PaycoAesPrueba;
use \App\Models\Transacciones as Transacciones;
use \App\Models\DetalleTransacciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Http\Lib\MCryptPayco;
use App\Http\Lib\AesRuby;

class EfectyGanaController extends Controller {
    private $request;
    public function __construct(Request $request) {
//        $this->middleware('oauth');
        $this->request = $request;
        //prueba
    }
    /**
     * 
     * @param integer $referencia
     * @return response
     */
    public function consultar_efecty($referencia) {
        return $this->consultar('EF', $referencia);
    }
    /**
     * 
     * @param integer $referencia
     * @return response
     */
    public function consultar_gana($referencia) {
        return $this->consultar('GA', $referencia);
    }

    /**
     * 
     * @return response
     */
    public function aplicar_pago_gana() {
        return $this->aplicarPago('GA');
    }

    /**
     * 
     * @return response
     */
    public function aplicar_pago_efecty() {
        return $this->aplicarPago('EF');
    }

    public function phpinfo() {
        return phpinfo();
    }
    /**
     * 
     * @param string $slug
     * @param string $referencia
     * @return object
     */
    private function consultar($slug, $referencia) {
        $slug = strtoupper($slug);
        $tr = Transacciones::where(['Id' => $referencia, 'tarjeta' => $slug])
                ->first();

        $fecha = date('Y-m-d');
        try {
            $dt = DetalleTransacciones::where('pago', $tr->Id)
                ->first();
            if ($tr) {
                $respuesta = $this->validarEstado("consultar", null, null,
                    $tr->Id, $tr->estado, $tr->fecha_exp);

                //Retornamos la respuesta
                $array_respuesta['cod_res'] = $respuesta->cod;
                $array_respuesta['menssag_rest'] = $respuesta->message;
                $array_respuesta['IdCliente'] = $dt->cedula;
                $array_respuesta['nombresCliente'] = $dt->nombres;

                $apellidos = $dt->apellidos;
                $apellido1 = $apellidos;
                $apellido2 = "";

                $strapellidos = explode(" ", $apellidos);
                if (count($strapellidos) >= 2) {
                    $apellido1 = $strapellidos[0];
                    $apellido2 = $strapellidos[1];
                }

                $array_respuesta['apellido1'] = $apellido1;
                $array_respuesta['apellido2'] = $apellido2;
                $array_respuesta['valor'] = (int) $tr->valortotal;
                $array_respuesta['referencia'] = $referencia;
                $array_respuesta['fecha'] = $fecha;
            } else {

                $respuesta = $this->crearMsgRespuesta(1);
                $array_respuesta['cod_res'] = $respuesta->cod;
                $array_respuesta['menssag_rest'] = $respuesta->message;
                $array_respuesta['id_cliente'] = $dt->cedula;
                $array_respuesta['nombresCliente'] = $dt->nombres;
                $array_respuesta['apellido1'] = 'NA';
                $array_respuesta['apellido2'] = 'NA';
                $array_respuesta['valor'] = 0;
                $array_respuesta['referencia'] = $referencia;
                $array_respuesta['fecha'] = $fecha;
            }
        } catch (\Exception $ex) {
            $respuesta = $this->crearMsgRespuesta(1);
            $array_respuesta['cod_res'] = $respuesta->cod;
            $array_respuesta['menssag_rest'] = $respuesta->message;
            $array_respuesta['IdCliente'] = '0000000000';
            $array_respuesta['nombresCliente'] = 'NA';
            $array_respuesta['apellido1'] = 'NA';
            $array_respuesta['apellido2'] = 'NA';
            $array_respuesta['valor'] = 0;
            $array_respuesta['referencia'] = $referencia;
            $array_respuesta['fecha'] = $fecha;
        }
        return $this->crearRespuesta($array_respuesta);
    }
    /**
     * 
     * @param string $slug
     * @return object
     */
    public function aplicarPago($slug) {
        $request = $this->request;
        $referencia = $request->referencia;
        $valor = $request->valor;
        $oficina = $request->oficina;
        $ordendeservicio = $request->ordendeservicio;
        $fecha = $request->fecha;


        try {
            //Primera Opcion Buscar por Pin
            $tr = Transacciones::where(['Id' => $referencia, 'tarjeta' => $slug])
                    ->first();
            if ($tr) {
                $dt = DetalleTransacciones::where('pago', $tr->Id)->first();

                $respuesta = $this->validarEstado("aplicar_pago", $valor,
                    $tr->valortotal,
                    $tr->Id, $tr->estado, $tr->fecha_exp);
                //Retornamos la respuesta
                if ($respuesta->cod == 0) {
                    $tr->estado = 'Aceptada';
                    $tr->respuesta = 'Aprobada';
                    $tr->cod_respuesta = '00';
                    $tr->autorizacion = $ordendeservicio;
                    $tr->save();

                    /*
                      //Abonar la transacción y enviar el comprobante
                      $utils = new Utils();
                      $url_rest = 'https://secure.payco.co/apprest/';
                      $url_final = $url_rest . 'abonartransaccion/' . $tr->Id;
                      $responseabono =
                      $utils->sendCurlVariables($url_final, array(), 'GET');

                      if($responseabono['header_code'] == '200') {
                      $responseabono = json_decode($responseabono['body']);
                      if (is_object($responseabono)) {
                      $response = 'Ok; Abono realizado existosamente';
                      }
                      }

                      $url_correo =
                      $url_rest . 'email/transaccion?transaction_id='.$tr->Id;
                      $responsecorreo =
                      $utils->sendCurlVariables($url_correo, array(), 'GET');

                      if ($responsecorreo['header_code'] == '200') {
                      $response = 'Ok; Correo enviado existosamente';
                      }
                     */
                }
            } else {
                $respuesta = $this->crearMsgRespuesta(1);
            }
            $array_respuesta['cod_res'] = $respuesta->cod;
            $array_respuesta['menssag_rest'] = $respuesta->message;
            $array_respuesta['referencia'] = $referencia;
        } catch (\Exception $ex) {
            $respuesta = $this->crearMsgRespuesta(1);
            $array_respuesta['cod_res'] = $respuesta->cod;
            $array_respuesta['menssag_rest'] = $respuesta->message;
            $array_respuesta['referencia'] = $referencia;
        }

        return $this->crearRespuesta($array_respuesta);
    }

    /**
     * 
     * @param string $tipo
     * @param float $valor
     * @param integer $tr_id
     * @param string $tr_estado
     * @param date $tr_fecha_exp
     * @return object
     */
    private function validarEstado($tipo, $valor, $valor_total, $tr_id,
                                   $tr_estado, $tr_fecha_exp) {
        $tr = DetalleTransacciones::where('pago', $tr_id)->first();
;
        //Pago esta aceptado
        if ($tr_estado == 'Aceptada') {
            $respuesta = $this->crearMsgRespuesta(2);
        } else {
            if ($tr_fecha_exp != "" && $tr_estado == 'Pendiente') {
                $fechaexpiracion = date_format($tr_fecha_exp, 'Y-m-d H:i:s');
                $ahora = date('Y-m-d H:i:s');
                if (strtotime($fechaexpiracion) < strtotime($ahora)) {
                    $respuesta = $this->crearMsgRespuesta(3);
                } else {
                    $respuesta = $this->crearMsgRespuesta(0);
                }
            } else {
                if ($tr_estado == 'Pendiente') {
                    $respuesta = $this->crearMsgRespuesta(0);
                } else {
                    $respuesta = $this->crearMsgRespuesta(3);
                }
            }
            if ($tipo == 'aplicar_pago') {
                //Validar si el monto coincide y si la cedula coincide
                $valorbd = (int) $valor_total;
                $valor = (int) $valor;
                if ($valorbd != $valor) {
                    $respuesta = $this->crearMsgRespuesta(5);
                }
            }
        }
        return $respuesta;
    }

    public function prueba_desencriptacion($slug)
    {
        switch ($slug) {
            case 'ruby':
                $encryptedData="0IkkstVn4EEAdQrA0t3mKmv68ZrDRN0LTia01kbaJcg=";
                $iv=base64_decode("MDAwMDAwMDAwMDAwMDAwMA==");
                $private_key="5c4773856f296c674685209bbfd11f92";
                $McryptAes=new AesRuby($private_key, $iv);
                $decrypt=$McryptAes->decrypt($encryptedData);
                break;
            case 'ios':
                $encryptedData="JU0jdU4eyQaT9T2cWMUo9Q==";
                $private_key="d044690f744d28dd57bdac30a7a0bdc9";
                $decrypt=$this->decryptios($private_key,$encryptedData);
                break;
            case 'android':
                $encryptedData="3a78f5e9acee41f23245e75daaae0cd1";
                $iv=base64_decode("MDAwMDAwMDAwMDAwMDAwMA==");
                $private_key="8e096211b8d063586ed197ca5133305f";
                $McryptAes=new MCryptPayco($private_key, $iv);
                $decrypt=$McryptAes->decrypt($encryptedData);
                break;
            case 'javascript':
                $encryptedData="PxFtRU0AZjIA3j65bx1T4g==";
                $iv=base64_decode("AAAAAAAAAAAAAAAAAAAAAA=");
                $private_key=base64_decode("HsxCuyh+Y4Q010wO6hqT1A==");
                $decrypt=openssl_decrypt(base64_decode($encryptedData), 'AES-128-CBC',
                    $private_key, OPENSSL_RAW_DATA, $iv);
                break;
        }
        dd( $decrypt );
    }

    private function decryptios($key,$value){

        $key=substr($key,0, 16);

        $base64encoded_ciphertext = $value;
        $res_non = mcrypt_decrypt(MCRYPT_RIJNDAEL_128,
            $key, base64_decode($base64encoded_ciphertext), 'ecb');
        $decrypted = $res_non;
        $dec_s2 = strlen($decrypted);
        $padding = ord($decrypted[$dec_s2-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return  $decrypted;
    }
}
