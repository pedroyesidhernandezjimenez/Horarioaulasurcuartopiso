<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "horarios_db";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Editar horario
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $dia = $_POST['dia'];
    $hora = $_POST['hora'];
    $profesor = $_POST['profesor'];
    $materia = $_POST['materia'];
    $contenido = $profesor . ' - ' . $materia;

    // Obtener el nombre de la tabla basado en el salón seleccionado
    $tabla = isset($_POST['tabla']) ? $_POST['tabla'] : 'horarios'; // Por defecto, cargar SA401
    $campo_dia = strtolower($dia); // Convertir a minúsculas
    $sql = "UPDATE $tabla SET $campo_dia=? WHERE id=? AND hora=?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("sii", $contenido, $id, $hora);
    $stmt->execute();
    if ($stmt->errno) {
        die("Error al ejecutar la consulta: " . $stmt->error);
    }
    $stmt->close();
}

// Consultar datos según el salón seleccionado
$tabla = isset($_POST['tabla']) ? $_POST['tabla'] : 'horarios'; // Por defecto, cargar SA401
$sql = "SELECT * FROM $tabla";
$result = $conn->query($sql);

$salonSeleccionado = isset($_POST['tabla']) ? strtoupper(str_replace('horario', '', $_POST['tabla'])) : 'SA401';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Administrar Horarios</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type='text/css'>
        /* Estilos para el modal y la tabla */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        table {
            width: 95%;
            border-collapse: collapse;
            background-color: #cfcfbb;
        }
        td, th {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
        }
        td:hover {
            background-color: #a9a9a9; /* Gris oscuro, pero no tan intenso */
        }
    </style>
    <script>
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
                option.value = 'horario' + salon.toLowerCase(); // Nombre de la tabla en la base de datos
                option.text = salon;
                salonSelect.appendChild(option);
            });
        }

        // Actualizar el nombre del salón seleccionado al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            updateSalones();
        });
    </script>
</head>
<body bgcolor="#FFFFF0">
<center>
    <h2>Administrar Horarios</h2>
    <form method="post" action="admin_horarios.php">
        <label for="bloque">Seleccionar Bloque:</label>
        <select id="bloque" name="bloque" onchange="updateSalones()">
            <option value="SA">Bloque SA</option>
            <option value="SB">Bloque SB</option>
            <option value="SC">Bloque SC</option>
            <option value="SD">Bloque SD</option>
            <option value="SF">Bloque SF</option>
            <option value="AU">Bloque AU</option>
        </select>
        <label for="salon">Seleccionar Salón:</label>
        <select id="salon" name="tabla">
            <!-- Las opciones se llenarán dinámicamente según el bloque seleccionado -->
        </select>
        <input type="submit" name="seleccionar" value="Seleccionar">
    </form>
    <p id="salonNombre">Salón Seleccionado: <?php echo $salonSeleccionado; ?></p>

    <table>
        <tr bgcolor="#db1e09">
            <th width="15%"><font class="materia">HORA</font></th>
            <th width="17%"><font class="materia">LUNES</font></th>
            <th width="17%"><font class="materia">MARTES</font></th>
            <th width="17%"><font class="materia">MIÉRCOLES</font></th>
            <th width="17%"><font class="materia">JUEVES</font></th>
            <th width="17%"><font class="materia">VIERNES</font></th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr data-id='{$row['id']}' data-hora='{$row['hora']}'>";
                echo "<td align='center'>" . $row["hora"] . "</td>";
                echo "<td align='center' data-dia='lunes'>" . $row["lunes"] . "</td>";
                echo "<td align='center' data-dia='martes'>" . $row["martes"] . "</td>";
                echo "<td align='center' data-dia='miercoles'>" . $row["miercoles"] . "</td>";
                echo "<td align='center' data-dia='jueves'>" . $row["jueves"] . "</td>";
                echo "<td align='center' data-dia='viernes'>" . $row["viernes"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' align='center'>No hay datos disponibles</td></tr>";
        }
        ?>
    </table>
</center>

<!-- El Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form method="post" action="admin_horarios.php">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="dia" id="dia">
            <input type="hidden" name="tabla" id="tabla" value="<?php echo $tabla; ?>">
            Hora: <input type="text" name="hora" id="hora" readonly><br><br>
            Profesor: <input type="text" name="profesor" id="profesor"><br><br>
            Materia: <input type="text" name="materia" id="materia"><br><br>
            <input type="submit" name="edit" value="Editar">
        </form>
    </div>
</div>

<script>
    // Obtener el modal
    var modal = document.getElementById("editModal");

    // Obtener el elemento <span> que cierra el modal
    var span = document.getElementsByClassName("close")[0];

    // Abrir el modal cuando se hace clic en una celda
    document.querySelectorAll("table tr td[data-dia]").forEach(function(cell) {
        cell.addEventListener("click", function() {
            var row = cell.parentNode;
            var contenido = cell.innerText.split('-');
            var profesor = contenido[0];
            var materia = contenido[1];
            
            document.getElementById("id").value = row.dataset.id;
            document.getElementById("dia").value = cell.dataset.dia;
            document.getElementById("hora").value = row.dataset.hora;
            document.getElementById("profesor").value = profesor;
            document.getElementById("materia").value = materia;
            modal.style.display = "block";
        });
    });

    // Cerrar el modal cuando se hace clic en <span> (x)
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Cerrar el modal cuando el usuario hace clic fuera del modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>

