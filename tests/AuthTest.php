<?php

use PHPUnit\Framework\TestCase;
use Enicore\RavenApi\Auth;
use Enicore\RavenApi\Session;

class AuthTest extends TestCase
{
    protected Auth $auth;
    protected $sessionMock;

    protected function setUp(): void
    {
        $this->sessionMock = $this->createMock(Session::class);

        // Instantiate Auth and inject the session mock
        $this->auth = Auth::instance();
        $this->auth->session = $this->sessionMock;
    }

    public function testIsLoggedInReturnsTrueWhenUserDataExists()
    {
        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('userData')
            ->willReturn(['userId' => 1]);

        $this->assertTrue($this->auth->isLoggedIn());
    }

    public function testIsLoggedInReturnsFalseWhenNoUserData()
    {
        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('userData')
            ->willReturn(null);

        $this->assertFalse($this->auth->isLoggedIn());
    }

    public function testGetUserDataReturnsUserData()
    {
        $userData = ['userId' => 1, 'name' => 'John Doe'];
        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('userData')
            ->willReturn($userData);

        $this->assertEquals($userData, $this->auth->getUserData());
    }

    public function testGetUserDataReturnsNullWhenNoUserData()
    {
        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('userData')
            ->willReturn(null);

        $this->assertNull($this->auth->getUserData());
    }

    public function testGetUserIdReturnsUserId()
    {
        $userData = ['userId' => 42];
        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('userData')
            ->willReturn($userData);

        $this->assertEquals(42, $this->auth->getUserId());
    }

    public function testGetUserIdReturnsNullWhenNoUserData()
    {
        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('userData')
            ->willReturn(null);

        $this->assertNull($this->auth->getUserId());
    }

    public function testGetReturnsSpecificUserData()
    {
        $userData = ['userId' => 1, 'name' => 'John Doe'];
        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('userData')
            ->willReturn($userData);

        $this->assertEquals('John Doe', $this->auth->get('name'));
    }

    public function testGetReturnsNullWhenKeyDoesNotExist()
    {
        $userData = ['userId' => 1, 'name' => 'John Doe'];
        $this->sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('userData')
            ->willReturn($userData);

        $this->assertNull($this->auth->get('email'));
    }

    public function testSetUserDataThrowsExceptionWhenUserIdIsMissing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("User ID is required.");

        $this->auth->setUserData(['name' => 'John Doe']);
    }

    public function testSetUserDataStoresUserDataInSession()
    {
        $userData = ['userId' => 1, 'name' => 'John Doe'];

        $this->sessionMock
            ->expects($this->once())
            ->method('set')
            ->with('userData', $userData);

        $this->auth->setUserData($userData);
    }

    public function testRemoveUserDataRemovesUserDataFromSession()
    {
        $this->sessionMock
            ->expects($this->once())
            ->method('remove')
            ->with('userData');

        $this->auth->removeUserData();
    }
}
