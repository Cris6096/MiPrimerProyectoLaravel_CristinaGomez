<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Event;
class EventController extends Controller
{

    public function listUsers(Event $event)
    {
        $users = $event->users;
        return response()->json(['message'=>null,'data'=>$users],200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'event_name'    => 'required|string|max:255',
            'event_detail'  => 'nullable|string',
            'event_type_id' => 'required|exists:event_types,id', // Validamos que exista en la base de datos
        ]);

        $event = new Event($validatedData);

        if (!$event->save()) {
            return response()->json([
                'message' => 'No se pudo crear el evento',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'message' => 'Evento creado con éxito',
            'data'    => $event
        ], 201);
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with('users')->latest()->paginate(10);
        return response()->json([
            'success' => true,
            'data'    => $events
        ], 201);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::findOrFail($id);
        if (!$event) {
            return response()->json([
                'message' => 'Evento no encontrado',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'message' => null,
            'data'    => $event
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
