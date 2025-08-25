<?php

namespace App\Tests\Unit\Traits;

trait ValidationTestTrait
{
    public const MAX_LENGTH_ERROR_CODE = 'd94b19cc-114f-4f44-9cc4-4138e80a87b9';
    public const NOT_BLANK_ERROR_CODE = 'c1051bb4-d103-4f74-8988-acbcafc7fdc3';
    public const MIN_LENGTH_ERROR_CODE = '9ff3fdc4-b214-49db-8718-39c315e33d45';
    public const UNIQUE_ERROR_CODE = '23bd9dbf-6b9b-41cd-a99e-4844bcf3077f';
    public const POSITIVE_ERROR_CODE = '778b7ae0-84d3-481a-9dec-35fdb64b1d78';

    /**
     * Assert that the validation errors of an object match the expected errors.
     *
     * @param object $object
     * @param array $expectedErrors ['title' => 'c1051bb4-d103-4f74-8988-acbcafc7fdc3', 'content' => 'Cette valeur est obligatoire']
     * @return void
     */
    protected function assertValidationErrors(object $object, array $expectedErrors = []): void
    {
        $errors = $this->validator->validate($object);

        if (empty($expectedErrors)) {
            $this->assertEmpty($errors, 'Expected no validation errors');
            return;
        }

        $errorSearch = $errors->findByCodes($expectedErrors['code']);

        $this->assertNotEmpty($errorSearch, 'Expected validation error not found');
        $this->assertEquals($expectedErrors['property'], $errorSearch[0]->getPropertyPath(), 'Validation errors do not match expected errors');
    }
}