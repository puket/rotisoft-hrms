<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_code' => 'EMP-' . fake()->unique()->numberBetween(1000, 9999),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'hire_date' => fake()->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            // แผนกและตำแหน่งจะถูกสุ่มใส่ตอนรัน Seeder ครับ
            'status' => fake()->randomElement(['Active', 'Active', 'Active', 'Resigned']), 
        ];
    }
}
