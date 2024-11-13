<?php

use PHPUnit\Framework\TestCase;
use Enicore\RavenApi\Session;

class SessionTest extends TestCase
{
    protected Session $session;

    protected function setUp(): void
    {
        $this->session = Session::instance();
    }

    /**
     * @runInSeparateProcess
     */
    public function testSingletonInstance()
    {
        $firstInstance = Session::instance();
        $secondInstance = Session::instance();

        // Assert that both instances are the same
        $this->assertSame($firstInstance, $secondInstance);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetAndGet()
    {
        $this->session->set('user', 'JohnDoe');
        $this->assertEquals('JohnDoe', $this->session->get('user'));

        // Test with a default value
        $this->assertEquals('default', $this->session->get('nonexistent_key', 'default'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testHas()
    {
        $this->session->set('user', 'JohnDoe');
        $this->assertTrue($this->session->has('user'));
        $this->assertFalse($this->session->has('nonexistent_key'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testRemove()
    {
        $this->session->set('user', 'JohnDoe');
        $this->assertTrue($this->session->has('user'));

        $this->session->remove('user');
        $this->assertFalse($this->session->has('user'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testStart()
    {
        $this->assertEquals(PHP_SESSION_NONE, session_status());

        $this->session->start();
        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetDefaultValue()
    {
        // Check that the default value is returned when the key doesn't exist
        $this->assertEquals('default', $this->session->get('nonexistent_key', 'default'));
    }
}
