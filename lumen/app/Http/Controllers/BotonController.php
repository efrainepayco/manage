<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;

class BotonController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->middleware('oauth');
        $this->request = $request;
        //prueba
    }

    public function generate_pago()
    {
        if (Auth::once($credentials)) {
            return Auth::user()->id;
        }

        dd($formParams);
        /*
        if ($this->getRequest()->getSession()->get('username')) {
            // se reciben las variables
            $idcli       = $this->getRequest()->get('idcli');
            // se instancia el modelo
            $em         = $this->getDoctrine()->getManager();

            $cliente    = $em->getRepository('PanelBundle:Clientes')->find($idcli);
            $keycli     = $cliente->getKeyCli();
            $doc        = $cliente->getDocumento();
            $newxkeyup  = sha1($keycli.$doc);
            return new Response(json_encode(array('cust' => $idcli, 'key' => $newxkeyup)));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
        */
    }

    public function generar_usuario() {
        $user = new User();
        $user->password = Hash::make('epayco123');
        $user->email = 'admin@epayco.co';
        $user->save();
    }
}