<?php

namespace App\Http\Controllers;
use App\Models\berita;
use App\Models\testimoni;
use App\Models\galeri;
use App\Models\program;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function count()
    {
        return response()->json([
            'berita' => [
                'total' => berita::count()
            ],
            'testimoni' => [
                'total' => testimoni::count()
            ],
            'galeri' => [
                'total' => galeri::count()
            ],
            'program' => [
                'total' => program::count()
            ]
        ]);
    }
}
