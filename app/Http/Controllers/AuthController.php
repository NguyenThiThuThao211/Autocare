<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('trang-chu');
        }
        return view('auth.dang-nhap');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email_sdt' => 'required|string',
            'password'  => 'required|string',
        ], [
            'email_sdt.required' => 'Vui lòng nhập số điện thoại hoặc email',
            'password.required'  => 'Vui lòng nhập mật khẩu',
        ]);

        if (Auth::attempt($request->only('email_sdt', 'password'))) {
            if (Auth::user()->trang_thai !== 'Hoat dong') {
                Auth::logout();
                return back()
                    ->withErrors(['email_sdt' => 'Tài khoản này đã bị khóa. Vui lòng liên hệ garage để được hỗ trợ.'])
                    ->withInput($request->only('email_sdt'));
            }

            $request->session()->regenerate();
            return $this->dieuHuongTheoVaiTro();
        }

        return back()
            ->withErrors(['email_sdt' => 'Số điện thoại/email hoặc mật khẩu không đúng'])
            ->withInput($request->only('email_sdt'));
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('trang-chu');
        }
        return view('auth.dang-ky');
    }

    public function register(Request $request)
    {
        $request->validate([
            'ho_ten'   => 'required|string|max:150',
            'sdt'      => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'ho_ten.required'   => 'Vui lòng nhập họ tên',
            'sdt.required'      => 'Vui lòng nhập số điện thoại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min'      => 'Mật khẩu tối thiểu 6 ký tự',
            'password.confirmed'=> 'Mật khẩu xác nhận không khớp',
        ]);

        $daTonTai = TaiKhoan::where('email_sdt', $request->sdt)->exists();
        if ($daTonTai) {
            return back()
                ->withErrors(['sdt' => 'Số điện thoại này đã có tài khoản. Vui lòng đăng nhập.'])
                ->withInput($request->only('ho_ten', 'sdt'));
        }

        DB::transaction(function () use ($request, &$taiKhoan) {
            $taiKhoan = TaiKhoan::create([
                'email_sdt'    => $request->sdt,
                'mat_khau_hash'=> Hash::make($request->password),
                'vai_tro'      => 'Khach hang',
                'trang_thai'   => 'Hoat dong',
            ]);

            KhachHang::create([
                'taikhoan_id' => $taiKhoan->id,
                'ho_ten'      => $request->ho_ten,
                'sdt'         => $request->sdt,
                'trang_thai'  => 'Hoat dong',
            ]);
        });

        Auth::login($taiKhoan);

        return redirect()->route('trang-chu')->with('thanhcong', 'Đăng ký thành công!');
    }

    /**
     * Chuyển hướng sang trang đăng nhập Google
     */
    public function chuyenHuongGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google gọi lại sau khi khách xác nhận đăng nhập
     */
    public function xuLyGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        // Tìm theo email — nếu đã có tài khoản (đăng ký thường trước đó với cùng email) thì dùng lại
        $taiKhoan = TaiKhoan::where('email_sdt', $googleUser->email)->first();

        if (!$taiKhoan) {
            // Chưa có — tạo mới, mật khẩu ngẫu nhiên (khách không cần biết, chỉ dùng Google để đăng nhập)
            DB::transaction(function () use ($googleUser, &$taiKhoan) {
                $taiKhoan = TaiKhoan::create([
                    'email_sdt'     => $googleUser->email,
                    'mat_khau_hash' => Hash::make(Str::random(24)),
                    'vai_tro'       => 'Khach hang',
                    'trang_thai'    => 'Hoat dong',
                ]);

                KhachHang::create([
                    'taikhoan_id' => $taiKhoan->id,
                    'ho_ten'      => $googleUser->name,
                    'sdt'         => $googleUser->email, // tạm thời, khách có thể cập nhật SĐT thật sau
                    'email'       => $googleUser->email,
                    'trang_thai'  => 'Hoat dong',
                ]);
            });
        }

        Auth::login($taiKhoan);

        return $this->dieuHuongTheoVaiTro();
    }

    private function dieuHuongTheoVaiTro()
    {
        return match (Auth::user()->vai_tro) {
            'Co van dich vu' => redirect()->route('cvdv.dashboard'),
            'Ky thuat vien'  => redirect()->route('ktv.phieu'),
            'Quan ly'        => redirect()->route('quan-ly.dashboard'),
            default          => redirect()->route('trang-chu'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('trang-chu-cong-khai');
    }
}