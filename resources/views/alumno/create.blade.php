<form action="{{ route('alumno.store') }}" method="POST">
    @csrf
    <label>Clave Alumno:</label>
    <input type="number" name="Clave_Alumno" required><br>

    <label>Nombre:</label>
    <input type="text" name="Nombre" required><br>

    <label>Apellido Paterno:</label>
    <input type="text" name="ApellidoP_Alumno"><br>

    <label>Apellido Materno:</label>
    <input type="text" name="ApellidoM_Alumno"><br>

    <label>Semestre:</label>
    <input type="number" name="Semestre"><br>

    <label>Carrera:</label>
    <input type="text" name="Carrera"><br>

    <label>Teléfono Celular:</label>
    <input type="text" name="TelefonoCelular"><br>

    <label>Correo Electrónico:</label>
    <input type="email" name="CorreoElectronico"><br>

    <button type="submit">Guardar</button>
</form>
