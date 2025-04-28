<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $baseUrl = 'https://group9portal-eehbdxbhcgftezez.canadaeast-01.azurewebsites.net';

    public function testLoginPageLoads()
    {
        $ch = curl_init($this->baseUrl . '/login.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $this->assertStringContainsString('<form', $response);
        $this->assertStringContainsString('name="username"', $response);
        $this->assertStringContainsString('name="password"', $response);
    }

    public function testLoginValidUser()
    {
        $postData = http_build_query([
            'username' => 'existinguser', // replace with real username
            'password' => 'ExistingPass123!', // replace with real password
        ]);

        $ch = curl_init($this->baseUrl . '/login.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $this->assertStringContainsString('Welcome', $response); // or dashboard page text
    }

    public function testLoginInvalidUser()
    {
        $postData = http_build_query([
            'username' => 'wronguser_' . rand(1000,9999),
            'password' => 'WrongPass!',
        ]);

        $ch = curl_init($this->baseUrl . '/login.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $this->assertStringContainsString('Invalid username or password', $response);
    }
}
?>
