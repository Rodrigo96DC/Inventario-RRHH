<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Funcionario;
use App\Models\Notificacion;
use Carbon\Carbon;

class NotificarContratosVencimiento extends Command
{
    protected $signature   = 'contratos:notificar';
    protected $description = 'Notifica contratos próximos a vencer';

    public function handle(): void
    {
        $hoy   = Carbon::today();
        $count = 0;

        $funcionarios = Funcionario::whereNotNull('fecha_fin_contrato')
            ->where('estado', 'Activo')
            ->get()
            ->filter(function ($f) use ($hoy) {
                $diasRestantes = $hoy->diffInDays($f->fecha_fin_contrato, false);
                return $diasRestantes >= 0 && $diasRestantes <= $f->dias_aviso_contrato;
            });

        foreach ($funcionarios as $f) {
            $diasRestantes = $hoy->diffInDays($f->fecha_fin_contrato, false);

            // Verificar si ya existe una notificación NO leída para hoy
            $yaExiste = Notificacion::where('tipo',               'contrato_vencimiento')
                ->where('id_ref',             $f->id)
                ->where('fecha_notificacion', $hoy->toDateString())
                ->where('leido',              false)
                ->exists();

            if ($yaExiste) continue;

            // Crear nueva notificación
            Notificacion::create([
                'tipo'               => 'contrato_vencimiento',
                'id_ref'             => $f->id,
                'fecha_ref'          => $f->fecha_fin_contrato,
                'fecha_notificacion' => $hoy->toDateString(),
                'titulo'             => 'Contrato próximo a vencer',
                'mensaje'            => "El contrato de {$f->nombre_completo} vence en {$diasRestantes} día(s) ({$f->fecha_fin_contrato->format('d/m/Y')})",
                'leido'              => false,
                'created_by'         => 1,
            ]);

            $count++;
        }

        $this->info("✅ {$count} notificaciones generadas.");
    }
}