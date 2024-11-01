<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\PredictEmail;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PredictEmailTest extends TestCase
{
    #[Test]
    public function full_name_with_dots()
    {
        $predictEmail = new PredictEmail;
        $email = $predictEmail->fullNameWithDots('John', 'Doe', 'example.com');
        $this->assertEquals('john.doe@example.com', $email);
    }

    #[Test]
    public function first_letter_name_last_name()
    {
        $predictEmail = new PredictEmail;
        $email = $predictEmail->firstLetterNameLastName('John', 'Doe', 'example.com');
        $this->assertEquals('jdoe@example.com', $email);
    }

    #[Test]
    public function name_only()
    {
        $predictEmail = new PredictEmail;
        $email = $predictEmail->nameOnly('John', 'example.com');
        $this->assertEquals('j@example.com', $email);
    }

    #[Test]
    public function first_letters_of_names_and_last_name()
    {
        $predictEmail = new PredictEmail;

        // Testing with multiple parts in the name and last name
        $email = $predictEmail->firstLettersOfNamesAndLastName('Juan Manuel', 'Perez Rodriguez', 'example.com');
        $this->assertEquals('jmperezrodriguez@example.com', $email);

        // Testing with a single name and last name
        $email = $predictEmail->firstLettersOfNamesAndLastName('John', 'Doe', 'example.com');
        $this->assertEquals('jdoe@example.com', $email);
    }

    #[Test]
    public function is_valid()
    {
        $predictEmail = new PredictEmail;

        // Mocking Laravel's Validator
        Validator::shouldReceive('make')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('passes')
            ->andReturn(true);

        $isValid = $predictEmail->isValid('john.doe@example.com');
        $this->assertTrue($isValid);
    }
}
