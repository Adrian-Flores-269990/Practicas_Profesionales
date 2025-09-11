<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Alumno;

class ActualizarPasswordsSeeder extends Seeder
{
    public function run()
    {
        $alumnos = Alumno::all();

        foreach ($alumnos as $alumno) {
            // Solo actualizar si no está en bcrypt
            if (!str_starts_with($alumno->password, '$2y$')) {
                $alumno->password = Hash::make($alumno->password);
                $alumno->save();
            }
        }

        $this->command->info('Todas las contraseñas se han actualizado a bcrypt.');
    }
}
