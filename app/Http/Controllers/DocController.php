<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocController extends Controller
{
    public function index()
    {
        try {
            $docs = Doc::latest()->get();

            return view('doc', compact('docs'));
        } catch (Exception $e) {
            report($e);

            session()->now('error', 'No se pudieron cargar los documentos');

            return view('doc', ['docs' => collect()]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'doc' => 'required|file|mimes:pdf|max:5120',
        ]);

        try {
            $path = $request->file('doc')->store('docs', 'public');

            Doc::create([
                'name' => $request->name,
                'doc_path' => $path,
            ]);

            return back()->with('success', 'doc subido correctamente');
        } catch (Exception $e) {
            report($e);

            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()->with('error', 'No se pudo subir el doc');
        }
    }

    public function update(Request $request, Doc $doc)
    {
        $request->validate([
            'doc' => 'required|file|mimes:pdf|max:5120',
        ]);

        try {
            $oldPath = $doc->doc_path;

            $newPath = $request->file('doc')->store('docs', 'public');

            $doc->update([
                'doc_path' => $newPath,
            ]);

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            return back()->with('success', 'doc actualizado');
        } catch (Exception $e) {
            report($e);

            return back()->with('error', 'No se pudo actualizar el doc');
        }
    }

    public function destroy(Doc $doc)
    {
        try {
            if (Storage::disk('public')->exists($doc->doc_path)) {
                Storage::disk('public')->delete($doc->doc_path);
            }

            $doc->delete();

            return back()->with('success', 'doc eliminado');
        } catch (Exception $e) {
            report($e);

            return back()->with('error', 'No se pudo eliminar el doc');
        }
    }
}
