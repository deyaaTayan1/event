<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    
    public function index()
    {
        $events = Event::all();
        return EventResource::collection($events->load('user' , 'attendees'));
        // return response()->json($events , 200);
    }

    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100', 'unique:events,name'],
            'description' => ['nullable', 'string', 'max:300'],
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
        ]);
        

        $event = Event::create([
            'user_id' => 1 ,
            ...$validatedData
        ]);

        return response()->json([
            'message' => 'event created successfully' ,
            'event' =>  new EventResource($event->load('user' , 'attendees'))
        ],201);
    }

    public function show(Event $event)
    {
        return $event ;
    }

    public function update(Request $request, Event $event)
    {
        $validatedData = $request->validate([
            'name' => ['sometimes', 'string', 'min:3', 'max:100', 'unique:events,name,'.$event->id],
            'description' => ['nullable', 'string', 'max:300'],
            'start_time' => ['sometimes', 'date', 'after_or_equal:now'],
            'end_time' => ['sometimes', 'date', 'after:start_time'],
        ]);

        $event->update($validatedData);
        
        $event->load('user' , 'attendees');
        return new EventResource($event);
    }

    
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'event deleted successfully'],200);
    }
}


