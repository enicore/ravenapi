<?php

use PHPUnit\Framework\TestCase;
use Enicore\RavenApi\Form;
use Enicore\RavenApi\Database;
use Enicore\RavenApi\Response;
use Enicore\RavenApi\Request;
use Enicore\RavenApi\Code;

class FormTest extends TestCase
{
    protected Form $form;
    protected $dbMock;
    protected $responseMock;
    protected $requestMock;
    protected $codeMock;

    protected function setUp(): void
    {
        // Create mocks for dependencies
        $this->dbMock = $this->createMock(Database::class);
        $this->responseMock = $this->createMock(Response::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->codeMock = $this->createMock(Code::class);

        // Create Form instance and inject dependencies
        $this->form = new Form();
        $this->form->db = $this->dbMock;
        $this->form->response = $this->responseMock;
        $this->form->request = $this->requestMock;
        $this->form->code = $this->codeMock;
    }

    public function testGetElements()
    {
        $elements = [
            'username' => ['input' => 'text', 'title' => 'Username'],
            'email' => ['input' => 'text', 'title' => 'Email'],
        ];
        $this->form->setElements($elements);

        $this->assertEquals($elements, $this->form->getElements());
    }

    public function testSetElements()
    {
        $elements = [
            'username' => ['input' => 'text'],
            'email' => ['input' => 'select'],
            'invalid' => ['input' => 'unsupported'],
        ];

        $this->form->setElements($elements);

        $expected = [
            'username' => [
                'input' => 'text',
                'title' => '',
                'info' => '',
                'tab' => '',
                'value' => '',
                'htmlBefore' => '',
                'htmlAfter' => '',
                'placeholder' => ''
            ],
            'email' => [
                'input' => 'select',
                'title' => '',
                'info' => '',
                'tab' => '',
                'value' => '',
                'htmlBefore' => '',
                'htmlAfter' => '',
                'options' => []
            ]
        ];

        $this->assertEquals($expected, $this->form->getElements());
    }

    public function testSetElementProperty()
    {
        $elements = [
            'username' => ['input' => 'text'],
        ];
        $this->form->setElements($elements);

        $this->form->setElementProperty('username', 'title', 'User Name');
        $this->assertEquals('User Name', $this->form->getElements()['username']['title']);
    }

    public function testSetDataWithValidation()
    {
        $elements = [
            'username' => ['input' => 'text', 'required' => true],
            'email' => ['input' => 'text', 'required_unique' => 'users'],
        ];
        $this->form->setElements($elements);

        // Simulate the unique constraint check on 'email' field
        $this->dbMock
            ->expects($this->once())
            ->method('row')
            ->with("SELECT id FROM `users` WHERE email = ? AND (id != ? OR ? IS NULL) LIMIT 1", ['existing@example.com', null, null])
            ->willReturn(['id' => 1]);

        $data = [
            'username' => 'testuser',
            'email' => 'existing@example.com'
        ];

        $result = $this->form->setData($data, true);
        $elements = $this->form->getElements();

        $this->assertFalse($result);
        $this->assertEquals('A record with this value already exists.', $elements['email']['error']);
    }

    public function testSetDataWithoutValidation()
    {
        $elements = [
            'username' => ['input' => 'text'],
            'email' => ['input' => 'text'],
        ];
        $this->form->setElements($elements);

        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com'
        ];

        $result = $this->form->setData($data, false);
        $elements = $this->form->getElements();

        $this->assertTrue($result);
        $this->assertEquals('testuser', $elements['username']['value']);
        $this->assertEquals('test@example.com', $elements['email']['value']);
    }

    public function testGetData()
    {
        $elements = [
            'username' => ['input' => 'text', 'value' => 'testuser'],
            'email' => ['input' => 'text', 'value' => 'test@example.com'],
        ];
        $this->form->setElements($elements);

        $expected = [
            'username' => 'testuser',
            'email' => 'test@example.com'
        ];

        $this->assertEquals($expected, $this->form->getData());
    }

    public function testSetErrors()
    {
        $elements = [
            'username' => ['input' => 'text', 'value' => 'testuser'],
            'email' => ['input' => 'text', 'value' => 'test@example.com'],
        ];
        $this->form->setElements($elements);

        $errors = [
            'username' => 'Username is required.',
            'email' => 'Email format is invalid.',
        ];
        $this->form->setErrors($errors);

        $elements = $this->form->getElements();
        $this->assertEquals('Username is required.', $elements['username']['error']);
        $this->assertEquals('Email format is invalid.', $elements['email']['error']);
    }

    public function testExtractDataFromElements()
    {
        $elements = [
            'username' => ['value' => 'testuser'],
            'email' => ['value' => 'test@example.com'],
        ];

        $data = Form::extractDataFromElements($elements);

        $expected = [
            'username' => 'testuser',
            'email' => 'test@example.com'
        ];

        $this->assertEquals($expected, $data);
    }
}
