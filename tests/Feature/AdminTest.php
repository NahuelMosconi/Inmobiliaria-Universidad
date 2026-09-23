<?php

namespace Tests\Feature;

use App\Models\Consulta;
use App\Models\Propiedad;
use App\Models\PropiedadImagen;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Las fotos se guardan en un disco falso para no ensuciar public/uploads
        Storage::fake('uploads');

        $this->admin = User::factory()->create([
            'email' => 'admin@triangulo.com',
            'password' => 'secreto123',
        ]);
    }

    private function datosPropiedad(array $cambios = []): array
    {
        return array_merge([
            'titulo' => 'Casa nueva en Godoy Cruz',
            'operacion' => 'venta',
            'tipo' => 'casa',
            'localidad' => 'Godoy Cruz',
            'resumen' => 'Casa de 3 dormitorios con patio.',
            'descripcion' => 'Descripción larga de la casa.',
            'precio' => '150.000',
            'moneda' => 'USD',
            'dormitorios' => 3,
            'superficie_total' => 300,
            'superficie_cubierta' => 150,
            'extras' => "Piscina\nQuincho, Parrilla",
            'publicada' => '1',
        ], $cambios);
    }

    /* ---------------- Login ---------------- */

    public function test_el_panel_pide_iniciar_sesion(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/propiedades')->assertRedirect('/admin/login');
        $this->get('/admin/consultas')->assertRedirect('/admin/login');
    }

    public function test_inicia_sesion_con_datos_correctos(): void
    {
        $this->post('/admin/login', ['email' => 'admin@triangulo.com', 'password' => 'secreto123'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_no_inicia_sesion_con_contrasena_incorrecta(): void
    {
        $this->post('/admin/login', ['email' => 'admin@triangulo.com', 'password' => 'otra'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_cierra_sesion(): void
    {
        $this->actingAs($this->admin)->post('/admin/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_las_pantallas_del_panel_cargan(): void
    {
        $propiedad = Propiedad::factory()->create();
        $consulta = Consulta::factory()->create();

        $this->actingAs($this->admin);

        foreach ([
            '/admin',
            '/admin/propiedades',
            '/admin/propiedades/crear',
            '/admin/propiedades/'.$propiedad->slug.'/editar',
            '/admin/consultas',
            '/admin/consultas/'.$consulta->id,
            '/admin/perfil',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    /* ---------------- Propiedades ---------------- */

    public function test_crea_una_propiedad_con_fotos(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/propiedades', $this->datosPropiedad([
                'imagenes' => [
                    UploadedFile::fake()->create('frente.jpg', 300, 'image/jpeg'),
                    UploadedFile::fake()->create('patio.png', 300, 'image/png'),
                ],
            ]))
            ->assertRedirect('/admin/propiedades/casa-nueva-en-godoy-cruz/editar');

        $propiedad = Propiedad::first();

        $this->assertSame('150000.00', $propiedad->precio); // se sacó el punto de miles
        $this->assertSame(['Piscina', 'Quincho', 'Parrilla'], $propiedad->extras);
        $this->assertTrue($propiedad->publicada);
        $this->assertFalse($propiedad->destacada); // el checkbox no se envió
        $this->assertCount(2, $propiedad->imagenes);

        foreach ($propiedad->imagenes as $imagen) {
            Storage::disk('uploads')->assertExists($imagen->ruta);
        }
    }

    public function test_valida_los_datos_de_la_propiedad(): void
    {
        $this->actingAs($this->admin)
            ->post('/admin/propiedades', $this->datosPropiedad([
                'titulo' => '',
                'tipo' => 'castillo',
                'superficie_cubierta' => 500, // mayor que la total (300)
                'imagenes' => [UploadedFile::fake()->create('virus.exe', 10)],
            ]))
            ->assertSessionHasErrors(['titulo', 'tipo', 'superficie_cubierta', 'imagenes.0']);

        $this->assertDatabaseCount('propiedades', 0);
    }

    public function test_edita_una_propiedad(): void
    {
        $propiedad = Propiedad::factory()->create(['titulo' => 'Título viejo']);

        $this->actingAs($this->admin)
            ->put('/admin/propiedades/'.$propiedad->slug, $this->datosPropiedad(['titulo' => 'Título nuevo', 'precio' => '']))
            ->assertRedirect();

        $propiedad->refresh();
        $this->assertSame('Título nuevo', $propiedad->titulo);
        $this->assertNull($propiedad->precio); // vacío = "Consultar"
    }

    public function test_publica_y_destaca_desde_el_listado(): void
    {
        $propiedad = Propiedad::factory()->create(['publicada' => true, 'destacada' => false]);

        $this->actingAs($this->admin);
        $this->patch('/admin/propiedades/'.$propiedad->slug.'/estado', ['campo' => 'publicada']);
        $this->patch('/admin/propiedades/'.$propiedad->slug.'/estado', ['campo' => 'destacada']);

        $propiedad->refresh();
        $this->assertFalse($propiedad->publicada);
        $this->assertTrue($propiedad->destacada);

        // Solo se pueden cambiar esos dos campos
        $this->patch('/admin/propiedades/'.$propiedad->slug.'/estado', ['campo' => 'precio'])
            ->assertSessionHasErrors('campo');
    }

    public function test_eliminar_una_propiedad_borra_sus_fotos_y_conserva_las_consultas(): void
    {
        $propiedad = Propiedad::factory()->create();
        Storage::disk('uploads')->put('propiedades/'.$propiedad->id.'/foto.jpg', 'contenido');
        $propiedad->imagenes()->create(['ruta' => 'propiedades/'.$propiedad->id.'/foto.jpg']);
        $consulta = Consulta::factory()->create(['propiedad_id' => $propiedad->id]);

        $this->actingAs($this->admin)
            ->delete('/admin/propiedades/'.$propiedad->slug)
            ->assertRedirect('/admin/propiedades');

        $this->assertModelMissing($propiedad);
        $this->assertDatabaseCount('propiedad_imagenes', 0);
        Storage::disk('uploads')->assertMissing('propiedades/'.$propiedad->id.'/foto.jpg');
        $this->assertNull($consulta->fresh()->propiedad_id);
    }

    public function test_cambia_la_foto_de_portada_y_elimina_fotos(): void
    {
        $propiedad = Propiedad::factory()->create();
        $primera = $propiedad->imagenes()->create(['ruta' => 'a.jpg', 'orden' => 1]);
        $segunda = $propiedad->imagenes()->create(['ruta' => 'b.jpg', 'orden' => 2]);
        Storage::disk('uploads')->put('b.jpg', 'contenido');

        $this->actingAs($this->admin)->patch('/admin/imagenes/'.$segunda->id.'/portada');
        $this->assertTrue($propiedad->fresh()->imagenes->first()->is($segunda));

        $this->actingAs($this->admin)->delete('/admin/imagenes/'.$segunda->id);
        $this->assertModelMissing($segunda);
        Storage::disk('uploads')->assertMissing('b.jpg');
        $this->assertModelExists($primera);
    }

    public function test_no_permite_superar_el_maximo_de_fotos(): void
    {
        $propiedad = Propiedad::factory()->create();
        PropiedadImagen::factory()->count(15)->create(['propiedad_id' => $propiedad->id]);

        $this->actingAs($this->admin)
            ->put('/admin/propiedades/'.$propiedad->slug, $this->datosPropiedad([
                'imagenes' => [UploadedFile::fake()->create('una-mas.jpg', 100, 'image/jpeg')],
            ]))
            ->assertSessionHasErrors('imagenes');
    }

    /* ---------------- Consultas ---------------- */

    public function test_abrir_una_consulta_la_marca_como_leida(): void
    {
        $consulta = Consulta::factory()->create();
        $this->assertFalse($consulta->leida);

        $this->actingAs($this->admin)->get('/admin/consultas/'.$consulta->id)->assertSee($consulta->mensaje);

        $this->assertTrue($consulta->fresh()->leida);
    }

    public function test_filtra_consultas_sin_leer(): void
    {
        Consulta::factory()->create(['nombre' => 'Pendiente de leer']);
        Consulta::factory()->leida()->create(['nombre' => 'Ya estaba leída']);

        $this->actingAs($this->admin)
            ->get('/admin/consultas?estado=sin_leer')
            ->assertSee('Pendiente de leer')
            ->assertDontSee('Ya estaba leída');
    }

    public function test_marca_como_no_leida_y_elimina_una_consulta(): void
    {
        $consulta = Consulta::factory()->leida()->create();

        $this->actingAs($this->admin)->patch('/admin/consultas/'.$consulta->id.'/leida');
        $this->assertFalse($consulta->fresh()->leida);

        $this->actingAs($this->admin)->delete('/admin/consultas/'.$consulta->id);
        $this->assertModelMissing($consulta);
    }

    /* ---------------- Perfil ---------------- */

    public function test_cambia_la_contrasena_solo_con_la_actual_correcta(): void
    {
        $this->actingAs($this->admin);

        $this->put('/admin/perfil', [
            'name' => 'Admin',
            'email' => 'admin@triangulo.com',
            'password_actual' => 'incorrecta',
            'password' => 'nueva12345',
            'password_confirmation' => 'nueva12345',
        ])->assertSessionHasErrors('password_actual');

        $this->put('/admin/perfil', [
            'name' => 'Admin',
            'email' => 'admin@triangulo.com',
            'password_actual' => 'secreto123',
            'password' => 'nueva12345',
            'password_confirmation' => 'nueva12345',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('nueva12345', $this->admin->fresh()->password));
    }
}
