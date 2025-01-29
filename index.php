<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>DWES Tarea 9</title>
        <!-- Importamos hoja de estilo -->
        <link rel="stylesheet" type="text/css" href="estilo.css">
    </head>
    <body>
        <header>
            <h1>DWES Tarea 9</h1>
        </header>
        
        <section>
            <div class="container">
                <h1>Buscador de Pokémon</h1>
                <form method="get">
                    <input type="text" name="pokemon" placeholder="Introduce el nombre del Pokémon o 'todos' para listarlos a todos">
                    <input type="submit" value="Buscar">
                    <button id="todos" name="todos">Listar todos los pokémon</button>
                    <button id="limpiar" name="limpiar">Limpiar resultados</button>
                </form>

                <?php
                // Llamamos al documento PHP con las funciones
                require_once "funciones.php";

                // Procesamiento del formulario
                if (isset($_GET['pokemon'])) { // Si hemos escrito en el input
                    // Si pulsamos el botón de listar todos o escribimos "todos"
                    if ($_GET['pokemon'] == "todos" || isset($_GET['todos'])) {
                        ?>
                        <h1>Lista de Pokémon</h1>
                        <!-- Creamos un barra de progreso de carga -->
                        <div class="progress-bar-container">
                            <div class="progress-bar"></div>
                            <span class="loading-text">Cargando...</span>
                        </div>
                        <?php
                        // Generamos los botones superiores de navegación
                        navegarBotones();
                        // Listamos todos los pokémon por medio de una tabla
                        mostrarTodos();
                        // Generamos los botones inferiores de navegación
                        navegarBotones();
                    } elseif ($_GET['pokemon'] != "") { // Si buscamos por nombre un pokémon
                        // Mostramos en una tabla el pokémon encontrado
                        mostrarUno($_GET['pokemon']);
                    } elseif (!isset($_GET['limpiar'])) { // Sin le damos a buscar sin un nombre o "todos"
                        // Mensaje de error
                        echo "<h2 class='error'>Debes escribir un nombre o 'todos' para que se muestre la lista completa</h2>";
                    }
                } elseif (isset($_GET['limpiar'])) { // Si le damos al botón "Limpiar"
                    // Redirigimos a la URI original para borrar cualquier resultado
                    header("http://localhost/DWES_T9");
                }
                ?>
            </div>
        </section>

        <footer>
            <h3>Tarea realizada por: Sonia Enjuto Gil</h3>
        </footer>
    </body>
</html>