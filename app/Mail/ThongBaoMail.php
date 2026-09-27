<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThongBaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $noiDung;
    public string $tieuDe;
    public ?string $duongDan;

    public function __construct(string $noiDung, string $tieuDe = 'Thông báo từ AutoCare', ?string $duongDan = null)
    {
        $this->noiDung = $noiDung;
        $this->tieuDe = $tieuDe;
        $this->duongDan = $duongDan;
    }

    public function build()
    {
        return $this->subject($this->tieuDe)
            ->view('emails.thong-bao')
            ->with([
                'noiDung' => $this->noiDung,
                'duongDan' => $this->duongDan,
            ]);
    }
}