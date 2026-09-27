<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatbotController extends Controller
{
    public function gui(Request $request, GeminiService $gemini)
    {
        $request->validate(['tin_nhan' => 'required|string|max:500']);

        $danhSachDichVu = DB::table('DichVu')
            ->where('trang_thai', 'Hoat dong')
            ->select('ten_dich_vu', 'don_gia')
            ->get()
            ->toArray();

        $traLoi = $gemini->chatbot($request->tin_nhan, $danhSachDichVu);

        return response()->json(['tra_loi' => $traLoi]);
    }
}