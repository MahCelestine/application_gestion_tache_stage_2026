<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    protected $fillable = ['nom'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public static function resolveId(array $data): int
    {
        if (!empty($data['client_id'])) {
            return (int) $data['client_id'];
        }

        $client = self::create([
            'nom' => $data['new_client_name']
        ]);

        return $client->id;
    }
}
