<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KhachHangController;
use App\Http\Controllers\LichHenController;
use App\Http\Controllers\BaoGiaController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\DanhGiaController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\ThongBaoNhanVienController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeXuatBaoDuongController;


use App\Http\Controllers\Cvdv\DashboardController as CvdvDashboard;
use App\Http\Controllers\Cvdv\PhieuController as CvdvPhieu;
use App\Http\Controllers\Cvdv\PhanCongController;
use App\Http\Controllers\Cvdv\QuanLyPhieuController;
use App\Http\Controllers\Cvdv\BaoGiaController as CvdvBaoGia;
use App\Http\Controllers\Cvdv\HoaDonController as CvdvHoaDon;
use App\Http\Controllers\Cvdv\KhachHangController as CvdvKhachHang;

use App\Http\Controllers\Ktv\PhieuController as KtvPhieu;

use App\Http\Controllers\QuanLy\DashboardController as QuanLyDashboard;
use App\Http\Controllers\QuanLy\TaiKhoanController as QuanLyTaiKhoan;
use App\Http\Controllers\QuanLy\DichVuController as QuanLyDichVu;
use App\Http\Controllers\QuanLy\PhuTungController as QuanLyPhuTung;

// ============================================================
// NHÓM 1 — KHÁCH HÀNG (công khai)
// ============================================================
Route::get('/', [HomeController::class, 'index'])->name('trang-chu-cong-khai');
Route::get('/dich-vu/{nhom?}', [HomeController::class, 'dichVu'])->name('dich-vu');
Route::get('/danh-gia', [HomeController::class, 'danhGia'])->name('danh-gia');
Route::get('/ve-garage', [HomeController::class, 'veGarage'])->name('ve-garage');

Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('dang-nhap');
Route::post('/dang-nhap', [AuthController::class, 'login'])->name('dang-nhap.xu-ly');
Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('dang-ky');
Route::post('/dang-ky', [AuthController::class, 'register'])->name('dang-ky.xu-ly');
Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('dang-xuat');

Route::get('/auth/google/redirect', [AuthController::class, 'chuyenHuongGoogle'])->name('google.chuyen-huong');
Route::get('/auth/google/callback', [AuthController::class, 'xuLyGoogleCallback'])->name('google.callback');

Route::get('/quen-mat-khau', [AuthController::class, 'showQuenMatKhau'])->name('quen-mat-khau');
Route::post('/quen-mat-khau', [AuthController::class, 'guiOtp'])->name('quen-mat-khau.gui-otp');
Route::get('/quen-mat-khau/nhap-otp', [AuthController::class, 'showNhapOtp'])->name('quen-mat-khau.nhap-otp');
Route::post('/quen-mat-khau/nhap-otp', [AuthController::class, 'xacThucOtp'])->name('quen-mat-khau.xac-thuc-otp');
Route::get('/quen-mat-khau/dat-lai', [AuthController::class, 'showDatLaiMatKhau'])->name('quen-mat-khau.dat-lai');
Route::post('/quen-mat-khau/dat-lai', [AuthController::class, 'datLaiMatKhau'])->name('quen-mat-khau.dat-lai.xu-ly');

Route::get('/dat-lich', [LichHenController::class, 'showDatLich'])->name('dat-lich');
Route::post('/dat-lich', [LichHenController::class, 'datLich'])->name('dat-lich.xu-ly');
Route::get('/api/khung-gio', [LichHenController::class, 'khungGioTrong'])->name('dat-lich.khung-gio');

Route::post('/api/chatbot', [ChatbotController::class, 'gui'])->name('api.chatbot');

Route::get('/de-xuat-bao-duong/{xeId}', [DeXuatBaoDuongController::class, 'show'])
    ->name('de-xuat-bao-duong.xem')->middleware('signed');
Route::post('/de-xuat-bao-duong/{xeId}/chap-nhan', [DeXuatBaoDuongController::class, 'chapNhan'])
    ->name('de-xuat-bao-duong.chap-nhan');
Route::post('/de-xuat-bao-duong/{xeId}/tu-choi', [DeXuatBaoDuongController::class, 'tuChoi'])
    ->name('de-xuat-bao-duong.tu-choi');
// --- Khách hàng ĐÃ đăng nhập ---
Route::middleware('auth')->group(function () {
    
    Route::get('/trang-chu', [KhachHangController::class, 'trangChu'])->name('trang-chu');
    Route::get('/ho-so', [KhachHangController::class, 'showHoSo'])->name('ho-so');
    Route::put('/ho-so', [KhachHangController::class, 'capNhatHoSo'])->name('ho-so.cap-nhat');

    Route::get('/lich-hen', [LichHenController::class, 'lichHenCuaToi'])->name('lich-hen');
    Route::get('/lich-hen/{id}/sua', [LichHenController::class, 'showSuaLich'])->name('lich-hen.sua');
    Route::put('/lich-hen/{id}/sua', [LichHenController::class, 'suaLich'])->name('lich-hen.sua.xu-ly');
    Route::post('/lich-hen/{id}/huy', [LichHenController::class, 'huyLich'])->name('lich-hen.huy');

    Route::get('/hoa-don', [KhachHangController::class, 'hoaDonCuaToi'])->name('hoa-don');
    Route::get('/hoa-don/{id}', [KhachHangController::class, 'hoaDonChiTiet'])->name('hoa-don.xem');

    Route::get('/thong-bao', [ThongBaoController::class, 'danhSach'])->name('thong-bao');
    Route::post('/thong-bao/danh-dau-da-xem', [ThongBaoController::class, 'danhDauDaXem'])->name('thong-bao.danh-dau-da-xem');

    Route::get('/xe-cua-toi', [XeController::class, 'danhSach'])->name('xe.danh-sach');
    Route::get('/xe-cua-toi/them', [XeController::class, 'showThemMoi'])->name('xe.them-moi');
    Route::post('/xe-cua-toi/them', [XeController::class, 'themMoi'])->name('xe.them-moi.xu-ly');

    Route::get('/phieu/{id}/danh-gia', [DanhGiaController::class, 'showTaoMoi'])->name('danh-gia.tao-moi');
    Route::post('/phieu/{id}/danh-gia', [DanhGiaController::class, 'luu'])->name('danh-gia.luu');

    Route::get('/bao-gia/{id}', [BaoGiaController::class, 'show'])->name('bao-gia.xem');
    Route::post('/bao-gia/{id}/duyet', [BaoGiaController::class, 'duyet'])->name('bao-gia.duyet');
    Route::post('/bao-gia/{id}/tu-choi', [BaoGiaController::class, 'tuChoi'])->name('bao-gia.tu-choi');
});

// --- Thông báo dùng chung cho CVDV/KTV/Quản lý (chỉ cần đăng nhập, không giới hạn role) ---
Route::post('/nv/thong-bao/danh-dau-da-xem', [ThongBaoNhanVienController::class, 'danhDauDaXem'])
    ->middleware('auth')->name('thong-bao-nv.danh-dau-da-xem');


// ============================================================
// NHÓM 2 — CỐ VẤN DỊCH VỤ (prefix: /cvdv)
// ============================================================
Route::prefix('cvdv')->name('cvdv.')->middleware(['auth', 'role:Co van dich vu'])->group(function () {
    Route::get('/dashboard', [CvdvDashboard::class, 'index'])->name('dashboard');
    Route::post('/lich-hen/{lichHenId}/doi-gio', [CvdvPhieu::class, 'doiGio'])->name('lich-hen.doi-gio');
Route::post('/lich-hen/{lichHenId}/huy-co-ly-do', [CvdvPhieu::class, 'huyCoLyDo'])->name('lich-hen.huy-co-ly-do');
Route::post('/phieu/{id}/huy', [QuanLyPhieuController::class, 'huyPhieu'])->name('phieu.huy');
    // Quản lý lịch hẹn (gộp: xem lịch, tạo phiếu, phân công KTV)
    Route::get('/lich-hen', [CvdvPhieu::class, 'showTaoMoi'])->name('lich-hen');
    Route::get('/phieu/tao-moi', [CvdvPhieu::class, 'showTaoMoi'])->name('phieu.tao-moi');
    Route::get('/phieu/tim-kiem', [CvdvPhieu::class, 'timKiem'])->name('phieu.tim-kiem');
    Route::post('/phieu/phan-loai-ai', [CvdvPhieu::class, 'phanLoaiAI'])->name('phieu.phan-loai-ai');
    Route::post('/phieu/goi-y-hang-muc', [CvdvPhieu::class, 'goiYHangMucSom'])->name('phieu.goi-y-hang-muc');
    Route::post('/phieu/goi-y-ktv', [CvdvPhieu::class, 'goiYKTV'])->name('phieu.goi-y-ktv');
    Route::post('/phieu/luu', [CvdvPhieu::class, 'taoMoi'])->name('phieu.luu');

    Route::get('/phan-cong', [PhanCongController::class, 'danhSach'])->name('phan-cong');
    Route::post('/phan-cong/goi-y-ai', [PhanCongController::class, 'goiYAI'])->name('phan-cong.goi-y-ai');
    Route::post('/phan-cong/{phieuId}/xu-ly', [PhanCongController::class, 'phanCong'])->name('phan-cong.xu-ly');

    // Quản lý phiếu sửa chữa (danh sách + chi tiết, có tìm kiếm + lọc)
    Route::get('/phieu', [QuanLyPhieuController::class, 'danhSach'])->name('phieu.danh-sach');
    Route::get('/phieu/{id}', [QuanLyPhieuController::class, 'chiTiet'])->name('phieu.chi-tiet');
    Route::post('/bao-gia/{baoGiaId}/duyet-thay-khach', [QuanLyPhieuController::class, 'duyetThayKhach'])->name('bao-gia.duyet-thay-khach');

    // Lập báo giá (AI)
    Route::get('/bao-gia/{phieuId}/tao-moi', [CvdvBaoGia::class, 'showTaoMoi'])->name('bao-gia.tao-moi');
    Route::post('/bao-gia/{phieuId}/goi-y-ai', [CvdvBaoGia::class, 'goiYAI'])->name('bao-gia.goi-y-ai');
    Route::post('/bao-gia/{phieuId}/luu', [CvdvBaoGia::class, 'luu'])->name('bao-gia.luu');

    // Lập hóa đơn
    Route::get('/hoa-don/{phieuId}/tao-moi', [CvdvHoaDon::class, 'showTaoMoi'])->name('hoa-don.tao-moi');
    Route::post('/hoa-don/{phieuId}/luu', [CvdvHoaDon::class, 'luu'])->name('hoa-don.luu');
    Route::post('/hoa-don/{hoaDonId}/danh-dau-thanh-toan', [CvdvHoaDon::class, 'danhDauDaThanhToan'])->name('hoa-don.danh-dau-thanh-toan');
    Route::get('/khach-hang/{id}/sua', [CvdvKhachHang::class, 'showSua'])->name('khach-hang.sua');
Route::put('/khach-hang/{id}/sua', [CvdvKhachHang::class, 'sua'])->name('khach-hang.sua.xu-ly');
    Route::get('/khach-hang', [CvdvKhachHang::class, 'danhSach'])->name('khach-hang');
Route::get('/khach-hang/{id}', [CvdvKhachHang::class, 'chiTiet'])->name('khach-hang.chi-tiet');
});


// ============================================================
// NHÓM 3 — KỸ THUẬT VIÊN (prefix: /ktv)
// ============================================================
Route::prefix('ktv')->name('ktv.')->middleware(['auth', 'role:Ky thuat vien'])->group(function () {
    Route::get('/phieu', [KtvPhieu::class, 'danhSach'])->name('phieu');
    Route::get('/phieu/{id}', [KtvPhieu::class, 'chiTiet'])->name('phieu.chi-tiet');
    Route::put('/phieu/{id}', [KtvPhieu::class, 'capNhat'])->name('phieu.cap-nhat');
    Route::post('/phieu/{id}/hoan-thanh', [KtvPhieu::class, 'danhDauHoanThanh'])->name('phieu.hoan-thanh');
    Route::post('/phieu/goi-y-ai', [KtvPhieu::class, 'goiYAI'])->name('phieu.goi-y-ai');
});


// ============================================================
// NHÓM 4 — QUẢN LÝ (prefix: /quan-ly)
// ============================================================
Route::prefix('quan-ly')->name('quan-ly.')->middleware(['auth', 'role:Quan ly'])->group(function () {
    Route::get('/dashboard', [QuanLyDashboard::class, 'index'])->name('dashboard');

    Route::get('/tai-khoan', [QuanLyTaiKhoan::class, 'danhSach'])->name('tai-khoan');
    Route::get('/tai-khoan/tao-moi', [QuanLyTaiKhoan::class, 'showTaoMoi'])->name('tai-khoan.tao-moi');
    Route::post('/tai-khoan/tao-moi', [QuanLyTaiKhoan::class, 'taoMoi'])->name('tai-khoan.tao-moi.xu-ly');
    Route::post('/tai-khoan/{id}/doi-trang-thai', [QuanLyTaiKhoan::class, 'doiTrangThai'])->name('tai-khoan.doi-trang-thai');

    Route::get('/dich-vu', [QuanLyDichVu::class, 'danhSach'])->name('dich-vu');
    Route::get('/dich-vu/tao-moi', [QuanLyDichVu::class, 'showTaoMoi'])->name('dich-vu.tao-moi');
    Route::post('/dich-vu/tao-moi', [QuanLyDichVu::class, 'taoMoi'])->name('dich-vu.tao-moi.xu-ly');
    Route::get('/dich-vu/{id}/sua', [QuanLyDichVu::class, 'showSua'])->name('dich-vu.sua');
    Route::put('/dich-vu/{id}/sua', [QuanLyDichVu::class, 'sua'])->name('dich-vu.sua.xu-ly');
    Route::post('/dich-vu/{id}/doi-trang-thai', [QuanLyDichVu::class, 'doiTrangThai'])->name('dich-vu.doi-trang-thai');
    Route::get('/dashboard/xuat-csv', [QuanLyDashboard::class, 'xuatCsv'])->name('dashboard.xuat-csv');
    Route::get('/phu-tung', [QuanLyPhuTung::class, 'danhSach'])->name('phu-tung');
    Route::get('/phu-tung/tao-moi', [QuanLyPhuTung::class, 'showTaoMoi'])->name('phu-tung.tao-moi');
    Route::post('/phu-tung/tao-moi', [QuanLyPhuTung::class, 'taoMoi'])->name('phu-tung.tao-moi.xu-ly');
    Route::post('/phu-tung/{id}/nhap-kho', [QuanLyPhuTung::class, 'nhapKho'])->name('phu-tung.nhap-kho');
    Route::get('/phu-tung/{id}/lich-su', [QuanLyPhuTung::class, 'lichSu'])->name('phu-tung.lich-su');
Route::post('/phu-tung/{id}/doi-trang-thai', [QuanLyPhuTung::class, 'doiTrangThai'])->name('phu-tung.doi-trang-thai');
});