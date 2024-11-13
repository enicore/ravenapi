<?php

use PHPUnit\Framework\TestCase;
use Enicore\RavenApi\Countries;

class CountriesTest extends TestCase
{
    public function testGetAllCountries()
    {
        $countries = Countries::getAllCountries();

        // Check that the returned array is not empty
        $this->assertNotEmpty($countries);

        // Check that the array contains specific known countries
        $this->assertArrayHasKey('US', $countries);
        $this->assertArrayHasKey('CA', $countries);
        $this->assertEquals('United States', $countries['US']);
        $this->assertEquals('Canada', $countries['CA']);
    }

    public function testCountryExists()
    {
        // Test with a valid country code
        $this->assertTrue(Countries::countryExists('US'));

        // Test with a lowercase valid country code
        $this->assertTrue(Countries::countryExists('us'));

        // Test with an invalid country code
        $this->assertFalse(Countries::countryExists('ZZ'));
    }

    public function testGetCountryName()
    {
        // Test with a valid country code
        $this->assertEquals('United States', Countries::getCountryName('US'));

        // Test with a lowercase valid country code
        $this->assertEquals('United States', Countries::getCountryName('us'));

        // Test with an invalid country code
        $this->assertEquals('', Countries::getCountryName('ZZ'));

        // Test with an empty country code, which should return 'Unknown'
        $this->assertEquals('Unknown', Countries::getCountryName(''));
    }
}
