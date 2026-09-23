<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultaRequest;
use App\Mail\NuevaConsulta;
use App\Models\Consulta;
use App\Models\Propiedad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ConsultaController extends Controller
{
    /**
     * Página de contacto. Si viene ?propiedad=slug el formulario queda
     * asociado a esa propiedad.
     */
    public function create(Request $request): View
    {
        $propiedad = $request->filled('propiedad')
            ? Propiedad::publicadas()->where('slug', $request->query('propiedad'))->first()
            : null;

        return view('contacto', compact('propiedad'));
    }

    /**
     * Guarda la consulta. Funciona tanto con el envío normal del formulario
     * como con fetch() desde JavaScript (en ese caso responde JSON).
     */
    public function store(ConsultaRequest $request)
    {
        // Campo trampa (honeypot): está oculto con CSS, así que una persona lo deja
        // vacío. Si viene completo es un bot: hago como que salió bien y no guardo nada.
        if ($request->filled('sitio_web')) {
            return $this->respuestaExitosa($request);
        }

        $consulta = Consulta::create([
            ...$request->validated(),
            'ip' => $request->ip(),
        ]);

        $this->avisarPorMail($consulta);

        return $this->respuestaExitosa($request);
    }

    /**
     * Manda un mail a la inmobiliaria avisando de la consulta nueva. Si el mail
     * falla (servidor SMTP caído, mal configurado, etc.) la consulta igual
     * queda guardada y se puede ver desde el panel, así que solo lo registro en el log.
     */
    private function avisarPorMail(Consulta $consulta): void
    {
        try {
            Mail::to(config('inmobiliaria.email_notificaciones'))->send(new NuevaConsulta($consulta));
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function respuestaExitosa(Request $request)
    {
        $mensaje = '¡Gracias por tu consulta! Te vamos a responder a la brevedad.';

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'mensaje' => $mensaje]);
        }

        // Desde el detalle de una propiedad vuelve a la misma página con el aviso;
        // desde la página de contacto va a la página de agradecimiento.
        if ($request->filled('propiedad_id')) {
            return back()->with('exito', $mensaje);
        }

        return redirect()->route('gracias');
    }
}
