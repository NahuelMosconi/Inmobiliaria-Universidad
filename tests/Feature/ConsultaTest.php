<?php

namespace Tests\Feature;

use App\Mail\NuevaConsulta;
use App\Models\Consulta;
use App\Models\Propiedad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ConsultaTest extends TestCase
{
    use RefreshDatabase;

    private function datosValidos(array $cambios = []): array
    {
        return array_merge([
            'nombre' => 'Juan Pérez',
            'telefono' => '261 1234567',
            'email' => 'juan@example.com',
            'mensaje' => 'Hola, quisiera más información por favor.',
        ], $cambios);
    }

    public function test_guarda_la_consulta_y_redirige_a_gracias(): void
    {
        Mail::fake();

        $this->post('/contacto', $this->datosValidos())->assertRedirect('/gracias');

        $this->assertDatabaseHas('consultas', ['email' => 'juan@example.com', 'propiedad_id' => null]);
        Mail::assertSent(NuevaConsulta::class);
    }

    public function test_el_mail_de_aviso_tiene_los_datos_de_la_consulta(): void
    {
        $propiedad = Propiedad::factory()->create(['titulo' => 'Casa en Chacras']);
        $consulta = Consulta::factory()->create([
            'propiedad_id' => $propiedad->id,
            'nombre' => 'Ana López',
            'email' => 'ana@example.com',
        ]);

        $mail = new NuevaConsulta($consulta);

        $mail->assertHasSubject('Nueva consulta por: Casa en Chacras');
        $mail->assertHasReplyTo('ana@example.com');
        $mail->assertSeeInHtml('Ana López');
        $mail->assertSeeInHtml($consulta->mensaje);
    }

    public function test_la_consulta_desde_una_propiedad_queda_asociada(): void
    {
        Mail::fake();
        $propiedad = Propiedad::factory()->create();

        $this->from('/propiedades/'.$propiedad->slug)
            ->post('/contacto', $this->datosValidos(['propiedad_id' => $propiedad->id]))
            ->assertRedirect('/propiedades/'.$propiedad->slug)
            ->assertSessionHas('exito');

        $this->assertSame($propiedad->id, Consulta::first()->propiedad_id);
    }

    public function test_valida_los_campos_obligatorios(): void
    {
        $this->post('/contacto', [])
            ->assertSessionHasErrors(['nombre', 'email', 'mensaje']);

        $this->assertDatabaseCount('consultas', 0);
    }

    public function test_rechaza_email_y_telefono_invalidos(): void
    {
        $this->post('/contacto', $this->datosValidos(['email' => 'no-es-un-mail', 'telefono' => 'abc']))
            ->assertSessionHasErrors(['email', 'telefono']);
    }

    public function test_responde_json_cuando_se_envia_con_javascript(): void
    {
        Mail::fake();

        $this->postJson('/contacto', $this->datosValidos())
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->postJson('/contacto', ['nombre' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['nombre', 'email', 'mensaje']);
    }

    public function test_el_campo_trampa_descarta_a_los_bots(): void
    {
        Mail::fake();

        $this->post('/contacto', $this->datosValidos(['sitio_web' => 'http://spam.com']))
            ->assertRedirect('/gracias');

        $this->assertDatabaseCount('consultas', 0);
        Mail::assertNothingSent();
    }

    public function test_el_texto_con_html_se_guarda_tal_cual_y_se_muestra_escapado(): void
    {
        // La versión vieja era vulnerable a inyección SQL. Ahora se usan consultas
        // preparadas (Eloquent) y Blade escapa todo lo que se muestra.
        Mail::fake();
        $mensaje = "<script>alert('hola')</script> '; DROP TABLE consultas; --";

        $this->post('/contacto', $this->datosValidos(['mensaje' => $mensaje]));

        $consulta = Consulta::first();
        $this->assertSame($mensaje, $consulta->mensaje);

        // En el panel se ve como texto, no se ejecuta
        $this->actingAs(User::factory()->create())
            ->get('/admin/consultas/'.$consulta->id)
            ->assertDontSee("<script>alert('hola')</script>", false)
            ->assertSee("<script>alert('hola')</script>");
    }

    public function test_limita_la_cantidad_de_envios_por_minuto(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/contacto', $this->datosValidos())->assertRedirect();
        }

        $this->post('/contacto', $this->datosValidos())->assertStatus(429);
    }
}
