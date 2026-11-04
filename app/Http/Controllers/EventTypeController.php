<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    public function listEvents(EventType $type){
        $events = $type->events;
        return response()->json(['message'=>null,'data'=>$events],200);
    }

    public function store (Request $request){
        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
        ]);

        $eventType = new EventType($validatedData);

        if (!$eventType->save()) {
            return response()->json([
                'message' => 'No se pudo crear el tipo de evento',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'message' => 'Tipo de evento creado con éxito',
            'data'    => $eventType
        ], 201);
    }

    public function update(Request $request, EventType $type)
    {
        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
        ]);

        if (!$type->update($validatedData)) {
            return response()->json([
                'message' => 'No se pudo actualizar el tipo de evento',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'message' => 'Tipo de evento actualizado con éxito',
            'data'    => $type
        ], 201);
    }


    public function destroy(EventType $type)
    {
        if (!$type->delete()) {
            return response()->json([
                'message' => 'No se pudo eliminar el tipo de evento',
                'data'    => null
            ], 500);
        }

        return response()->json([
            'message' => 'Tipo de evento eliminado con éxito',
            'data'    => null
        ], 201);
    }
}
