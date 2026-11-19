<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        try {
            $photos = Photo::latest()->get();

            return view('photos', compact('photos'));
        } catch (Exception $e) {
            report($e);

            session()->now('error', 'No se pudieron cargar las imgs');

            return view('photos', ['photos' => collect()]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $path = $request->file('image')->store('photos', 'public');

            Photo::create([
                'name' => $request->name,
                'image_path' => $path,
            ]);

            return back()->with('success', 'img subida correctamente');
        } catch (Exception $e) {
            report($e);

            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()->with('error', 'No se pudo subir la img');
        }
    }

    public function update(Request $request, Photo $photo)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $oldPath = $photo->image_path;

            $newPath = $request->file('image')->store('photos', 'public');

            $photo->update([
                'image_path' => $newPath,
            ]);

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            return back()->with('success', 'img actualizada');
        } catch (Exception $e) {
            report($e);

            return back()->with('error', 'No se pudo actualizar la img');
        }
    }

    public function destroy(Photo $photo)
    {
        try {
            if (Storage::disk('public')->exists($photo->image_path)) {
                Storage::disk('public')->delete($photo->image_path);
            }

            $photo->delete();

            return back()->with('success', 'img eliminada');
        } catch (Exception $e) {
            report($e);

            return back()->with('error', 'No se pudo eliminar la img');
        }
    }
}
