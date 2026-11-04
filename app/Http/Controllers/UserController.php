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
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        return response()->json(['message'=>'User Created','data'=>$user],200);
    }
    public function show(User $user)
    {
        return response()->json(['message'=>'','data'=>$user],200);
    }
        public function show_address(User $user)
        {
            return response()->json(['message'=>'','data'=>$user->address],200);
    }


    public function bookEvent(Request $request, User $user, Event $event)
    {
        $note = '';
        if($request->note){
            $note = $request->note;
        }
        if($user->events()->save($event, array('note' => $note))){
        return response()->json(['message'=>'User Event Created','data'=>$event],200);
    }
    return response()->json(['message'=>'Error','data'=>null],400);
    }


    public function listEvents(User $user)
    {
        $events = $user->events;
        return response()->json(['message'=>null,'data'=>$events],200);
    }


}
