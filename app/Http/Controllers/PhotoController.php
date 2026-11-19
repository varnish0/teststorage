<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Photo;
use App\Http\Controlles\Exception;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::latest()->get();

        return view('photos', compact('photos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:256',
            'image' => 'required|image|mimes:jpg,jpeg,png,web|max:2048',
        ]);

        try
        {
            $path = $request->file('image')->store('photos', 'public');
            
             Photo::create([
            'name' => $request->name,
            'image_path' => $path
            ]);
            
            return back()->with('success', 'img subida correctamente');

        }
        catch(Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }}

    public function update(Request $request, Photo $photo)
    {
       $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,web|max:2048',
        ]);

        try{
            if(Storage::disk('public')->exists($photo->image_path))
            {
            Storage::disk('public')->delete($photo->image_path);
        }

        $newPath = $request->file('image')->store('photos', 'public');

        $photo->update([
            'image_path' => $newPath
        ]);

        return back()->with('success', 'img actualizada');
    }
    catch(Exception $e)
    {
        return back()->with('error', $e->getMessage());

    }

        }

        
    public function destroy (Photo $photo)
    {
    try
    {
        if(Storage::disk('public')->exists($photo->image_path))
        {
        Storage::disk('public')->delete($photo->image_path);
        }

        $photo->delete();

        return back()->with('success', 'img elmininada');
    }
    catch(Exception $e)
    {
        return back()->with('error', $e->getMessage());

    }}

}
