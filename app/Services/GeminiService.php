<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $caCertPath = 'D:\wamp64\bin\php\php8.0.30\extras\ssl\cacert.pem';

    /**
     * Gọi Gemini API, ép trả về đúng JSON theo schema mong muốn.
     * Đã tắt "thinking" (thinkingBudget: 0) — task phân loại/gợi ý đơn giản
     * không cần model tự suy luận nội bộ, giúp phản hồi nhanh hơn đáng kể.
     */
    public function goiJSON(string $systemPrompt, string $userPrompt, array $responseSchema): array
    {
        $response = Http::timeout(30)
            ->withOptions([
                'verify' => $this->caCertPath,
            ])
            ->withHeaders([
                'x-goog-api-key' => config('services.gemini.api_key'),
            ])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.model') . ':generateContent', [
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => [['parts' => [['text' => $userPrompt]]]],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $responseSchema,
                ],
            ]);

        if (!$response->successful()) {

    \Log::error('GEMINI PHAN LOAI LOI', [
        'status' => $response->status(),
        'body' => $response->body(),
        'json' => $response->json(),
    ]);

    return [
        'loi' => 'Gemini API lỗi',
        'status' => $response->status(),
        'body' => $response->body(),
    ];
}

        $text = $response->json('candidates.0.content.parts.0.text');

        if (!$text) {
            return ['loi' => 'Gemini khong tra ve noi dung'];
        }

        return json_decode($text, true) ?? ['loi' => 'Khong the doc JSON tra ve'];
    }

    public function phanLoaiSuCo(string $moTaSuCo): array
    {
        return $this->goiJSON(
            "Bạn là trợ lý phân loại sự cố ô tô cho garage. Dựa vào mô tả sự cố của khách hàng, 
            hãy xác định NHÓM SỰ CỐ phù hợp nhất (VD: 'Hệ thống phanh', 'Động cơ', 'Hệ thống điện', 
            'Hộp số', 'Hệ thống treo/gầm', 'Điều hòa', 'Khác') và mức độ tin cậy (0-100).
            Trả lời NHANH, không cần giải thích dài dòng.",
            "Mô tả sự cố: \"$moTaSuCo\"",
            [
                'type' => 'object',
                'properties' => [
                    'nhomSuCo' => ['type' => 'string'],
                    'doTinCay' => ['type' => 'number'],
                ],
                'required' => ['nhomSuCo', 'doTinCay'],
            ]
        );
    }

    public function goiYUuTien(array $phieuChoXuLy, array $danhSachKTV, array $taiCongViec): array
    {
        return $this->goiJSON(
            "Bạn là trợ lý điều phối kỹ thuật viên cho garage ô tô.
            Dựa trên danh sách phiếu đang chờ, danh sách kỹ thuật viên (chuyên môn), và tải công việc 
            hiện tại của từng người, hãy: 1) Xếp thứ tự ưu tiên xử lý (Cao/Trung bình/Thấp) — sự cố 
            liên quan an toàn (phanh, lái) ưu tiên cao hơn; 2) Đề xuất kỹ thuật viên phù hợp nhất cho 
            từng phiếu, kèm lý do ngắn gọn. Trả lời NHANH, không cần giải thích dài dòng.",
            json_encode(compact('phieuChoXuLy', 'danhSachKTV', 'taiCongViec')),
            [
                'type' => 'object',
                'properties' => [
                    'thuTuUuTien' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'phieuId' => ['type' => 'number'],
                                'mucDoUuTien' => ['type' => 'string'],
                            ],
                        ],
                    ],
                    'ktvDeXuat' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'phieuId' => ['type' => 'number'],
                                'ktvId' => ['type' => 'number'],
                                'lyDo' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
                'required' => ['thuTuUuTien', 'ktvDeXuat'],
            ]
        );
    }

    public function goiYBaoGia(string $ketQuaChanDoan, ?string $nhomSuCo, array $danhMucDichVu): array
    {
        return $this->goiJSON(
            "Bạn là trợ lý gợi ý báo giá sửa chữa ô tô cho garage.
            Dựa vào kết quả chẩn đoán của kỹ thuật viên, hãy chọn ra các hạng mục dịch vụ PHÙ HỢP NHẤT 
            từ danh mục dịch vụ garage đang cung cấp (chỉ chọn trong danh sách được cung cấp), kèm lý do 
            ngắn gọn cho từng hạng mục, và độ tin cậy tổng thể (0-100). Trả lời NHANH.",
            json_encode(compact('ketQuaChanDoan', 'nhomSuCo', 'danhMucDichVu')),
            [
                'type' => 'object',
                'properties' => [
                    'hangMuc' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'tenDichVu' => ['type' => 'string'],
                                'lyDo' => ['type' => 'string'],
                            ],
                        ],
                    ],
                    'doTinCay' => ['type' => 'number'],
                ],
                'required' => ['hangMuc', 'doTinCay'],
            ]
        );
    }

    public function nhacBaoDuong(array $lichSuDichVu): array
    {
        $homNay = now()->format('Y-m-d');

        return $this->goiJSON(
            "Bạn là trợ lý nhắc bảo dưỡng ô tô chủ động.
            Dựa vào lịch sử các dịch vụ đã thực hiện (tên dịch vụ, ngày thực hiện gần nhất, chu kỳ 
            khuyến nghị tính bằng tháng), hãy xác định dịch vụ nào SẮP ĐẾN HẠN sớm nhất tính đến hôm 
            nay ($homNay). Nếu có dịch vụ sắp đến hạn (dưới 7 ngày hoặc đã quá hạn), trả về 
            coDeXuat=true kèm tên dịch vụ và giải thích ngắn gọn, thân thiện. Nếu chưa có, trả về 
            coDeXuat=false. Trả lời NHANH.",
            json_encode($lichSuDichVu),
            [
                'type' => 'object',
                'properties' => [
                    'coDeXuat' => ['type' => 'boolean'],
                    'dichVuSapDenHan' => ['type' => 'string'],
                    'giaiThich' => ['type' => 'string'],
                ],
                'required' => ['coDeXuat'],
            ]
        );
    }

    /**
     * UC-15 — Chatbot tư vấn (đã tắt thinking để phản hồi nhanh hơn)
     */
    public function chatbot(string $tinNhan, array $danhSachDichVu = []): string
    {
        $tenDichVu = collect($danhSachDichVu)->pluck('ten_dich_vu')->implode(', ');

        $response = Http::timeout(30)
            ->withOptions([
                'verify' => $this->caCertPath,
            ])
            ->withHeaders([
                'x-goog-api-key' => config('services.gemini.api_key'),
            ])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/' . config('services.gemini.model') . ':generateContent', [
                'system_instruction' => ['parts' => [['text' =>
                    "Bạn là trợ lý ảo của garage ô tô AutoCare. Trả lời NGẮN GỌN, thân thiện bằng tiếng Việt,
                    không quá 3-4 câu. Có thể: trả lời câu hỏi chung (giờ mở cửa 8:00-20:00, địa chỉ...), 
                    tư vấn sơ bộ vấn đề xe, ước lượng giá THAM KHẢO dựa trên danh mục dịch vụ garage cung cấp 
                    (LUÔN kèm câu 'giá chính thức sẽ được xác nhận sau khi kiểm tra trực tiếp tại garage').
                    Danh mục dịch vụ garage đang có: $tenDichVu.
                    KHÔNG tự thực hiện thay đổi dữ liệu, chỉ hướng dẫn khách tự thao tác nếu cần."
                ]]],
                'contents' => [['parts' => [['text' => $tinNhan]]]],
                'generationConfig' => [
                ],
            ]);

        if (!$response->successful()) {
            return 'Xin lỗi, trợ lý đang bận. Vui lòng thử lại sau ít phút.';
        }

        return $response->json('candidates.0.content.parts.0.text') ?? 'Xin lỗi, tôi chưa hiểu câu hỏi này.';
    }
}