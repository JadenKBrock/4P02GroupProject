<?php
use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    private $baseUrl = 'https://group9portal-eehbdxbhcgftezez.canadaeast-01.azurewebsites.net';

    public function testRegisterPageLoads()
    {
        $ch = curl_init($this->baseUrl . '/register.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $this->assertStringContainsString('<form', $response);
        $this->assertStringContainsString('name="username"', $response);
        $this->assertStringContainsString('name="password"', $response);
    }

    public function testRegisterNewUser()
    {
        $username = 'testuser_' . rand(1000, 9999);
        $postData = http_build_query([
            'username' => $username,
            'password' => 'TestPass123!',
            'confirm_password' => 'TestPass123!',
        ]);

        $ch = curl_init($this->baseUrl . '/register.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $this->assertStringContainsString('Registration successful', $response);
    }
}
?>
