<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')
            ->when($request->modelo, fn($q) =>
                $q->where('subject_type', 'like', "%{$request->modelo}%")
            )
            ->when($request->evento, fn($q) =>
                $q->where('event', $request->evento)
            )
            ->when($request->usuario, fn($q) =>
                $q->whereHasMorph('causer', [\App\Models\User::class], fn($q2) =>
                    $q2->where('name', 'like', "%{$request->usuario}%")
                )
            )
            ->when($request->fecha_desde, fn($q) =>
                $q->whereDate('created_at', '>=', $request->fecha_desde)
            )
            ->when($request->fecha_hasta, fn($q) =>
                $q->whereDate('created_at', '<=', $request->fecha_hasta)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $modelos = Activity::select('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->map(fn($m) => class_basename($m))
            ->unique()
            ->sort()
            ->values();

        if ($request->ajax()) {
            return response()->json([
                'html'       => view('auditoria.partials.tabla', compact('query'))->render(),
                'pagination' => view('auditoria.partials.paginacion', compact('query'))->render(),
            ]);
        }

        $logs = $query;
        return view('auditoria.index', compact('logs', 'modelos'));
    }

public function exportar(Request $request)
{
    if (!$request->desde || !$request->hasta) {
        abort(422, 'Fechas requeridas.');
    }

    $desde = \Carbon\Carbon::parse($request->desde);
    $hasta = \Carbon\Carbon::parse($request->hasta);

    if ($desde->diffInDays($hasta) > 93) {
        abort(422, 'El rango máximo es 3 meses.');
    }

    $logs = Activity::with('causer')
        ->whereDate('created_at', '>=', $request->desde)
        ->whereDate('created_at', '<=', $request->hasta)
        ->when($request->modelo, fn($q) =>
            $q->where('subject_type', 'like', "%{$request->modelo}%")
        )
        ->when($request->evento, fn($q) =>
            $q->where('event', $request->evento)
        )
        ->latest()
        ->limit(10000)
        ->get();

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet       = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Auditoría');

    $headers = ['Fecha', 'Usuario', 'Acción', 'Modelo', 'Descripción', 'Cambios'];
    foreach ($headers as $i => $header) {
        $letra = chr(65 + $i);
        $sheet->setCellValue("{$letra}1", $header);
        $sheet->getColumnDimension($letra)->setWidth(22);
    }

    $sheet->getStyle('A1:F1')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1a56db']],
    ]);

    foreach ($logs as $i => $log) {
        $fila    = $i + 2;
        $cambios = '';

        if ($log->event === 'updated' && isset($log->properties['attributes'])) {
            $camposOcultos = ['updated_at','created_at','updated_by','created_by','deleted_by'];
            $attrs = $log->properties['attributes'];
            $old   = $log->properties['old'] ?? [];

            $cambios = collect($attrs)
                ->filter(fn($v, $k) => !in_array($k, $camposOcultos) && isset($old[$k]) && $old[$k] != $v)
                ->map(fn($v, $k) => "{$k}: {$old[$k]} → {$v}")
                ->join(' | ');
        }

        $sheet->setCellValue("A{$fila}", $log->created_at->format('d/m/Y H:i'));
        $sheet->setCellValue("B{$fila}", $log->causer->name ?? 'Sistema');
        $sheet->setCellValue("C{$fila}", $log->description);
        $sheet->setCellValue("D{$fila}", class_basename($log->subject_type ?? ''));
        $sheet->setCellValue("E{$fila}", $log->description);
        $sheet->setCellValue("F{$fila}", $cambios);

        if ($fila % 2 === 0) {
            $sheet->getStyle("A{$fila}:F{$fila}")->applyFromArray([
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'f8fafc']],
            ]);
        }
    }

    $nombreArchivo = "auditoria_{$request->desde}_{$request->hasta}.xlsx";
    $writer        = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    return response()->stream(function() use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
    ]);
}
}