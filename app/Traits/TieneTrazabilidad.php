<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;

trait TieneTrazabilidad
{
    use LogsActivity;

    // Spatie registra todos los campos automáticamente
public function getActivitylogOptions(): LogOptions
{
    $nombreModelo = class_basename($this);

    return LogOptions::defaults()
        ->logAll()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
            'created' => "{$nombreModelo} creado",
            'updated' => "{$nombreModelo} actualizado",
            'deleted' => "{$nombreModelo} eliminado",
            default   => $eventName,
        });
}

    // Setea created_by y updated_by automáticamente
    public static function bootTieneTrazabilidad(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && isset($model->deleted_by)) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });
    }
}