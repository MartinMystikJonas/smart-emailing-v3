<?php

declare(strict_types=1);

namespace SmartEmailing\v3\Tests\Models;

use PHPUnit\Framework\TestCase;
use SmartEmailing\v3\Exceptions\PropertyRequiredException;
use SmartEmailing\v3\Models\Recipient;

class RecipientTest extends TestCase
{
    private Recipient $model;

    protected function setUp(): void
    {
        $this->model = new Recipient();
    }

    public function testShouldThrowExceptionWhenMissingAllData(): void
    {
        $this->expectException(PropertyRequiredException::class);
        $this->model->toArray();
    }

    public function testShouldReturnArrayWithData(): void
    {
        $this->model->setEmailAddress('email@example.com');
        $data = $this->model->toArray();

        self::assertSame([
            'emailaddress' => 'email@example.com',
        ], $data);
    }

    public function testShouldReturnSameDataFromSerializer(): void
    {
        $this->model->setEmailAddress('email@example.com');

        self::assertSame($this->model->toArray(), $this->model->jsonSerialize());
    }

    public function testShouldSetSettersAndReadGetters(): void
    {
        $this->model->setEmailAddress('email@example.com');
        self::assertSame('email@example.com', $this->model->getEmailAddress());
    }
}
