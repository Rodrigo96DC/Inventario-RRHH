<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── PERMISOS ───────────────────────────────────────────────

        $permisos = [
            // Usuarios
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            // Funcionarios
            'funcionarios.ver',
            'funcionarios.crear',
            'funcionarios.editar',
            'funcionarios.eliminar',

            // Inventario - Equipos
            'equipos.ver',
            'equipos.crear',
            'equipos.editar',
            'equipos.eliminar',

            // Inventario - Insumos
            'insumos.ver',
            'insumos.crear',
            'insumos.editar',
            'insumos.eliminar',

            // Inventario - Proveedores
            'proveedores.ver',
            'proveedores.crear',
            'proveedores.editar',
            'proveedores.eliminar',

            // RRHH - Asistencia
            'asistencia.ver',
            'asistencia.crear',
            'asistencia.editar',
            'asistencia.eliminar',

            // RRHH - Licencias
            'licencias.ver',
            'licencias.crear',
            'licencias.editar',
            'licencias.eliminar',

            // RRHH - Vacaciones
            'vacaciones.ver',
            'vacaciones.crear',
            'vacaciones.editar',
            'vacaciones.eliminar',

            // RRHH - Remuneraciones
            'remuneraciones.ver',
            'remuneraciones.crear',
            'remuneraciones.editar',
            'remuneraciones.eliminar',

            // Reportes
            'reportes.ver',

            // Auditoría
            'auditoria.ver',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // ─── ROLES ──────────────────────────────────────────────────

        // Admin: acceso total
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // RRHH: gestiona funcionarios, asistencia, licencias, vacaciones, remuneraciones
        $rrhh = Role::firstOrCreate(['name' => 'RRHH', 'guard_name' => 'web']);
        $rrhh->syncPermissions([
            'funcionarios.ver', 'funcionarios.crear', 'funcionarios.editar',
            'asistencia.ver', 'asistencia.crear', 'asistencia.editar',
            'licencias.ver', 'licencias.crear', 'licencias.editar',
            'vacaciones.ver', 'vacaciones.crear', 'vacaciones.editar',
            'remuneraciones.ver', 'remuneraciones.crear', 'remuneraciones.editar',
            'reportes.ver',
        ]);

        // Supervisor: ve todo pero no edita remuneraciones
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $supervisor->syncPermissions([
            'funcionarios.ver',
            'asistencia.ver', 'asistencia.crear', 'asistencia.editar',
            'licencias.ver',
            'vacaciones.ver',
            'reportes.ver',
        ]);

        // Inventario: gestiona solo inventario
        $inventario = Role::firstOrCreate(['name' => 'Inventario', 'guard_name' => 'web']);
        $inventario->syncPermissions([
            'equipos.ver', 'equipos.crear', 'equipos.editar', 'equipos.eliminar',
            'insumos.ver', 'insumos.crear', 'insumos.editar', 'insumos.eliminar',
            'proveedores.ver', 'proveedores.crear', 'proveedores.editar',
            'reportes.ver',
        ]);

        // Funcionario: solo ve sus propios datos
        $funcionario = Role::firstOrCreate(['name' => 'Funcionario', 'guard_name' => 'web']);
        $funcionario->syncPermissions([
            'asistencia.ver',
            'licencias.ver', 'licencias.crear',
            'vacaciones.ver', 'vacaciones.crear',
        ]);

        // ─── USUARIO ADMIN ───────────────────────────────────────────

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@sistema.cl'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('Admin1234!'),
                'estado'   => 'Activo',
            ]
        );

        $adminUser->assignRole('Admin');

        $this->command->info('✅ Roles, permisos y usuario admin creados correctamente.');
        $this->command->info('   Email: admin@sistema.cl');
        $this->command->info('   Password: Admin1234!');
    }
}
