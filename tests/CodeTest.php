<?php

use PHPUnit\Framework\TestCase;
use Enicore\RavenApi\Code;

class CodeTest extends TestCase
{
    protected Code $code;

    protected function setUp(): void
    {
        $this->code = Code::instance();
    }

    public function testEncodeAndDecodeId()
    {
        $id = 123456;
        $encodedId = $this->code->encodeId($id);
        $this->assertNotNull($encodedId);

        $decodedId = $this->code->decodeId($encodedId);
        $this->assertEquals($id, $decodedId);

        // Test invalid ID
        $this->assertNull($this->code->encodeId(-1));
        $this->assertNull($this->code->decodeId('invalid_string'));

        // Test with randomized encoding
        $randomizedEncodedId = $this->code->encodeId($id, true);
        $this->assertNotNull($randomizedEncodedId);
        $this->assertNotEquals($encodedId, $randomizedEncodedId);
    }

    public function testEncryptAndDecrypt()
    {
        $key = 'secretkey';
        $data = 'Hello, World!';

        $encryptedData = $this->code->encrypt($data, $key);
        $this->assertNotFalse($encryptedData);

        $decryptedData = $this->code->decrypt($encryptedData, $key);
        $this->assertEquals($data, $decryptedData);

        // Test with different data types
        $arrayData = ['key' => 'value'];
        $encryptedArray = $this->code->encrypt($arrayData, $key);
        $this->assertNotFalse($encryptedArray);

        $decryptedArray = $this->code->decrypt($encryptedArray, $key);
        $this->assertEquals($arrayData, $decryptedArray);

        // Test with null data
        $encryptedNull = $this->code->encrypt(null, $key);
        $decryptedNull = $this->code->decrypt($encryptedNull, $key);
        $this->assertNull($decryptedNull);
    }

    public function testEncryptStringAndDecryptString()
    {
        $key = 'secretkey';
        $string = 'Test String';

        $encryptedString = $this->code->encryptString($string, $key);
        $this->assertNotFalse($encryptedString);

        $decryptedString = $this->code->decryptString($encryptedString, $key);
        $this->assertEquals($string, $decryptedString);

        // Test with empty string
        $this->assertFalse($this->code->encryptString('', $key));
        $this->assertFalse($this->code->decryptString('', $key));
    }

    public function testBaseEncodeAndDecode()
    {
        $number = 123456;
        $encodedBase36 = Code::baseEncode($number, Code::BASE_36_CHARSET);
        $this->assertNotEmpty($encodedBase36);

        $decodedBase36 = Code::baseDecode($encodedBase36, Code::BASE_36_CHARSET);
        $this->assertEquals($number, $decodedBase36);

        $encodedBase62 = Code::baseEncode($number, Code::BASE_62_CHARSET);
        $this->assertNotEmpty($encodedBase62);

        $decodedBase62 = Code::baseDecode($encodedBase62, Code::BASE_62_CHARSET);
        $this->assertEquals($number, $decodedBase62);

        $encodedBase92 = Code::baseEncode($number, Code::BASE_92_CHARSET);
        $this->assertNotEmpty($encodedBase92);

        $decodedBase92 = Code::baseDecode($encodedBase92, Code::BASE_92_CHARSET);
        $this->assertEquals($number, $decodedBase92);
    }

    public function testBinaryToTextAndTextToBinary()
    {
        $binaryData = random_bytes(16);

        $encodedText = Code::binaryToText($binaryData);
        $this->assertNotEmpty($encodedText);

        $decodedBinary = Code::textToBinary($encodedText);
        $this->assertEquals($binaryData, $decodedBinary);

        // Test with empty string input
        $this->assertSame('', Code::binaryToText(''));
        $this->assertSame('', Code::textToBinary(''));
    }

    public function testGetShuffledAlphabetIndirectly()
    {
        // Ensures getShuffledAlphabet is covered through encodeId and decodeId
        $id = 789123;
        $encodedId = $this->code->encodeId($id, true);
        $this->assertNotNull($encodedId);

        $decodedId = $this->code->decodeId($encodedId);
        $this->assertEquals($id, $decodedId);
    }
}
