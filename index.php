<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Información Académica - Horario de Salón</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type='text/css'>
        /* Puedes agregar estilos adicionales aquí según sea necesario */
    </style>
</head>
<body bgcolor="#FFFFF0">
<center>
    <font class="option">
    <center>
         <!-- Encabezado de la tabla de horarios -->
           
         <table width='80%' border='1' bordercolor='#ac2e21'>
                <td align='center' bgcolor='#ac2e21'>Edificio</td>
                <td align='center'><font class='title'>AULAS SUR</font></td>
                <td align='center' bgcolor='#ac2e21'>Ubicación</td>
                <td align='center'><font class='title'>CUARTO PISO</font></td>
            </tr>
        </table>
        <br>

        <!-- Formulario para seleccionar el bloque y el salón -->
        <form method="POST" action="">
            <label for="bloque">Bloque:</label>
            <select name="bloque" id="bloque" onchange="updateSalones()">
                <option value="SA">SA</option>
                <option value="SB">SB</option>
                <option value="SC">SC</option>
                <option value="SD">SD</option>
                <option value="SF">SF</option>
                <option value="AU">AU</option>
            </select>
            &nbsp;&nbsp;
            <label for="salon">Salón:</label>
            <select name="salon" id="salon">
                <!-- Opciones de salones se llenarán dinámicamente -->
            </select>
            &nbsp;&nbsp;
            <input type="submit" name="submit" value="Mostrar Horarios">
        </form>
        <!-- Fin del formulario -->

        <br>
    </center>
    <hr color='#ac2e21' width='90%'>

    <!-- Tabla de horarios -->
    <table width="95%" border="1" bgcolor="#cfcfbb">
        <tr bgcolor="#ac2e21">
            <td align="center" width="15%"><font class="materia">HORA</font></td>
            <td align="center" width="17%"><font class="materia">LUNES</font></td>
            <td align="center" width="17%"><font class="materia">MARTES</font></td>
            <td align="center" width="17%"><font class="materia">MIÉRCOLES</font></td>
            <td align="center" width="17%"><font class="materia">JUEVES</font></td>
            <td align="center" width="17%"><font class="materia">VIERNES</font></td>
        </tr>
        <?php
        // Procesar la solicitud cuando se envíe el formulario
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(isset($_POST['bloque']) && isset($_POST['salon'])) {
                $bloque = $_POST['bloque'];
                $salon = $_POST['salon'];

                // Crear la conexión a la base de datos
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "horarios_db";
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Verificar la conexión
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Obtener el nombre de la tabla
                $nombre_tabla = 'horario' . strtolower($salon);

                // Consultar datos
                $sql = "SELECT hora, lunes, martes, miercoles, jueves, viernes FROM $nombre_tabla";
                $result = $conn->query($sql);

                // Mostrar los resultados
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td bgcolor='#ac2e21' align='center'><font class='materia'>" . $row["hora"] . "</font></td>";
                        echo "<td align='center'>" . $row["lunes"] . "</td>";
                        echo "<td align='center'>" . $row["martes"] . "</td>";
                        echo "<td align='center'>" . $row["miercoles"] . "</td>";
                        echo "<td align='center'>" . $row["jueves"] . "</td>";
                        echo "<td align='center'>" . $row["viernes"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' align='center'>No hay datos disponibles</td></tr>";
                }

                // Cerrar la conexión
                $conn->close();
            }
        }
        ?>
    </table>
    <br>
    <a href="login.php">Administrar Horarios</a>
</center>

<script>
    // Función JavaScript para actualizar los salones al cambiar el bloque seleccionado
    function updateSalones() {
        var bloque = document.getElementById('bloque').value;
        var salonSelect = document.getElementById('salon');
        salonSelect.innerHTML = '';

        var salones = {
            'SA': ['SA401', 'SA402', 'SA403', 'SA404'],
            'SB': ['SB401', 'SB402', 'SB403', 'SB404'],
            'SC': ['SC401', 'SC402', 'SC403', 'SC404'],
            'SD': ['SD401', 'SD402', 'SD403', 'SD404'],
            'SF': ['SF401', 'SF402', 'SF403', 'SF404'],
            'AU': ['AU401']
        };

        salones[bloque].forEach(function(salon) {
            var option = document.createElement('option');
            option.value = salon.toLowerCase(); // Nombre del salón
            option.text = salon;
            salonSelect.appendChild(option);
        });
    }

    // Llamar a updateSalones() inicialmente para llenar los salones según el bloque seleccionado
    document.addEventListener('DOMContentLoaded', function() {
        updateSalones();
    });
</script>
</body>
</html>