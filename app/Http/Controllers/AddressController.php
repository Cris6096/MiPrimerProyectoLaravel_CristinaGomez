<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Crea y guarda una nueva dirección asociada a un usuario (Relación 1:1).
     * Ruta: POST /api/address
     */
    public function store(Request $request)
    {
        // Ejecutamos los validadores privados para comprobar el email y los datos de la dirección
        $emailValidator = $this->validateEmail($request);
        $addressValidator = $this->validateAddress($request);

        // Si alguna validación falla, devolvemos un JSON con código 400 (Bad Request) y los errores
        if ($emailValidator->fails() || $addressValidator->fails()) {
            return response()->json([
                'message' => 'Failed',
                'email'   => $emailValidator->messages(),
                'address' => $addressValidator->messages(),
            ], 400);
        }

        // Buscamos al usuario por su email; si no existe, lanza error 404 automáticamente (firstOrFail)
        $user = User::where('email', $request->get('email'))->firstOrFail();

        // Comprobamos si el usuario ya tiene una dirección asignada para evitar duplicados en relación 1:1
        if ($user->address) {
            return response()->json([
                'message' => 'Failed, UserAddress already exists',
                'data' => null
            ], 400);
        }

        // Creamos una nueva instancia de Address con los campos ya validados (country y zipcode)
        $address = new Address($addressValidator->validate());

        // Guardamos la dirección en BD vinculándola al usuario a través de su relación Eloquent
        if($user->address()->save($address)){
            return response()->json(['message' => 'Address saved', 'data' => $address], 200);
        }
        return response()->json(['message' => 'Failed', 'data' => null], 400);
    }

    /**
     * Muestra los datos de una dirección específica usando Route Model Binding.
     * Ruta: GET /api/address/{address}
     */
    public function show(Address $address)
    {
        return response()->json(['message' => '', 'data' => $address], 200);
    }

    /**
     * Muestra el usuario al que pertenece una dirección (Relación inversa belongsTo).
     * Ruta: GET /api/address/{address}/user
     */
    public function show_user(Address $address)
    {
        return response()->json(['message' => '', 'data' => $address->user], 200);
    }

    /**
     * Método privado para validar que el email recibido exista en la tabla 'users'.
     */
    private function validateEmail(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
        ]);
    }

    /**
     * Método privado para validar los campos obligatorios de la dirección.
     */
    private function validateAddress(Request $request)
    {
        return Validator::make($request->all(), [
            'country' => 'required|string|max:255',
            'zipcode' => 'required|string|max:20',
        ]);
    }

    /**
     * Devuelve un listado con todas las direcciones registradas en la base de datos.
     * Ruta: GET /api/address
     */
    public function index()
    {
        return response()->json([
            'message' => null,
            'data'    => Address::all()
        ], 200);
    }
}
