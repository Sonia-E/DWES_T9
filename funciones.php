<?php

/**
 * Muestra una tabla con información de Pokémon obtenida de la PokéAPI.
 *
 * Esta función realiza una petición a la PokéAPI para obtener información de una lista de Pokémon y 
 * muestra los resultados en una tabla HTML. La función también incluye una barra de progreso 
 * para indicar el avance de la carga de los datos.
 *
 * @return void No devuelve ningún valor, ya que imprime directamente la tabla HTML.
 */
function mostrarTodos() {
    ?>
    <!-- Empezamos a crear la tabla -->
    <table>
        <tr>
            <th>Número</th>
            <th>Apariencia</th>
            <th>Nombre</th>
            <th>Tipos</th>
            <th>Shiny</th>
        </tr>
        <?php
        // Llamada a la API de Pokémon
        $urlBase = "https://pokeapi.co/api/v2/pokemon/?limit=100000";
        // Obtenemos los datos JSON de la API
        $data = file_get_contents($urlBase);
        // Decodificamos los datos JSON
        $datos = json_decode($data, true);
        // Seleccionamos los datos de todos los pokémon
        $todosPokemon = $datos['results'];

        // Cantidad de pokémon a mostrar por página
        $pokemonPorPagina = 15;
        // Obtener la página actual desde la URL
        $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

        // Calcular el índice inicial para la página actual
        $inicio = ($paginaActual - 1) * $pokemonPorPagina;

        // Obtener los pokémon para la página actual
        $pokemonPaginaActual = array_slice($todosPokemon, $inicio, $pokemonPorPagina);

        // Iniciamos barra del progreso de carga
        iniciarBarra($pokemonPaginaActual);
        // Declaramos variable de pokémon que ya se han cargado
        $pokemonCargados = 0;

        // Recorremos los pokémon para sacar los datos de cada uno
        foreach ($pokemonPaginaActual as $pokemon) {
            // Recogemos la URL del pokémon actual
            $urlPokemon = $pokemon['url'];

            // Extraemos el número de la URL
            $partesUrl = explode('/', $urlPokemon);
            // Recogemos la posición del número
            $numeroPokemon = $partesUrl[count($partesUrl) - 2];

            // Obtenemos los datos del pokémon en JSON
            $data = file_get_contents($urlPokemon);
            // Decodificamos los datos JSON
            $pokemonData = json_decode($data, true);
            // Seleccionamos la imagen de apariencia base del pokémon
            $sprite = $pokemonData['sprites']['front_default'];
            // Seleccionamos la imagen de apariencia shiny del pokémon
            $shiny = $pokemonData['sprites']['front_shiny'];

            echo "<tr>";
            echo "<td>$numeroPokemon</td>";
            echo "<td><img src='$sprite' alt='{$pokemon['name']}' width='60'></td>";
            echo "<td>{$pokemon['name']}</td>";

            // Recorremos los tipos
            echo "<td class='izquierda'><ul>";
            foreach ($pokemonData['types'] as $tipo) {
                echo "<li>" . $tipo['type']['name'] . "</li>";
            }

            echo "</ul></td>";
            echo "<td><img src='$shiny' alt='{$pokemon['name']} shiny' width='60'></td>";
            echo "</tr>";

            // Actualizar la barra de progreso
            echo '<script>actualizarBarra();</script>';
            ob_flush();
            flush();

            $pokemonCargados++;
        }
        ?>
    </table>
    <?php

    // Ocultar la barra de progreso
    ocultarBarra();
}

/**
 * Muestra la información de un pokémon específico.
 *
 * Esta función recibe el nombre de un pokémon y realiza una solicitud a la PokéAPI
 * para obtener los datos del pokémon. Si se encuentra el pokémon, se muestra una tabla con su información.
 * De lo contrario, se muestra un mensaje de error.
 *
 * @param string $nombre El nombre del pokémon a buscar.
 * @return void No devuelve ningún valor, ya que imprime directamente el resultado en HTML.
 */
function mostrarUno($nombre) {
        // Pasamos el valor del input a minúsculas
        $pokemon = strtolower($nombre);

        // Llamada a la PokéAPI
        $url = "https://pokeapi.co/api/v2/pokemon/" . urlencode($pokemon);
        // Obtenemos los datos JSON de la API
        @$data = file_get_contents($url); // Usamos el operador de omisión de error para cuando no encuentre el pokémon
        // Decodificamos los datos JSON
        $pokemon = json_decode($data, true);

        // Si encuentra el pokémon
        if ($pokemon) {
            ?>
            <table class="uno">
                <tr>
                    <th>Número</th>
                    <th>Apariencia</th>
                    <th>Nombre</th>
                    <th>Tipos</th>
                    <th>Shiny</th>
                </tr>
                <?php
                    // Seleccionamos la imagen de apariencia shiny del pokémon
                    $shiny = $pokemon['sprites']['front_shiny'];

                    echo "<tr>";
                    echo "<td>{$pokemon['id']}</td>";
                    echo "<td><img src='" . $pokemon['sprites']['front_default'] . "' alt='" . $pokemon['name'] . "' width='60'></td>";
                    echo "<td>{$pokemon['name']}</td>";

                    // Recorremos los tipos
                    echo "<td class='izquierda'><ul>";
                    foreach ($pokemon['types'] as $tipo) {
                        echo "<li>" . $tipo['type']['name'] . "</li>";
                    }

                    echo "</ul></td>";
                    echo "<td><img src='$shiny' alt='{$pokemon['name']} shiny' width='60'></td>";
                    echo "</tr>";
                ?>
            </table>
            <?php
        } else {
            // Si no se encuentra el pokémon con el nombre introducido
            echo "<h2 class='error'>No se encontró el pokémon: " . $_GET['pokemon'] . "</h2>";
        }
}

/**
 * Genera la navegación entre páginas de resultados de la lista de todos los pokémon.
 *
 * Esta función crea los botones de "Anterior" y "Siguiente" para navegar entre las páginas de resultados
 * de la lista de todos los pokémon. El número de pokémon por página y la página actual se obtienen de 
 * los parámetros GET.
 *
 * @return void No devuelve ningún valor, ya que imprime directamente el HTML de la navegación.
 */
function navegarBotones() {
    // Llamada a la API de Pokémon
    $urlBase = "https://pokeapi.co/api/v2/pokemon/?limit=100000";
    // Obtenemos los datos JSON de la API
    $data = file_get_contents($urlBase);
    // Decodificamos los datos JSON
    $datos = json_decode($data, true);
    // Seleccionamos los datos de todos los pokémon
    $todosPokemon = $datos['results'];
    ?>
    <nav>
        <?php
        // Cantidad de pokémon a mostrar por página
        $pokemonPorPagina = 15;
        // Obtener la página actual desde la URL
        $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

        // Calcular el índice inicial y final para la página actual
        $inicio = ($paginaActual - 1) * $pokemonPorPagina;
        $fin = $inicio + $pokemonPorPagina - 1;?>

        <!-- Creamos botón de Anterior -->
        <?php if ($paginaActual > 1): ?>
            <a class="left" href="?pokemon=todos&pagina=<?php echo $paginaActual - 1; ?>">Anterior</a>
        <?php else: ?>
            <div class="invisible"></div>
        <?php endif; ?>

        <!-- Creamos botón de Siguiente -->
        <?php if ($fin < count($todosPokemon)): ?>
            <a class="right" href="?pokemon=todos&pagina=<?php echo $paginaActual + 1; ?>">Siguiente</a>
        <?php endif; ?>
    </nav>
    <?php
}

/**
 * Inicializa la barra de progreso para cargar los datos de los pokémon.
 *
 * Esta función crea una barra de progreso HTML y un script JavaScript para actualizarla
 * dinámicamente mientras se cargan los datos de los pokémon.
 *
 * @param array $pokemonPaginaActual Un array que contiene los datos de los pokémon de la página actual.
 * @return void No devuelve ningún valor, ya que imprime directamente el código HTML y JavaScript.
 */
function iniciarBarra($pokemonPaginaActual) {
    // Número total de pokémon a cargar
    $totalPokemon = count($pokemonPaginaActual);

    // Iniciar la barra de progreso
    echo '<script>
    const progressBar = document.querySelector(".progress-bar");
    const total = ' . $totalPokemon . '; // Pasar el total de pokémon al JavaScript
    let progress = 0;

    function actualizarBarra() {
        progress++;
        progressBar.style.width = ((progress / total) * 100) + "%";
    }
    </script>';
}

/**
 * Oculta la barra de progreso.
 *
 * Esta función ejecuta un script JavaScript que oculta el contenedor de la barra de progreso.
 *
 * @return void No devuelve ningún valor, ya que imprime directamente el código JavaScript.
 */
function ocultarBarra() {
    echo '<script>
    document.querySelector(".progress-bar-container").style.display = "none";
    </script>';
}

?>