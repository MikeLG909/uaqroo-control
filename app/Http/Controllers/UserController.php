<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;
use App\Mail\UsersQr;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Persona;
use App\Models\Area;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;



class UserController extends Controller
{
    public function index()
    {
        $users = User::with('persona')->get();


        return view('pages-control.users', compact('users'));
    }

    public function generarQr(String $id)
    {
        $user = User::find($id);
        $user_email = $user->email;
        //encriptacion usando la funcioon oppenssl_encrypt para aes 256
        $key = env('ENCRYPTION_KEY');

        //vector de inicialización

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        //encriptar el email
        $encriptacion = openssl_encrypt($user_email, 'aes-256-cbc', $key, 0, $iv);

        //concatenar el vector de inicializacion y el email cifrado
        $correo_encriptado = base64_encode($iv . $encriptacion);

        $pathToImage = public_path('images/uqroo.png');

        $qrCode = QrCode::size(200)->merge($pathToImage, 0.3, true)->generate($correo_encriptado);
        return view('qrcode', compact('qrCode', 'user'));
    }

    public function generarQrUser()
    {
        $user = User::userIn();

        $user_email = $user->email;
        //encriptacion usando la funcioon oppenssl_encrypt para aes 256
        $key = env('ENCRYPTION_KEY');

        //vector de inicialización

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        //encriptar el email
        $encriptacion = openssl_encrypt($user_email, 'aes-256-cbc', $key, 0, $iv);

        //concatenar el vector de inicializacion y el email cifrado
        $correo_encriptado = base64_encode($iv . $encriptacion);

        $pathToImage = public_path('images/uqroo.png');

        $qrCode = QrCode::size(200)->merge($pathToImage, 0.3, true)->generate($correo_encriptado);
        return view('qrcode', compact('qrCode', 'user'));
    }
    
    public function enviarMailsQr()
    {
        //Obtener los datos de todos los usuarios
        $users = User::all();

        //enviar correo a cada usuario dentro de la base de datos
        foreach ($users as $user) {
            //encrypytar el emeail del usuario, posteriormente se desencriptará en el esp32
            $correo_encriptado = Crypt::encryptString($user->email);

            //generar códigos qr
            $qrCode = QrCode::size(200)->generate($correo_encriptado);

            //mandar correos pasando los datos al metodo constructor ya creado
            Mail::to($user->email)->queue(new UsersQr($user, $qrCode));
        }
        Alert::success('Éxito', 'Correos enviados a todos los usuarios');
    
        return redirect()->back();

    }

    public function userInfo()
    {
        $user = User::userIn();
        
        $user_email = $user->email;

        $sql = DB::table('areas')
        ->join('autorizaciones_usuarios', 'areas.area_id', '=', 'autorizaciones_usuarios.area_id')
        ->select('area.nombre', 'autorizaciones_usuario.expires_at')
        ->where('autorizaciones_usuario.usuario_id', Auth::user()->usuario_id);


        //encriptacion usando la funcioon oppenssl_encrypt para aes 256
        $key = env('ENCRYPTION_KEY');

        //vector de inicialización

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        //encriptar el email
        $encriptacion = openssl_encrypt($user_email, 'aes-256-cbc', $key, 0, $iv);

        //concatenar el vector de inicializacion y el email cifrado
        $correo_encriptado = base64_encode($iv . $encriptacion);
        $correo_encriptado = str_replace('/', '_', $correo_encriptado);


        $pathToImage = public_path('images/uqroo.png');

        $qrCode = QrCode::size(200)->generate($correo_encriptado);



        return view('pages-control.user.user-info', compact('user', 'qrCode'));
    }

    public function downloadQr()
    {
        $user = User::userIn();
        
        $user_email = $user->email;
        //encriptacion usando la funcioon oppenssl_encrypt para aes 256
        $key = env('ENCRYPTION_KEY');

        //vector de inicialización

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        //encriptar el email
        $encriptacion = openssl_encrypt($user_email, 'aes-256-cbc', $key, 0, $iv);

        //concatenar el vector de inicializacion y el email cifrado
        $correo_encriptado = base64_encode($iv . $encriptacion);
        $correo_encriptado = str_replace('/', '_', $correo_encriptado);

        $pathToImage = public_path('images/uqroo.png');

        $qrCode = QrCode::format('png')->generate($correo_encriptado);

        return response($qrCode)->header('Content-type', 'image/png');
    }

    public function crearAdmin()
    {
        return view('pages-control.user.admin_form');
    }

    public function nuevoAdmin(Request $request)
    {
        $faker = Faker::create();

        $user = new User;

        $username = $faker->unique()->userName;

        $user->nombre_usuario = $username;
        $user->email = $request->correo;
        $user->password = Hash::make($request->contrasena);
        $user->rol_id = 2;
        $user->save();

        $userId = $user->usuario_id;

        $persona = new Persona;
        $persona->usuario_id = $userId;
        $persona->nombre = $request->nombre;
        $persona->ape_materno = $request->apeMaterno;
        $persona->ape_paterno = $request->apePaterno;
        $persona->sexo = $request->genero;
        $persona->save();

        $personaId = $persona->persona_id;
        $user->persona_id = $personaId;

        $user->save();

        return redirect()->back()->with('success', 'Nuevo administrador registrado');

    }

    public function nuevoProfe(Request $request)
    {
        $faker = Faker::create();

        $user = new User;

        $username = $faker->unique()->userName;

        $user->nombre_usuario = $username;
        $user->email = $request->correo;
        $user->password = Hash::make($request->contrasena);
        $user->rol_id = 3;
        $user->save();

        $userId = $user->usuario_id;

        $persona = new Persona;
        $persona->usuario_id = $userId;
        $persona->nombre = $request->nombre;
        $persona->ape_materno = $request->apeMaterno;
        $persona->ape_paterno = $request->apePaterno;
        $persona->sexo = $request->genero;
        $persona->save();

        $personaId = $persona->persona_id;
        $user->persona_id = $personaId;

        $user->save();

    }
}
