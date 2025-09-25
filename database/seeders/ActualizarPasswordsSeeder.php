<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Alumno;
use App\Models\Encargado;


class ActualizarPasswordsSeeder extends Seeder
{
    public function run()
    {
        $alumnos = Alumno::all();
        $encargados = Encargado::all();

        foreach ($alumnos as $alumno) {
            // Solo actualizar si no está en bcrypt
            if (!str_starts_with($alumno->password, '$2y$')) {
                $alumno->password = Hash::make($alumno->password);
                $alumno->save();
            }
        }

        // Actualizar contraseñas de encargados
        $encargados = Encargado::all();
        foreach ($encargados as $encargado) {
            if (!str_starts_with($encargado->Contrasena, '$2y$')) {
                $encargado->Contrasena = Hash::make($encargado->Contrasena);
                $encargado->save();
            }
        }

        $this->command->info('Todas las contraseñas se han actualizado a bcrypt.');
    }
}
