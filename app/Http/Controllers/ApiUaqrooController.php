<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Autorizaciones;
use App\Models\EventosAcceso;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\AccesoNoAutorizadoNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class ApiUaqrooController extends Controller
{
    public function validarUsuario ()
    {
        $validaciones = Autorizaciones::with(['user' => function($query) {
            $query->select('nombre_usuario');
        }])->get();
        return response()->json($validaciones);
    }

    public function eventosAcceso (Request $request)
    {
        $validarDatos = $request->validate([
            'semestre' => 'required|string|max:50',
            'grupo' => 'required|string|max:50',
            'matricula' => 'required|string|max:50',
            'area_id' => 'nullable|integer',
            'usuario_id' => 'nullable|integer',
            'permiso' => 'nullable|boolean',
        ]);


        $evento = EventosAcceso::create($validarDatos);

        return response()->json($evento, 201);
    }

    public function buscarUsuario($email, $areaId)
    {
     try {
        // Decodificar el correo de base 64
        $emailDecoded = base64_decode($email);
        Log::info('Email decodificado: ' . $emailDecoded);
        //Separar el IV del correo encriptado
        $iv_lenght = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($emailDecoded, 0, $iv_lenght);
        $emailDecoded2 = substr($emailDecoded, $iv_lenght);

        // Clave para el desenciptado que se establecio en el .env
        $key = env('ENCRYPTION_KEY');

        //desencriptar el email
        $user_email = openssl_decrypt($emailDecoded2, 'aes-256-cbc', $key, 0, $iv);

        $user = User::where('email', $user_email)->first();
        $user_id = $user->usuario_id;
    
        $e = new EventosAcceso;
        $e->area_id = $areaId;
        $e->usuario_id = $user_id;
        $e->fecha_hora = Carbon::now();
        $e->updated_at = Carbon::now();
        $e->created_at = Carbon::now();
        $e->evento_id = $this->generateEventoId();
        Log::info('Evento creado: ' . $e->evento_id);


        $validacion = Autorizaciones::where('usuario_id', $user_id)
                                        ->where('area_id', $areaId)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
        // usaremos una variable para almacnear el resultado de una funcion
        // Validar si la autorozación sigue vigente usando if, y la función
        // isFuture(), que es una función proporcionada por la biblioteca
        // carbon de PHP dateTime utilzada por Laravel, para el manejo
        // de fechas y tiempos más sencilla y fluida, en este caso 
        // verifica si espires_at esta en futuro.                      
        
        if (!$validacion) {
            $e->permiso = "NO PERMITIDO";
            $e->save();
            return response()->json([
                'acceso' => false,
                'mensaje' => 'No tiene permiso para acceder a esta área'
            ]);
        } elseif (!$validacion->expires_at || Carbon::parse($validacion->expires_at)->isFuture()) {
            $e->permiso = "PERMITIDO";
            $e->save();
            return response()->json([
                'acceso' => true,
                'mensaje' => 'Acceso permitido'
            ]);
        }

        
    } catch (\Exception $e) {
        return response()->json(['acceso' => false, 'error' => $e->getMessage()], 500);
    }
                                    

    }

    public function generateEventoId()
    {
        $maxValue = 999999999;

        do {
            $randomNumber = random_int(1, $maxValue); // Puedes ajustar el rango según tus necesidades
        } while (EventosAcceso::where('evento_id', $randomNumber)->exists());
    
        return $randomNumber;    }
    
}