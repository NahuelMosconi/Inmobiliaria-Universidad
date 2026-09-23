<?php

namespace Tests\Feature;

use App\Models\Propiedad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitioPublicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_las_paginas_principales_cargan(): void
    {
        Propiedad::factory()->count(3)->create();

        foreach (['/', '/propiedades', '/nosotros', '/contacto', '/favoritos', '/gracias'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_el_inicio_muestra_las_destacadas(): void
    {
        $destacada = Propiedad::factory()->destacada()->create(['titulo' => 'Casa destacada de prueba']);

        $this->get('/')->assertSee($destacada->titulo);
    }

    public function test_el_listado_no_muestra_propiedades_ocultas(): void
    {
        Propiedad::factory()->create(['titulo' => 'Visible en el sitio']);
        Propiedad::factory()->oculta()->create(['titulo' => 'Oculta del sitio']);

        $this->get('/propiedades')
            ->assertSee('Visible en el sitio')
            ->assertDontSee('Oculta del sitio');
    }

    public function test_filtra_por_operacion_y_tipo(): void
    {
        Propiedad::factory()->create(['titulo' => 'Casa que se vende', 'operacion' => 'venta', 'tipo' => 'casa']);
        Propiedad::factory()->create(['titulo' => 'Depto que se alquila', 'operacion' => 'alquiler', 'tipo' => 'departamento']);

        $this->get('/propiedades?operacion=venta&tipo=casa')
            ->assertSee('Casa que se vende')
            ->assertDontSee('Depto que se alquila');
    }

    public function test_filtra_por_rango_de_precio_en_la_misma_moneda(): void
    {
        Propiedad::factory()->create(['titulo' => 'Barata', 'precio' => 50000, 'moneda' => 'USD']);
        Propiedad::factory()->create(['titulo' => 'Cara', 'precio' => 500000, 'moneda' => 'USD']);
        Propiedad::factory()->create(['titulo' => 'En pesos', 'precio' => 100000, 'moneda' => 'ARS']);

        $this->get('/propiedades?moneda=USD&precio_min=40000&precio_max=200000')
            ->assertSee('Barata')
            ->assertDontSee('Cara')
            ->assertDontSee('En pesos');
    }

    public function test_los_filtros_invalidos_se_ignoran(): void
    {
        Propiedad::factory()->create(['titulo' => 'Cualquier propiedad']);

        $this->get('/propiedades?operacion=inventada&tipo=castillo&precio_min=abc&orden=cualquiera')
            ->assertOk()
            ->assertSee('Cualquier propiedad');
    }

    public function test_ordena_por_precio(): void
    {
        Propiedad::factory()->create(['titulo' => 'Precio medio', 'precio' => 200000, 'moneda' => 'USD']);
        Propiedad::factory()->create(['titulo' => 'Precio bajo', 'precio' => 100000, 'moneda' => 'USD']);
        Propiedad::factory()->create(['titulo' => 'Sin precio', 'precio' => null]);

        // Las que no tienen precio ("Consultar") van al final
        $this->get('/propiedades?orden=precio_asc')
            ->assertSeeInOrder(['Precio bajo', 'Precio medio', 'Sin precio']);
    }

    public function test_muestra_el_detalle_de_una_propiedad(): void
    {
        $propiedad = Propiedad::factory()->create([
            'titulo' => 'Casa en Chacras',
            'precio' => 250000,
            'moneda' => 'USD',
            'operacion' => 'venta',
            'extras' => ['Piscina', 'Quincho'],
        ]);

        $this->get('/propiedades/'.$propiedad->slug)
            ->assertOk()
            ->assertSee('Casa en Chacras')
            ->assertSee('USD 250.000')
            ->assertSee('Piscina');
    }

    public function test_el_detalle_suma_una_visita_por_sesion(): void
    {
        $propiedad = Propiedad::factory()->create();

        $this->get('/propiedades/'.$propiedad->slug);
        $this->get('/propiedades/'.$propiedad->slug);

        $this->assertSame(1, $propiedad->fresh()->visitas);
    }

    public function test_una_propiedad_oculta_da_404_salvo_para_el_admin(): void
    {
        $propiedad = Propiedad::factory()->oculta()->create();

        $this->get('/propiedades/'.$propiedad->slug)->assertNotFound();

        $this->actingAs(User::factory()->create())
            ->get('/propiedades/'.$propiedad->slug)
            ->assertOk()
            ->assertSee('Esta propiedad está oculta');
    }

    public function test_el_slug_se_genera_solo_y_no_se_repite(): void
    {
        $primera = Propiedad::factory()->create(['titulo' => 'Casa en Maipú']);
        $segunda = Propiedad::factory()->create(['titulo' => 'Casa en Maipú']);

        $this->assertSame('casa-en-maipu', $primera->slug);
        $this->assertSame('casa-en-maipu-2', $segunda->slug);
    }

    public function test_formatea_el_precio(): void
    {
        $venta = Propiedad::factory()->make(['precio' => 120000, 'moneda' => 'USD', 'operacion' => 'venta']);
        $alquiler = Propiedad::factory()->make(['precio' => 900000, 'moneda' => 'ARS', 'operacion' => 'alquiler']);
        $consultar = Propiedad::factory()->make(['precio' => null]);

        $this->assertSame('USD 120.000', $venta->precio_formateado);
        $this->assertSame('$ 900.000 / mes', $alquiler->precio_formateado);
        $this->assertSame('Consultar', $consultar->precio_formateado);
    }

    public function test_favoritos_devuelve_solo_propiedades_publicadas(): void
    {
        $publicada = Propiedad::factory()->create();
        $oculta = Propiedad::factory()->oculta()->create();

        $this->getJson('/favoritos/datos?ids[]='.$publicada->id.'&ids[]='.$oculta->id)
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $publicada->id);
    }
}
