<?php

namespace App\Http\Controllers;

use App\Models\Tentangkami;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class TentangkamiController extends Controller
{

    public function index()
    {
        $data = Tentangkami::all();
        return response()->json([
            'status' => true,
            'message' => 'Data Tentang Kami berhasil diambil',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $data = Tentangkami::find($id);
        if (!$data) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['status' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        try {
            /* ======================
         | VALIDATION
         ====================== */
            $validator = Validator::make($request->all(), [
                'nama'        => 'required|string|max:255',
                'email'       => 'nullable|email',
                'telepon'     => 'nullable|string|max:20',
                'instagram'   => 'nullable|string|max:255',
                'alamat'      => 'nullable|string',
                'sejarah'     => 'nullable|string',
                'visi'        => 'nullable|string',
                'misi'        => 'nullable|string',
                'program'     => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            /* ======================
         | SINGLETON DATA
         ====================== */
            $validated = $validator->validated();

            // Ambil 1 data saja
            $data = Tentangkami::first();

            if ($data) {
                // UPDATE
                $data->update($validated);

                return response()->json([
                    'status'  => true,
                    'message' => 'Data berhasil diperbarui',
                    'data'    => $data,
                ]);
            }

            // CREATE (jika belum ada)
            $data = Tentangkami::create($validated);

            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil ditambahkan',
                'data'    => $data,
            ]);
        } catch (QueryException $e) {
            // Error database (constraint, dll)
            Log::error('TentangKami DB Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Throwable $e) {
            // Error umum
            Log::error('TentangKami Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Tentangkami::find($id);
        if (!$data) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $data->update($request->all());

        return response()->json(['status' => true, 'message' => 'Data berhasil diperbarui', 'data' => $data]);
    }


    public function destroy($id)
    {
        $data = Tentangkami::find($id);
        if (!$data) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        $data->delete();

        return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
    }
}
