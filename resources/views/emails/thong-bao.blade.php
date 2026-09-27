
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: -apple-system, Arial, sans-serif; background: #f8fafc; padding: 24px; margin: 0;">
    <div style="max-width: 480px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden;">
        <div style="background: #0B2545; padding: 20px 24px;">
            <span style="color: white; font-size: 18px; font-weight: 600;">🚗 AutoCare</span>
        </div>
        <div style="padding: 24px;">
            @if ($nhanTen)
                <p style="color: #64748b; font-size: 14px;">Xin chào {{ $nhanTen }},</p>
            @endif
            <p style="color: #0B2545; font-size: 15px; line-height: 1.6;">{{ $noiDung }}</p>

            @if ($duongDan)
                <a href="{{ $duongDan }}" style="display: inline-block; background: #0B2545; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; margin-top: 12px;">
                    Xem chi tiết
                </a>
            @endif
        </div>
        <div style="background: #f8fafc; padding: 16px 24px; text-align: center;">
            <p style="color: #94a3b8; font-size: 12px; margin: 0;">Email tự động từ hệ thống AutoCare — vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>