<?php

namespace App\Http\Controllers;

use App\Models\program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    public function index()
    {
        $data = program::all();

        $data->map(function ($item) {
            $item->media_url = $item->media ? url('storage/' . $item->media) : null;
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Data program berhasil diambil',
            'jumlah' => $data->count(),
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = program::find($id);

        if (!$data) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        $data->media_url = $data->media ? url('storage/' . $data->media) : null;
        return response()->json(['status' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'        => 'required|string|max:255',
            'deskripsi'   => 'required',
            'tanggal'     => 'nullable|date',
            'tipe_media'  => 'required|in:gambar,vidio',
            'media'       => 'required|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:102400',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        if (!$request->hasFile('media')) {
            return response()->json([
                'status' => false,
                'message' => 'File media wajib dikirim'
            ], 400);
        }
        $folder = 'program/' . date('Y') . '/' . date('m') . '/' . date('d');
        $path = $request->file('media')->store($folder, 'public');
        $validatedData['media'] = $path;

        $data = program::create($validatedData);
        $data->media_url = url('storage/' . $data->media);
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil ditambahkan',
            'data' => $data,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = Program::find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama'        => 'nullable|string|max:255',
            'deskripsi'   => 'nullable|string',
            'tanggal'     => 'nullable|date',
            'tipe_media'  => 'nullable|in:gambar,vidio',
            'media'       => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:102400',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        /* ======================
     | HANDLE FILE UPLOAD
     ====================== */
        if ($request->hasFile('media')) {

            // hapus file lama
            if ($data->media) {
                Storage::disk('public')->delete($data->media);
            }

            $folder = 'program/' . date('Y/m/d');
            $path = $request->file('media')->store($folder, 'public');

            // SIMPAN PATH SAJA
            $validated['media'] = $path;
        }

        $data->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diperbarui',
            'data' => [
                ...$data->toArray(),
                'media_url' => $data->media
                    ? url('storage/' . $data->media)
                    : null
            ]
        ]);
    }

    public function destroy($id)
    {
        $data = program::find($id);

        if (!$data) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        if ($data->media) {
            $oldPath = str_replace(url('storage') . '/', '', $data->media);
            Storage::disk('public')->delete($oldPath);
        }

        $data->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }
}
