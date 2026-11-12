<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Address;
use App\Models\User;
use App\Models\Event;

class UserController extends Controller
{
    /**
     * Registra un nuevo usuario validando los datos y encriptando su contraseña.
     * Ruta: POST /api/register (habitualmente)
     */
    public function register(Request $request)
    {
        // Validamos los campos; 'unique:users' evita emails repetidos y 'same:password' verifica la confirmación
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'c_password' => 'required|same:password',
        ]);

        // Si la validación falla, devolvemos los mensajes de error con código 400
        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }

        // Creamos el registro en la tabla users usando Hash::make() para cifrar la contraseña
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json(['message'=>'User Created','data'=>$user], 200);
    }

    /**
     * Muestra los datos de un usuario específico mediante Route Model Binding.
     * Ruta: GET /api/user/{user}
     */
    public function show(User $user)
    {
        return response()->json(['message'=>'','data'=>$user], 200);
    }

    /**
     * Muestra la dirección asociada a un usuario concreto (Relación 1:1 hasOne).
     * Ruta: GET /api/user/{user}/address
     */
    public function show_address(User $user)
    {
        return response()->json(['message'=>'','data'=>$user->address], 200);
    }

    /**
     * Inscribe un usuario en un evento (Relación Muchos a Muchos / Many-to-Many)
     * guardando un campo extra ('note') en la tabla pivote.
     * Ruta: POST /api/user/{user}/event/{event}
     */
    public function bookEvent(Request $request, User $user, Event $event)
    {
        // Si llega una nota en la petición la guardamos; si no, queda como cadena vacía
        $note = '';
        if($request->note){
            $note = $request->note;
        }

        // Guardamos la relación en la tabla pivote pasando como segundo argumento el array con datos extra
        if($user->events()->save($event, array('note' => $note))){
            return response()->json(['message'=>'User Event Created','data'=>$event], 200);
        }
        return response()->json(['message'=>'Error','data'=>null], 400);
    }

    /**
     * Lista todos los eventos a los que está inscrito un usuario (Relación N:M).
     * Ruta: GET /api/user/{user}/events
     */
    public function listEvents(User $user)
    {
        // Accedemos a la colección de eventos relacionados mediante la propiedad dinámica
        $events = $user->events;
        return response()->json(['message'=>null,'data'=>$events], 200);
    }
}
