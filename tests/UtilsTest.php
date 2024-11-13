<?php

use PHPUnit\Framework\TestCase;
use Enicore\RavenApi\Utils;

class UtilsTest extends TestCase
{
    public function testGetHost()
    {
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'example.com';
        $this->assertEquals('https://example.com', Utils::getHost());

        $_SERVER['HTTPS'] = 'off';
        $_SERVER['SERVER_NAME'] = 'example.com';
        $this->assertEquals('http://example.com', Utils::getHost());
    }

    public function testSizeToString()
    {
        $this->assertEquals('1.5 MB', Utils::sizeToString(1500000));
        $this->assertEquals('500 B', Utils::sizeToString(500));
        $this->assertEquals('-', Utils::sizeToString('invalid'));
    }

    public function testShortenString()
    {
        $this->assertEquals('Hello...', Utils::shortenString('Hello world', 5));
        $this->assertEquals('Hello world', Utils::shortenString('Hello world', 20));
    }

    public function testStructuredDirectory()
    {
        $this->assertEquals('000/000/', Utils::structuredDirectory(1, 500, 2));
        $this->assertEquals('000/001/', Utils::structuredDirectory(500, 500, 2));
    }

    public function testEnsureFileName()
    {
        $this->assertEquals('file_name', Utils::ensureFileName('file/name'));
        $this->assertEquals('unknown', Utils::ensureFileName(''));
    }

    public function testGenerateUuid()
    {
        $uuid = Utils::generateUuid();
        $this->assertMatchesRegularExpression('/^[a-f0-9\-]{36}$/', $uuid);
    }

    public function testGeneratePassword()
    {
        $password = Utils::generatePassword(12);
        $this->assertEquals(12, strlen($password));
    }

    public function testValidateEmail()
    {
        $this->assertTrue(Utils::validateEmail('test@example.com', false));
        $this->assertFalse(Utils::validateEmail('invalid-email', false));
    }

    public function testGetExtension()
    {
        $this->assertEquals('jpg', Utils::getExtension('file.jpg'));
        $this->assertEquals('gz', Utils::getExtension('document.tar.gz'));
    }

    public function testAddSlash()
    {
        $this->assertEquals('path/', Utils::addSlash('path'));
        $this->assertEquals('path/', Utils::addSlash('path/'));
    }

    public function testRemoveSlash()
    {
        $this->assertEquals('path', Utils::removeSlash('path/'));
        $this->assertEquals('path', Utils::removeSlash('path'));
    }

    public function testStrToBool()
    {
        $this->assertTrue(Utils::strToBool('true'));
        $this->assertFalse(Utils::strToBool('false'));
        $this->assertEquals('other', Utils::strToBool('other'));
    }

    public function testEncodeBinary()
    {
        $data = 'hello';
        $encoded = Utils::encodeBinary($data);
        $this->assertNotEmpty($encoded);
    }

    public function testDecodeBinary()
    {
        $encoded = Utils::encodeBinary('hello');
        $decoded = Utils::decodeBinary($encoded);
        $this->assertEquals('hello', $decoded);
    }
}
