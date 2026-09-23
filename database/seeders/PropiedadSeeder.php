<?php

namespace Database\Seeders;

use App\Models\Consulta;
use App\Models\Propiedad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Propiedades de ejemplo. Las 4 primeras son las que estaban en las páginas
 * HTML de la primera versión del sitio; el resto se agregaron para que el
 * listado, los filtros y la paginación tengan con qué trabajar.
 *
 * Las fotos están en database/seeders/imagenes (sacadas de Pexels, uso libre)
 * y se copian a public/uploads como si se hubieran subido desde el panel.
 */
class PropiedadSeeder extends Seeder
{
    public function run(): void
    {
        // Si ya hay propiedades no hago nada, para no duplicar ni pisar datos reales.
        if (Propiedad::exists()) {
            $this->command?->warn('Ya hay propiedades cargadas: se omiten las de ejemplo.');

            return;
        }

        // Como la tabla está vacía, cualquier foto que haya quedado de antes está huérfana.
        Storage::disk('uploads')->deleteDirectory('propiedades');

        foreach ($this->propiedades() as $datos) {
            $fotos = $datos['fotos'];
            unset($datos['fotos']);

            $propiedad = Propiedad::create($datos);
            $propiedad->forceFill(['visitas' => rand(15, 250)])->saveQuietly();

            foreach ($fotos as $orden => [$archivo, $descripcion]) {
                $ruta = 'propiedades/'.$propiedad->id.'/'.$archivo.'.jpg';
                Storage::disk('uploads')->put($ruta, File::get(__DIR__.'/imagenes/'.$archivo.'.jpg'));

                $propiedad->imagenes()->create([
                    'ruta' => $ruta,
                    'descripcion' => $descripcion,
                    'orden' => $orden,
                ]);
            }
        }

        $this->crearConsultasDeEjemplo();
    }

    /**
     * Algunas consultas para que el panel no arranque vacío.
     */
    private function crearConsultasDeEjemplo(): void
    {
        $ejemplos = [
            ['Lucía Fernández', 'lucia.fernandez@example.com', '261 4556677', 'casa-en-venta-en-chacras-de-coria', 'Hola, ¿la casa se puede visitar este sábado a la mañana? ¿Aceptan permuta por un departamento en Ciudad?'],
            ['Martín Gómez', 'martin.gomez@example.com', '261 5123456', 'departamento-en-alquiler-en-godoy-cruz', 'Buenas tardes, ¿qué requisitos piden para alquilar? Tengo garantía propietaria en Mendoza.'],
            ['Sofía Ruiz', 'sofia.ruiz@example.com', null, null, 'Quisiera tasar un departamento de 2 dormitorios en la Quinta Sección para ponerlo en venta. ¿Cómo coordinamos?'],
            ['Diego Morales', 'dmorales@example.com', '261 6789012', 'local-comercial-en-ciudad', '¿Cuál es el valor del alquiler del local? Me interesa para una cafetería.'],
        ];

        foreach ($ejemplos as $indice => [$nombre, $email, $telefono, $slug, $mensaje]) {
            $consulta = Consulta::create([
                'propiedad_id' => $slug ? Propiedad::where('slug', $slug)->value('id') : null,
                'nombre' => $nombre,
                'email' => $email,
                'telefono' => $telefono,
                'mensaje' => $mensaje,
            ]);

            // Fechas escalonadas y las dos más viejas ya leídas
            $consulta->forceFill([
                'created_at' => now()->subDays(($indice + 1) * 2),
                'leida_at' => $indice >= 2 ? now()->subDay() : null,
            ])->save();
        }
    }

    private function propiedades(): array
    {
        return [
            [
                'titulo' => 'Casa en Venta en Chacras de Coria',
                'operacion' => 'venta',
                'tipo' => 'casa',
                'localidad' => 'Luján de Cuyo',
                'direccion' => 'Calle Viamonte 1234, Chacras de Coria',
                'latitud' => -32.9869,
                'longitud' => -68.8766,
                'resumen' => 'Amplia casa con 3 dormitorios, 2 baños, jardín y piscina. Excelente ubicación residencial.',
                'descripcion' => 'Esta magnífica propiedad ubicada en el corazón de Chacras de Coria ofrece un estilo de vida único. Con amplios espacios verdes y una construcción de primera calidad, es ideal para una familia que busca confort y tranquilidad. La casa cuenta con un living comedor espacioso y luminoso, cocina totalmente equipada y un hermoso jardín con piscina y quincho.',
                'precio' => 250000,
                'moneda' => 'USD',
                'ambientes' => 5,
                'dormitorios' => 3,
                'banos' => 2,
                'cocheras' => 2,
                'superficie_total' => 600,
                'superficie_cubierta' => 220,
                'antiguedad' => 12,
                'extras' => ['Piscina', 'Quincho', 'Riego por aspersión', 'Cocina equipada', 'Comedor'],
                'destacada' => true,
                'fotos' => [
                    ['chacras-fachada', 'Fachada de la casa'],
                    ['chacras-interior', 'Interior de la casa'],
                    ['chacras-jardin', 'Jardín de la casa'],
                ],
            ],
            [
                'titulo' => 'Departamento en Alquiler en Godoy Cruz',
                'operacion' => 'alquiler',
                'tipo' => 'departamento',
                'localidad' => 'Godoy Cruz',
                'direccion' => 'Calle Mitre 456',
                'latitud' => -32.9265,
                'longitud' => -68.8440,
                'resumen' => 'Moderno departamento de 2 dormitorios, muy luminoso, con balcón y cochera. Cerca de la ciclovía.',
                'descripcion' => "Moderno departamento en alquiler ubicado en el corazón de Godoy Cruz, ideal para quienes buscan comodidad y cercanía a servicios. La unidad cuenta con 65 m² bien distribuidos: living-comedor muy luminoso con salida al balcón, cocina integrada equipada con muebles modernos e isla desayunadora, dos dormitorios con placard empotrado y un baño completo con excelentes terminaciones.\n\nEl edificio ofrece acceso seguro, cochera cubierta y espacio de guardado. La orientación favorece la entrada de luz natural durante todo el día y los materiales de la vivienda son de calidad. Está a 2 cuadras de la ciclovía, con acceso rápido a transporte público, supermercados y centros gastronómicos.",
                'precio' => 900000,
                'moneda' => 'ARS',
                'expensas' => 25000,
                'ambientes' => 3,
                'dormitorios' => 2,
                'banos' => 1,
                'cocheras' => 1,
                'superficie_total' => 65,
                'superficie_cubierta' => 60,
                'antiguedad' => 5,
                'extras' => ['Balcón', 'Cochera cubierta', 'Placares empotrados', 'Isla desayunadora'],
                'destacada' => true,
                'fotos' => [
                    ['godoy-living', 'Living luminoso del departamento'],
                    ['godoy-cocina', 'Cocina integrada moderna'],
                    ['godoy-dormitorio', 'Dormitorio principal'],
                ],
            ],
            [
                'titulo' => 'Terreno en Luján de Cuyo',
                'operacion' => 'venta',
                'tipo' => 'terreno',
                'localidad' => 'Luján de Cuyo',
                'direccion' => 'Barrio privado a 10 minutos del centro de Luján',
                'latitud' => -33.0360,
                'longitud' => -68.8790,
                'resumen' => 'Lote de 500 m² en barrio privado con seguridad 24 hs. Ideal para construir la casa de tus sueños.',
                'descripcion' => "Excelente lote de 500 m² ubicado en un barrio privado de Luján de Cuyo, ideal para construir la casa de tus sueños. El terreno es plano y regular, con orientación norte que garantiza buena luminosidad durante todo el día y vistas despejadas hacia áreas verdes y cerros cercanos.\n\nForma parte de un complejo con seguridad 24 horas, calles internas pavimentadas y alumbrado público; cuenta con conexiones disponibles a red eléctrica, agua potable y gas natural a la vereda. La normativa del barrio permite viviendas unifamiliares de baja densidad, lo que asegura privacidad y calidad de entorno.",
                'precio' => 120000,
                'moneda' => 'USD',
                'superficie_total' => 500,
                'extras' => ['Seguridad 24 hs', 'Calles pavimentadas', 'Agua, luz y gas a la vereda', 'Orientación norte'],
                'destacada' => true,
                'fotos' => [
                    ['lujan-lote', 'Lote amplio con vista a los cerros'],
                    ['lujan-barrio', 'Vista aérea del barrio'],
                    ['lujan-vista', 'Alrededores del lote'],
                ],
            ],
            [
                'titulo' => 'Local Comercial en Ciudad',
                'operacion' => 'alquiler',
                'tipo' => 'local',
                'localidad' => 'Ciudad de Mendoza',
                'direccion' => 'Calle San Martín 1200',
                'latitud' => -32.8870,
                'longitud' => -68.8390,
                'resumen' => 'Excelente local comercial sobre calle transitada. 80 m² con baño y pequeño depósito.',
                'descripcion' => "Excelente local comercial disponible en zona de alto tránsito de la ciudad, ideal para rubros gastronómicos, comercio minorista o showroom. El espacio cuenta con 80 m² útiles distribuidos en planta libre con gran vidriera, baño completo y pequeño depósito en la parte posterior.\n\nEl local posee instalación eléctrica renovada, conexión de agua y desagüe, persiana metálica de seguridad y aire acondicionado instalado. Su ubicación sobre una avenida comercial asegura un flujo constante de peatones, acceso a transporte público y proximidad a bancos y comercios.",
                'precio' => null, // Consultar
                'moneda' => 'ARS',
                'banos' => 1,
                'superficie_total' => 80,
                'superficie_cubierta' => 80,
                'extras' => ['Vidriera amplia', 'Persiana metálica', 'Aire acondicionado', 'Depósito'],
                'fotos' => [
                    ['local-fachada', 'Fachada y vidriera del local'],
                    ['local-interior', 'Interior del local'],
                ],
            ],
            [
                'titulo' => 'Casa en Alquiler en Guaymallén',
                'operacion' => 'alquiler',
                'tipo' => 'casa',
                'localidad' => 'Guaymallén',
                'direccion' => 'Barrio Dalvian Este, calle Los Álamos 320',
                'latitud' => -32.9050,
                'longitud' => -68.8130,
                'resumen' => 'Casa de 3 dormitorios con patio, parrilla y cochera doble. Barrio tranquilo cerca de accesos.',
                'descripcion' => "Linda casa en alquiler en zona residencial de Guaymallén, a pocos minutos del Acceso Este. Cuenta con living comedor con hogar, cocina separada con lavadero, tres dormitorios (el principal en suite) y un segundo baño completo.\n\nAl fondo tiene un patio con césped y parrilla techada, ideal para reuniones. Cochera para dos autos. Se aceptan mascotas.",
                'precio' => 1200000,
                'moneda' => 'ARS',
                'ambientes' => 5,
                'dormitorios' => 3,
                'banos' => 2,
                'cocheras' => 2,
                'superficie_total' => 300,
                'superficie_cubierta' => 150,
                'antiguedad' => 15,
                'extras' => ['Parrilla', 'Hogar a leña', 'Patio con césped', 'Acepta mascotas'],
                'fotos' => [
                    ['guaymallen-fachada', 'Frente de la casa'],
                    ['guaymallen-comedor', 'Living comedor'],
                    ['guaymallen-dormitorio', 'Dormitorio principal'],
                ],
            ],
            [
                'titulo' => 'Departamento en Venta en Quinta Sección',
                'operacion' => 'venta',
                'tipo' => 'departamento',
                'localidad' => 'Ciudad de Mendoza',
                'direccion' => 'Av. Emilio Civit 550, Quinta Sección',
                'latitud' => -32.8830,
                'longitud' => -68.8560,
                'resumen' => 'Departamento de 2 dormitorios a estrenar, a metros del Parque General San Martín. Amenities.',
                'descripcion' => "Departamento a estrenar en edificio de categoría sobre Av. Emilio Civit, a metros del Parque General San Martín. Living comedor con ventanales y balcón corrido, cocina integrada con mesada de granito, dos dormitorios con placard y dos baños.\n\nEl edificio cuenta con pileta en la terraza, SUM, gimnasio y seguridad las 24 horas. Cochera y baulera incluidas.",
                'precio' => 145000,
                'moneda' => 'USD',
                'expensas' => 60000,
                'ambientes' => 3,
                'dormitorios' => 2,
                'banos' => 2,
                'cocheras' => 1,
                'superficie_total' => 85,
                'superficie_cubierta' => 78,
                'antiguedad' => 0,
                'extras' => ['Pileta', 'SUM', 'Gimnasio', 'Seguridad 24 hs', 'Baulera'],
                'destacada' => true,
                'fotos' => [
                    ['quinta-edificio', 'Frente del edificio'],
                    ['quinta-living', 'Living comedor'],
                    ['quinta-cocina', 'Cocina integrada'],
                ],
            ],
            [
                'titulo' => 'Casa Moderna con Pileta en Vistalba',
                'operacion' => 'venta',
                'tipo' => 'casa',
                'localidad' => 'Luján de Cuyo',
                'direccion' => 'Barrio cerrado, Vistalba',
                'latitud' => -33.0180,
                'longitud' => -68.9200,
                'resumen' => 'Casa de diseño en barrio cerrado con vista a la cordillera, 4 dormitorios y pileta.',
                'descripcion' => "Casa de arquitectura moderna en uno de los barrios cerrados más buscados de Vistalba, con vista directa a la cordillera. Planta baja con amplio living comedor de doble altura, cocina con isla, escritorio y suite principal con vestidor.\n\nEn planta alta, tres dormitorios y baño completo. Galería con parrilla, pileta climatizada y jardín parquizado con riego automático. Calefacción por losa radiante.",
                'precio' => 420000,
                'moneda' => 'USD',
                'ambientes' => 7,
                'dormitorios' => 4,
                'banos' => 3,
                'cocheras' => 2,
                'superficie_total' => 1000,
                'superficie_cubierta' => 320,
                'antiguedad' => 3,
                'extras' => ['Pileta climatizada', 'Losa radiante', 'Vista a la cordillera', 'Riego automático', 'Parrilla'],
                'destacada' => true,
                'fotos' => [
                    ['vistalba-fachada', 'Vista de la casa y la pileta'],
                    ['vistalba-comedor', 'Comedor'],
                    ['vistalba-living', 'Living'],
                ],
            ],
            [
                'titulo' => 'Oficina en Alquiler en Microcentro',
                'operacion' => 'alquiler',
                'tipo' => 'oficina',
                'localidad' => 'Ciudad de Mendoza',
                'direccion' => 'Av. España 1050, piso 6',
                'latitud' => -32.8920,
                'longitud' => -68.8410,
                'resumen' => 'Oficina de 120 m² en planta libre con sala de reuniones. Edificio corporativo con recepción.',
                'descripcion' => "Oficina en edificio corporativo del microcentro, ideal para estudios o empresas. Planta libre de 120 m² con capacidad para 15 puestos de trabajo, sala de reuniones vidriada, kitchenette y dos baños.\n\nPiso técnico, aire acondicionado central y excelente iluminación natural. El edificio tiene recepción, control de acceso y cocheras opcionales.",
                'precio' => 1800000,
                'moneda' => 'ARS',
                'expensas' => 150000,
                'banos' => 2,
                'superficie_total' => 120,
                'superficie_cubierta' => 120,
                'antiguedad' => 10,
                'extras' => ['Sala de reuniones', 'Aire acondicionado central', 'Recepción', 'Kitchenette'],
                'fotos' => [
                    ['oficina-planta', 'Planta libre'],
                    ['oficina-puestos', 'Puestos de trabajo'],
                    ['oficina-sala', 'Sala de trabajo'],
                ],
            ],
            [
                'titulo' => 'Finca con Viñedo en Maipú',
                'operacion' => 'venta',
                'tipo' => 'finca',
                'localidad' => 'Maipú',
                'direccion' => 'Carril Barrancas, Russell',
                'latitud' => -33.0100,
                'longitud' => -68.7500,
                'resumen' => 'Finca de 5 hectáreas con viñedo en producción, casa principal y derecho de riego.',
                'descripcion' => "Finca de 5 hectáreas en la zona de Russell, Maipú, con 4 hectáreas implantadas con Malbec y Cabernet Sauvignon en producción. Cuenta con derecho de riego y perforación propia.\n\nCasa principal de 180 m² con tres dormitorios, galería y parque, más casa para el cuidador y galpón para maquinaria. Excelente oportunidad para proyecto enoturístico.",
                'precio' => 380000,
                'moneda' => 'USD',
                'dormitorios' => 3,
                'banos' => 2,
                'superficie_total' => 50000,
                'superficie_cubierta' => 250,
                'antiguedad' => 30,
                'extras' => ['Viñedo en producción', 'Derecho de riego', 'Perforación', 'Casa de cuidador', 'Galpón'],
                'fotos' => [
                    ['maipu-vinedo', 'Viñedo de la finca'],
                    ['maipu-casa', 'Casa principal'],
                    ['maipu-exterior', 'Exterior de la casa'],
                ],
            ],
            [
                'titulo' => 'Casa en Venta en Las Heras',
                'operacion' => 'venta',
                'tipo' => 'casa',
                'localidad' => 'Las Heras',
                'direccion' => 'Barrio Municipal, calle Independencia 780',
                'latitud' => -32.8500,
                'longitud' => -68.8250,
                'resumen' => 'Casa familiar de 3 dormitorios con cochera y patio. Apta crédito hipotecario.',
                'descripcion' => "Casa familiar en barrio consolidado de Las Heras, cerca de escuelas, centro de salud y paradas de colectivo. Living, cocina comedor amplia, tres dormitorios y un baño completo.\n\nCochera cubierta, patio con parrilla y lavadero independiente. La propiedad está apta para crédito hipotecario.",
                'precio' => 85000,
                'moneda' => 'USD',
                'ambientes' => 4,
                'dormitorios' => 3,
                'banos' => 1,
                'cocheras' => 1,
                'superficie_total' => 250,
                'superficie_cubierta' => 120,
                'antiguedad' => 25,
                'extras' => ['Apta crédito', 'Parrilla', 'Lavadero'],
                'fotos' => [
                    ['lasheras-fachada', 'Frente de la casa'],
                    ['lasheras-cocina', 'Cocina comedor'],
                    ['lasheras-living', 'Living'],
                ],
            ],
            [
                'titulo' => 'Dúplex en Alquiler en Godoy Cruz',
                'operacion' => 'alquiler',
                'tipo' => 'ph',
                'localidad' => 'Godoy Cruz',
                'direccion' => 'Calle Rivadavia 1520',
                'latitud' => -32.9350,
                'longitud' => -68.8500,
                'resumen' => 'Dúplex de 2 dormitorios en complejo cerrado, con patio propio y cochera.',
                'descripcion' => "Dúplex en complejo cerrado de pocas unidades. En planta baja: living comedor, cocina y toilette, con salida a patio propio. En planta alta: dos dormitorios con placard y baño completo.\n\nCochera descubierta dentro del complejo. Ideal para parejas o familias pequeñas.",
                'precio' => 950000,
                'moneda' => 'ARS',
                'expensas' => 30000,
                'ambientes' => 3,
                'dormitorios' => 2,
                'banos' => 2,
                'cocheras' => 1,
                'superficie_total' => 110,
                'superficie_cubierta' => 90,
                'antiguedad' => 8,
                'extras' => ['Patio propio', 'Complejo cerrado', 'Toilette'],
                'fotos' => [
                    ['duplex-fachada', 'Frente del dúplex'],
                    ['duplex-escalera', 'Living con escalera'],
                    ['duplex-dormitorio', 'Dormitorio'],
                ],
            ],
            [
                'titulo' => 'Departamento de 1 Dormitorio en Ciudad',
                'operacion' => 'alquiler',
                'tipo' => 'departamento',
                'localidad' => 'Ciudad de Mendoza',
                'direccion' => 'Calle Arístides Villanueva 350',
                'latitud' => -32.8890,
                'longitud' => -68.8530,
                'resumen' => 'Departamento de 1 dormitorio sobre Arístides, ideal para estudiantes o profesionales.',
                'descripcion' => "Departamento luminoso a una cuadra de la calle Arístides Villanueva, en plena zona gastronómica. Living comedor con cocina integrada, un dormitorio con vista abierta y baño completo.\n\nSe alquila con algunos muebles (cama, mesa y heladera). Cerca de universidades, del Parque y de líneas de colectivo.",
                'precio' => 650000,
                'moneda' => 'ARS',
                'expensas' => 20000,
                'ambientes' => 2,
                'dormitorios' => 1,
                'banos' => 1,
                'superficie_total' => 45,
                'superficie_cubierta' => 42,
                'antiguedad' => 20,
                'extras' => ['Semi amueblado', 'Cerca de universidades'],
                'fotos' => [
                    ['centro-living', 'Living'],
                    ['centro-cocina', 'Cocina'],
                    ['centro-dormitorio', 'Dormitorio con vista'],
                ],
            ],
        ];
    }
}
