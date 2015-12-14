<?php
use App\Models\ApiKey;  
use Illuminate\Database\Seeder;
class ApiKeyTableSeeder extends Seeder  
{
    public function run()
    {
        ApiKey::create([
            'key' => 'c1049812-6e62-11e5-9d70-feff819cdc9f',
            'name' => 'Web App'
        ]);
    }
}