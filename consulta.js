$(document).ready(function() {
    // Función para buscar sugerencias de nombres
    function obtenerSugerencias(nombre) {
        if (nombre.length === 0) {
            $('#suggestions').empty(); // Limpiar sugerencias si no hay texto
            return;
        }

        $.ajax({
            url: 'buscar_nombres.php', // Archivo PHP para manejar la búsqueda de nombres
            method: 'GET',
            data: { nombre: nombre },
            dataType: 'json',
            success: function(data) {
                $('#suggestions').empty(); // Limpiar sugerencias anteriores

                if (data.length > 0) {
                    $.each(data, function(index, sugerencia) {
                        $('#suggestions').append('<div class="autocomplete-suggestion" data-nombre="' + sugerencia.nombre + '">' + sugerencia.nombre + '</div>');
                    });
                }
            }
        });
    }

    // Función para buscar usuarios
    function buscarUsuario() {
        const searchTerm = $('#nombre').val().trim();
        $('#resultados').html('<p>Buscando...</p>'); // Mensaje de carga

        $.ajax({
            url: 'buscar_usuarios.php', // Archivo PHP para manejar la búsqueda
            method: 'GET',
            data: { nombre: searchTerm }, // Enviando el término de búsqueda
            dataType: 'json',
            success: function(data) {
                $('#tablaResultados tbody').html(''); // Limpiar la tabla antes de llenarla

                if (data.length > 0) {
                    $.each(data, function(index, usuario) {
                        $('#tablaResultados tbody').append('<tr>' +
                            '<td>' + usuario.nombre + '</td>' +
                            '<td>' + usuario.usuario + '</td>' +
                            '<td>' + usuario.puesto + '</td>' +
                            '<td>' + usuario.estado + '</td>' +
                            '</tr>');
                    });
                } else {
                    $('#tablaResultados tbody').append('<tr><td colspan="4">No se encontraron resultados.</td></tr>');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#resultados').html('<p>Error en la búsqueda: ' + textStatus + '</p>');
            }
        });
    }

    // Evento para autocompletar al escribir
    $('#nombre').on('input', function() {
        const searchTerm = $(this).val();
        obtenerSugerencias(searchTerm); // Llama a la función para obtener sugerencias
    });

    // Evento para buscar al presionar "Enter"
    $('#nombre').on('keyup', function(event) {
        if (event.key === 'Enter') {
            buscarUsuario();
        }
    });

    // Evento para el botón de búsqueda
    $('#btnBuscar').on('click', function() {
        buscarUsuario();
    });

    // Evento para seleccionar una sugerencia
    $(document).on('click', '.autocomplete-suggestion', function() {
        const nombreSeleccionado = $(this).data('nombre');
        $('#nombre').val(nombreSeleccionado);
        $('#suggestions').empty(); // Limpiar sugerencias
        buscarUsuario(); // Ejecutar búsqueda con el nombre seleccionado
    });
});
