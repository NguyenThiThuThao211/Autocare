<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\Assert;

class AutoCareDuskTest extends DuskTestCase
{
    private string $baseUrl;
    private string $loginPath;
    private string $password = 'Test@12345';

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = rtrim(env('DUSK_BASE_URL', 'http://127.0.0.1:8000'), '/');
        $this->loginPath = env('AUTOC_LOGIN_PATH', '/login');
    }

    /**
     * Chỉ chạy khi APP đang có sẵn giao diện/route tương ứng.
     * Nếu tên route hoặc label khác, sửa các biến ở đầu file trước khi chạy.
     */
    public function test_customer_can_login_and_open_home(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser, 'test_customer@autocare.local');
            $this->assertAnyText($browser, ['Trang chủ', 'Dashboard', 'Xe của tôi', 'Đặt lịch']);
        });
    }

    public function test_customer_can_open_appointments(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser, 'test_customer@autocare.local');
            $this->clickText($browser, ['Lịch hẹn', 'Đặt lịch hẹn']);
            $this->assertAnyText($browser, ['Lịch hẹn', 'Đặt lịch', 'Danh sách lịch']);
        });
    }

    public function test_customer_can_open_my_cars(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser, 'test_customer@autocare.local');
            $this->clickText($browser, ['Xe của tôi', 'Quản lý xe']);
            $this->assertAnyText($browser, ['Xe của tôi', 'Thêm xe', 'Biển số']);
        });
    }

    public function test_advisor_can_open_repair_tickets(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser, 'test_cvdv@autocare.local');
            $this->clickText($browser, ['Phiếu sửa chữa', 'Quản lý phiếu sửa chữa', 'Tiếp nhận']);
            $this->assertAnyText($browser, ['Phiếu sửa chữa', 'Tiếp nhận', 'Chẩn đoán']);
        });
    }

    public function test_technician_can_open_assigned_tickets(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser, 'test_ktv@autocare.local');
            $this->clickText($browser, ['Phiếu được phân công', 'Phiếu sửa chữa', 'Công việc']);
            $this->assertAnyText($browser, ['Phiếu sửa chữa', 'Chẩn đoán', 'Phân công']);
        });
    }

    public function test_manager_can_open_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser, 'test_manager@autocare.local');
            $this->assertAnyText($browser, ['Dashboard', 'Thống kê', 'Doanh thu', 'Báo cáo']);
        });
    }

    public function test_chatbot_is_visible(): void
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser, 'test_customer@autocare.local');
            $this->clickText($browser, ['Chatbot', 'Trợ lý', 'AI']);
            $this->assertAnyText($browser, ['Chatbot', 'Trợ lý', 'Nhập câu hỏi', 'Gửi']);
        });
    }

    private function login(Browser $browser, string $username): void
    {
        $browser->visit($this->baseUrl . $this->loginPath)
            ->waitFor('body');

        $this->typeFirst($browser, ['input[name="email_sdt"]','input[name="email"]','input[type="email"]','input[name="username"]'], $username);
        $this->typeFirst($browser, ['input[name="mat_khau"]','input[name="password"]','input[type="password"]'], $this->password);
        $this->clickText($browser, ['Đăng nhập', 'Login']);
        $browser->pause(800);
    }

    private function typeFirst(Browser $browser, array $selectors, string $value): void
    {
        foreach ($selectors as $selector) {
            if ($browser->element($selector)) {
                $browser->type($selector, $value);
                return;
            }
        }
        Assert::fail('Không tìm thấy ô input phù hợp: ' . implode(', ', $selectors));
    }

    private function clickText(Browser $browser, array $texts): void
    {
        foreach ($texts as $text) {
            $xpath = "//*[self::a or self::button or @role='button'][contains(normalize-space(.), " . $this->xpathLiteral($text) . ")]";
            $elements = $browser->driver->findElements(WebDriverBy::xpath($xpath));
            if (!empty($elements)) {
                $elements[0]->click();
                $browser->pause(400);
                return;
            }
        }
        Assert::fail('Không tìm thấy nút/link có text: ' . implode(' | ', $texts));
    }

    private function assertAnyText(Browser $browser, array $texts): void
    {
        $body = $browser->text('body');
        foreach ($texts as $text) {
            if (mb_stripos($body, $text) !== false) {
                return;
            }
        }
        Assert::fail('Không tìm thấy bất kỳ text kỳ vọng nào: ' . implode(' | ', $texts));
    }

    private function xpathLiteral(string $value): string
    {
        if (!str_contains($value, "'")) {
            return "'{$value}'";
        }
        if (!str_contains($value, '"')) {
            return '"' . $value . '"';
        }
        $parts = explode("'", $value);
        return "concat('" . implode("', \"'\", '", $parts) . "')";
    }
}
