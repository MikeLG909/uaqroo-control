<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventosAcceso;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\Auth;


class DashController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $inicio_semana = Carbon::now()->startOfWeek();
        $fin_semana = Carbon::now()->endOfWeek();

        $conteo_hoy = EventosAcceso::whereDate('created_at', $hoy)->count();
        
        $conteo_permitido = EventosAcceso::whereBetween('created_at', [$inicio_semana, $fin_semana])
                                            ->where('permiso', 'PERMITIDO')
                                            ->count();
        
        $conteo_denegado = EventosAcceso::whereBetween('created_at', [$inicio_semana, $fin_semana])
                                            ->where('permiso', 'NO PERMITIDO')
                                            ->count();

        $total = $conteo_permitido + $conteo_denegado;
        $porcentaje_perm = $total == 0 ? 0 : ($conteo_permitido / $total) * 100;
        $porcentaje_deneg = $total == 0 ? 0 : ($conteo_denegado / $total) * 100;
        //limitamos los decimales a solo mostrar 2 con bcdiv
        $porcentaje_perm = bcdiv($porcentaje_perm, '1', 2);
        $porcentaje_deneg = bcdiv($porcentaje_deneg, '1', 2);

        // Crear gráfico semanal 
        $eventos_m = EventosAcceso::accesosSemanalesM();
        $eventos_f = EventosAcceso::accesosSemanalesF();

        $fecha_actual = date('Y-m-d');

        // Mapear nombre de los dias de ingles a español

        $daysMap = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];

        // Inicializar array para que el grafico empiece desde el dia lunes

        $diasSemana = [
            'Lunes' => 0,
            'Martes' => 0,
            'Miércoles' => 0,
            'Jueves' => 0,
            'Viernes' => 0,
            'Sábado' => 0,
            'Domingo' => 0
        ];

        $diasSemanaF = $diasSemana; // Copia para el conteo femenino

        foreach($eventos_m as $access_m) {
            $dia = date('l', strtotime($access_m->date));
            $diaEspanol = $daysMap[$dia];
            if (isset($diasSemana[$diaEspanol])){
                $diasSemana[$diaEspanol] += $access_m->count;
            }
        }
        
        foreach($eventos_f as $access_f) {
            $dia = date('l', strtotime($access_f->date));
            $diaEspanol = $daysMap[$dia];
            if (isset($diasSemanaF[$diaEspanol])){
                $diasSemanaF[$diaEspanol] += $access_f->count;
            }
        }
        
        
         //obtener los dias en el orden correcto 
         
         $diasOrdenados = array_keys($diasSemana);

         //obtener los accesos en el orden correcto

         $dataM = array_values($diasSemana);
         $dataF = array_values($diasSemanaF);

         $chart = (new LarapexChart)->lineChart()
         ->addData('Hombres', $dataM)
         ->addData('Mujeres', $dataF)
         ->setColors(['#0000FF', '#FF00FF']) // Colores azul y rosa
         ->setXAxis($diasOrdenados);
     // Línea con grosor 2
 /*   ->setOptions([
        'chart' => [
            'type' => 'line',
        ],
        'stroke' => [
            'curve' => 'smooth'
        ],
        'fill' => [
            'type' => 'gradient',
            'gradient' => [
                'shadeIntensity' => 1,
                'gradientToColors' => ['#00c6ff', '#ff99cc'], // Azul y rosa gradientes
                'inverseColors' => false,
                'opacityFrom' => 1,
                'opacityTo' => 1,
                'stops' => [0, 100]
            ],
        ]
    ]); */
        
       //dd(Auth::user()->role->rol);
        return view('barebone', compact('conteo_hoy','chart', 'conteo_permitido', 'conteo_denegado', 'porcentaje_perm', 'porcentaje_deneg'));
    }

    public function pollAccessCount(Request $request)
    {
        // definimos la variable de inicio
        $start = time();
        // definimos el tiempo maximo de espera en segundos
        $timeout = 30;
        // variable para obtener el ultimo conteo
        $lastAccessCount = $request->input('lastAccessCount', 0);

        //Iniciar conteo dentro de un ciclo while

        while(true) {
            //obtenemos el conteo actual
            $accessCount = DB::table('eventos_acceso')
                ->whereDate('created_at', Carbon::today())
                ->count();

            if($accessCount > $lastAccessCount) {
                return response()->json(['accessCount' => $accessCount]);
            }

            if ((time() - $start) >= $timeout) {
                return response()->json(['accessCount' => $lastAccessCount]);
            }

            //Dormir medio segundo para volver a verificar

            usleep(500000);
        }

    }

    public function miActividad()
    {
        setlocale(LC_TIME, 'es_ES.UTF-8');
        Carbon::setLocale('es');

        $areas = EventosAcceso::accesosUser();
 
        // mapeado del array areas para convertir el formato de fecha y hora al convencional
        $areasFormateadas = $areas->map(function($area){
            $area->created_at = Carbon::parse($area->created_at)->translatedFormat('j \d\e F \d\e Y h:i A');
            return $area;
        });

        //obtener el último acceso del usuario
        $ultimoAcceso = EventosAcceso::where('usuario_id', Auth::user()->usuario_id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
        
        // formatear el tiempo transcurrido desde el último acceso
        // la funcion de carbon diffForHumans() Formatea la fecha del último acceso para que se muestre como "hace 2 horas" o similar.
        $ultimoAccesoFormat = $ultimoAcceso 
        ? Carbon::parse($ultimoAcceso->created_at)->diffForHumans()
        : 'No se ha registrado ningún acceso';

        return view('dashboard-u', compact('areasFormateadas', 'ultimoAccesoFormat'));
    }
}
