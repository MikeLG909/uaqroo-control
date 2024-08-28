<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Solicitud;
use App\Models\SolicitudUsuarios;
use App\Models\EventosAcceso;
use App\Models\Autorizaciones;
use RealRashid\SweetAlert\Facades\Alert;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use App\Models\User;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    public function crearSolicitud()
    {
        $users_i = User::users();
        $areas = Area::all();


        return view('pages-control.solicitudes.solicitud_form', compact('users_i', 'areas'));

    }

    public function store(Request $request)
    {
        $solicitud = new Solicitud();
        $solicitud->usuario_id = Auth::user()->usuario_id;
        $solicitud->mensaje = $request->mensaje;
        $solicitud->area_id = $request->area;
        $solicitud->estado = 'PENDIENTE';
        $solicitud->mensaje = $request->mensaje;
        $solicitud->save();

        $usuarios = $request->usuarios;

        foreach ($usuarios as $user)
        {
            $solicitud_usuario = new SolicitudUsuarios();
            $solicitud_usuario->solicitud_id = $solicitud->solicitud_id;
            $solicitud_usuario->usuario_id = $user;
            $solicitud_usuario->updated_at = now();
            $solicitud_usuario->save();
        }

        return redirect()->back()->with('success', 'Se acaba de enviar tu solicitud');


    }

    public function index()
    {
        setlocale(LC_TIME, 'es_ES.UTF-8');
        Carbon::setLocale('es');

        $solicitudes = DB::table('solicitudes as a')
                        ->join('usuarios as b', 'a.usuario_id', '=', 'b.usuario_id')
                        ->join('persona as c', 'b.usuario_id', '=', 'c.usuario_id')
                        ->join('areas as e', 'a.area_id', '=', 'e.area_id')
                        ->select('c.nombre', 'c.ape_materno', 'c.ape_paterno', 'a.created_at', 'a.solicitud_id', 'a.estado')
                        ->orderBy('a.created_at', 'desc')  // Ordenar por fecha de creación descendente
                        ->get();
        
      /*  foreach($solicitudes as $solicitud) {

        $solicitudes_usuario = DB::table('solicitud_usuarios as s')
                        ->join('solicitudes as b', 's.solicitud_id', '=', 'b.solicitud_id')
                        ->join('usuarios as u', 's.usuario_id', '=', 'u.usuario_id')
                        ->join('persona as p', 'u.usuario_id', '=', 'p.usuario_id')
                        ->select('b.area_id', 's.usuario_id')
                        ->where('s.solicitud_id', $solicitud->solicitud_id)
                        ->get(); 

        } */

        $solicitudes_format = collect($solicitudes)->map(function($solicitud){
            return [
                'nombre' => $solicitud->nombre . ' ' . $solicitud->ape_materno . ' '. $solicitud->ape_paterno,
                'id' => $solicitud->solicitud_id,
                'estado' => $solicitud->estado,
                'fecha' => $solicitud->created_at
                    ? Carbon::parse($solicitud->created_at)->diffForHumans()
                    : 'No se ha registrado fecha',
            ];
        });

        $solicitudes_array = $solicitudes_format->toArray();




        return view('pages-control.solicitudes.buzon', compact('solicitudes_array'));

    }

    public function verSolicitud(String $id)
    {
        setlocale(LC_TIME, 'es_ES.UTF-8');
        Carbon::setLocale('es');

        $solicitud = DB::table('solicitudes as a')
        ->join('usuarios as b', 'a.usuario_id', '=', 'b.usuario_id')
        ->join('persona as c', 'b.usuario_id', '=', 'c.usuario_id')
        ->join('areas as e', 'a.area_id', '=', 'e.area_id')
        ->where('a.solicitud_id', $id)
        ->select('c.nombre', 'c.ape_materno', 'c.ape_paterno', 'a.created_at', 'a.solicitud_id', 'a.estado', 'a.created_at', 'a.fecha_deseada',
          'a.mensaje', 'a.area_id')
        ->first();

        $fecha_creacion = Carbon::parse($solicitud->created_at)->translatedFormat('j \d\e F \d\e Y');
        $fecha_deseada = Carbon::parse($solicitud->fecha_deseada)->translatedFormat('j \d\e F \d\e Y');

        $usuarios = DB::table('solicitud_usuarios as s')
        ->join('solicitudes as b', 's.solicitud_id', '=', 'b.solicitud_id')
        ->join('usuarios as u', 's.usuario_id', '=', 'u.usuario_id')
        ->join('persona as p', 'u.usuario_id', '=', 'p.usuario_id')
        ->join('roles as r', 'u.rol_id', '=', 'r.rol_id')
        ->select(DB::raw('CONCAT(p.nombre, " ", p.ape_materno, " ", p.ape_paterno) AS full_name'), 'r.rol', 'b.area_id', 's.usuario_id',)
        ->where('s.solicitud_id', $solicitud->solicitud_id)
        ->get(); 


      //  dd($usuarios);

        return view('pages-control.solicitudes.ver-solicitud', compact('solicitud', 'fecha_creacion', 'fecha_deseada', 'usuarios'));
        
    }

    public function concederPermisos(Request $request)
    {
            // Valida los datos recibidos
    $request->validate([
        'selected_items' => 'array', // Asegúrate de que sea un array
        'selected_items.*' => 'integer' // Cada elemento del array debe ser un entero
    ]);

    // Obtén los IDs seleccionados
    $users = $request->input('selected_items', []);
    $area = $request->area;

   // dd($users);

   foreach($users as $user)
   {
       $autorizacion = new Autorizaciones;
       $autorizacion->usuario_id = $user;
       $autorizacion->area_id = $area;
       $now = now(); // Obtener la fecha y hora actual
       $autorizacion->created_at = $now;
       $autorizacion->updated_at = $now;
       $autorizacion->expires_at = Carbon::create($request->fecha);
       $autorizacion->save();

   }

   $solicitud = Solicitud::find($request->solicitud);
   $solicitud->estado = 'ACEPTADA';
   $solicitud->save();

   return redirect()->back()->with('success', 'Los usuarios ahora tienen acceso a esta área');

    }

    public function miBuzon()
    {
        setlocale(LC_TIME, 'es_ES.UTF-8');
        Carbon::setLocale('es');

        $solicitudes = DB::table('solicitudes as a')
                        ->join('usuarios as b', 'a.usuario_id', '=', 'b.usuario_id')
                        ->join('persona as c', 'b.usuario_id', '=', 'c.usuario_id')
                        ->join('areas as e', 'a.area_id', '=', 'e.area_id')
                        ->select('c.nombre', 'c.ape_materno', 'c.ape_paterno', 'a.created_at', 'a.solicitud_id', 'a.estado')
                        ->where('a.usuario_id', Auth::user()->usuario_id)
                        ->get();
        
      /*  foreach($solicitudes as $solicitud) {

        $solicitudes_usuario = DB::table('solicitud_usuarios as s')
                        ->join('solicitudes as b', 's.solicitud_id', '=', 'b.solicitud_id')
                        ->join('usuarios as u', 's.usuario_id', '=', 'u.usuario_id')
                        ->join('persona as p', 'u.usuario_id', '=', 'p.usuario_id')
                        ->select('b.area_id', 's.usuario_id')
                        ->where('s.solicitud_id', $solicitud->solicitud_id)
                        ->get(); 

        } */

        $solicitudes_format = collect($solicitudes)->map(function($solicitud){
            return [
                'nombre' => $solicitud->nombre . ' ' . $solicitud->ape_materno . ' '. $solicitud->ape_paterno,
                'id' => $solicitud->solicitud_id,
                'estado' => $solicitud->estado,
                'fecha' => $solicitud->created_at
                    ? Carbon::parse($solicitud->created_at)->diffForHumans()
                    : 'No se ha registrado fecha',
            ];
        });

        $solicitudes_array = $solicitudes_format->toArray();




        return view('pages-control.solicitudes.buzon', compact('solicitudes_array'));

    }

}
