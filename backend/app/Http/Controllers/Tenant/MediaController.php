<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Media;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Mostrar galería de medios.
     */
    public function index(Request $request): View
    {
        $query = Media::query()->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('filename', 'like', "%{$request->search}%");
        }

        $media = $query->paginate(24);
        $tenant = $request->attributes->get('tenant');

        return view('tenant.media.index', compact('media', 'tenant'));
    }

    /**
     * Subir archivo(s).
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg,pdf|max:5120', // 5MB max
        ]);

        $tenant = $request->attributes->get('tenant');
        $uploadPath = "tenants/{$tenant->id}/media";
        $uploaded = [];

        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();

            // Generar nombre único
            $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) 
                . '-' . Str::random(8) 
                . '.' . $extension;

            // Guardar archivo
            $path = $file->storeAs($uploadPath, $filename, 'public');

            // Determinar tipo
            $type = str_starts_with($mimeType, 'image/') ? 'image' : 'document';

            // Crear registro en BD
            $media = Media::create([
                'filename' => $originalName,
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'mime_type' => $mimeType,
                'size' => $size,
                'type' => $type,
            ]);

            $uploaded[] = [
                'id' => $media->id,
                'filename' => $media->filename,
                'url' => $media->url,
                'type' => $media->type,
            ];
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploaded) . ' archivo(s) subido(s).',
                'data' => $uploaded,
            ]);
        }

        return back()->with('status', count($uploaded) . ' archivo(s) subido(s) exitosamente.');
    }

    /**
     * Mostrar detalles de un archivo.
     */
    public function show(Media $media): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $media,
        ]);
    }

    /**
     * Actualizar metadatos del archivo.
     */
    public function update(Request $request, Media $media): JsonResponse|RedirectResponse
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
        ]);

        $media->update([
            'alt_text' => $request->alt_text,
            'title' => $request->title,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Archivo actualizado.',
                'data' => $media,
            ]);
        }

        return back()->with('status', 'Archivo actualizado.');
    }

    /**
     * Eliminar archivo.
     */
    public function destroy(Request $request, Media $media): JsonResponse|RedirectResponse
    {
        // Eliminar archivo físico
        if ($media->path && Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Archivo eliminado.',
            ]);
        }

        return back()->with('status', 'Archivo eliminado.');
    }

    /**
     * Selector de medios (modal/popup).
     */
    public function picker(Request $request): View
    {
        $query = Media::query()
            ->where('type', 'image')
            ->latest();

        if ($request->filled('search')) {
            $query->where('filename', 'like', "%{$request->search}%");
        }

        $media = $query->paginate(20);
        $tenant = $request->attributes->get('tenant');

        return view('tenant.media.picker', compact('media', 'tenant'));
    }

    /**
     * Subir desde URL externa.
     */
    public function uploadFromUrl(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $tenant = $request->attributes->get('tenant');
        $uploadPath = "tenants/{$tenant->id}/media";

        try {
            $contents = file_get_contents($request->url);
            
            if (!$contents) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo descargar el archivo.',
                ], 400);
            }

            // Detectar extensión y mime type
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($contents);

            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/svg+xml' => 'svg',
            ];

            if (!isset($extensions[$mimeType])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de archivo no soportado.',
                ], 400);
            }

            $extension = $extensions[$mimeType];
            $filename = 'url-import-' . Str::random(12) . '.' . $extension;
            $path = $uploadPath . '/' . $filename;

            Storage::disk('public')->put($path, $contents);

            $media = Media::create([
                'filename' => basename(parse_url($request->url, PHP_URL_PATH)) ?: $filename,
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
                'mime_type' => $mimeType,
                'size' => strlen($contents),
                'type' => 'image',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen importada.',
                'data' => $media,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al importar: ' . $e->getMessage(),
            ], 500);
        }
    }
}
