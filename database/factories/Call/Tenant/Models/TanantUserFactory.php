<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Database\Factories\Call\Tenant\Models;


use Call\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class TanantUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id'=>Uuid::uuid4(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'description' => $this->faker->lastName,
            'password' => 'secret',
            'remember_token' => Str::random(10),
            'owner' => false,
        ];
    }
}
