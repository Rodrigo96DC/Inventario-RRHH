<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Provincia;
use App\Models\Comuna;

class RegionComunaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['numero' => 'I',    'nombre' => 'Tarapacá',                'capital' => 'Iquique',
             'provincias' => [
                ['nombre' => 'Iquique',      'comunas' => ['Iquique', 'Alto Hospicio']],
                ['nombre' => 'Tamarugal',    'comunas' => ['Pozo Almonte', 'Camiña', 'Colchane', 'Huara', 'Pica']],
            ]],
            ['numero' => 'II',   'nombre' => 'Antofagasta',             'capital' => 'Antofagasta',
             'provincias' => [
                ['nombre' => 'Antofagasta',  'comunas' => ['Antofagasta', 'Mejillones', 'Sierra Gorda', 'Taltal']],
                ['nombre' => 'El Loa',       'comunas' => ['Calama', 'Ollagüe', 'San Pedro de Atacama']],
                ['nombre' => 'Tocopilla',    'comunas' => ['Tocopilla', 'María Elena']],
            ]],
            ['numero' => 'III',  'nombre' => 'Atacama',                 'capital' => 'Copiapó',
             'provincias' => [
                ['nombre' => 'Copiapó',      'comunas' => ['Copiapó', 'Caldera', 'Tierra Amarilla']],
                ['nombre' => 'Chañaral',     'comunas' => ['Chañaral', 'Diego de Almagro']],
                ['nombre' => 'Huasco',       'comunas' => ['Vallenar', 'Alto del Carmen', 'Freirina', 'Huasco']],
            ]],
            ['numero' => 'IV',   'nombre' => 'Coquimbo',                'capital' => 'La Serena',
             'provincias' => [
                ['nombre' => 'Elqui',        'comunas' => ['La Serena', 'Coquimbo', 'Andacollo', 'La Higuera', 'Paiguano', 'Vicuña']],
                ['nombre' => 'Choapa',       'comunas' => ['Illapel', 'Canela', 'Los Vilos', 'Salamanca']],
                ['nombre' => 'Limarí',       'comunas' => ['Ovalle', 'Combarbalá', 'Monte Patria', 'Punitaqui', 'Río Hurtado']],
            ]],
            ['numero' => 'V',    'nombre' => 'Valparaíso',              'capital' => 'Valparaíso',
             'provincias' => [
                ['nombre' => 'Valparaíso',   'comunas' => ['Valparaíso', 'Casablanca', 'Concón', 'Juan Fernández', 'Puchuncaví', 'Quintero', 'Viña del Mar']],
                ['nombre' => 'Isla de Pascua','comunas' => ['Isla de Pascua']],
                ['nombre' => 'Los Andes',    'comunas' => ['Los Andes', 'Calle Larga', 'Rinconada', 'San Esteban']],
                ['nombre' => 'Marga Marga',  'comunas' => ['Quilpué', 'Limache', 'Olmué', 'Villa Alemana']],
                ['nombre' => 'Petorca',      'comunas' => ['La Ligua', 'Cabildo', 'Papudo', 'Petorca', 'Zapallar']],
                ['nombre' => 'Quillota',     'comunas' => ['Quillota', 'Calera', 'Hijuelas', 'La Cruz', 'Nogales']],
                ['nombre' => 'San Antonio',  'comunas' => ['San Antonio', 'Algarrobo', 'Cartagena', 'El Quisco', 'El Tabo', 'Santo Domingo']],
                ['nombre' => 'San Felipe',   'comunas' => ['San Felipe', 'Catemu', 'Llaillay', 'Panquehue', 'Putaendo', 'Santa María']],
            ]],
            ['numero' => 'VI',   'nombre' => "O'Higgins",               'capital' => 'Rancagua',
             'provincias' => [
                ['nombre' => 'Cachapoal',    'comunas' => ['Rancagua', 'Codegua', 'Coinco', 'Coltauco', 'Doñihue', 'Graneros', 'Las Cabras', 'Machalí', 'Malloa', 'Mostazal', 'Olivar', 'Peumo', 'Pichidegua', 'Quinta de Tilcoco', 'Requínoa', 'Rengo', 'Sanzana']],
                ['nombre' => 'Cardenal Caro','comunas' => ['Pichilemu', 'La Estrella', 'Litueche', 'Marchihue', 'Navidad', 'Paredones']],
                ['nombre' => 'Colchagua',    'comunas' => ['San Fernando', 'Chépica', 'Chimbarongo', 'Lolol', 'Nancagua', 'Palmilla', 'Peralillo', 'Placilla', 'Pumanque', 'Santa Cruz']],
            ]],
            ['numero' => 'VII',  'nombre' => 'Maule',                   'capital' => 'Talca',
             'provincias' => [
                ['nombre' => 'Talca',        'comunas' => ['Talca', 'Constitución', 'Curepto', 'Empedrado', 'Maule', 'Pelarco', 'Pencahue', 'Río Claro', 'San Clemente', 'San Rafael']],
                ['nombre' => 'Cauquenes',    'comunas' => ['Cauquenes', 'Chanco', 'Pelluhue']],
                ['nombre' => 'Curicó',       'comunas' => ['Curicó', 'Hualañé', 'Licantén', 'Molina', 'Rauco', 'Romeral', 'Sagrada Familia', 'Teno', 'Vichuquén']],
                ['nombre' => 'Linares',      'comunas' => ['Linares', 'Colbún', 'Longaví', 'Parral', 'Retiro', 'San Javier', 'Villa Alegre', 'Yerbas Buenas']],
            ]],
            ['numero' => 'VIII', 'nombre' => 'Biobío',                  'capital' => 'Concepción',
             'provincias' => [
                ['nombre' => 'Concepción',   'comunas' => ['Concepción', 'Coronel', 'Chiguayante', 'Florida', 'Hualqui', 'Lota', 'Penco', 'San Pedro de la Paz', 'Santa Juana', 'Talcahuano', 'Tomé', 'Hualpén']],
                ['nombre' => 'Arauco',       'comunas' => ['Lebu', 'Arauco', 'Cañete', 'Contulmo', 'Curanilahue', 'Los Álamos', 'Tirúa']],
                ['nombre' => 'Biobío',       'comunas' => ['Los Ángeles', 'Antuco', 'Cabrero', 'Laja', 'Mulchén', 'Nacimiento', 'Negrete', 'Quilaco', 'Quilleco', 'San Rosendo', 'Santa Bárbara', 'Tucapel', 'Yumbel', 'Alto Biobío']],
            ]],
            ['numero' => 'IX',   'nombre' => 'La Araucanía',            'capital' => 'Temuco',
             'provincias' => [
                ['nombre' => 'Cautín',       'comunas' => ['Temuco', 'Carahue', 'Cunco', 'Curarrehue', 'Freire', 'Galvarino', 'Gorbea', 'Lautaro', 'Loncoche', 'Melipeuco', 'Nueva Imperial', 'Padre las Casas', 'Perquenco', 'Pitrufquén', 'Pucón', 'Saavedra', 'Teodoro Schmidt', 'Toltén', 'Vilcún', 'Villarrica', 'Cholchol']],
                ['nombre' => 'Malleco',      'comunas' => ['Angol', 'Collipulli', 'Curacautín', 'Ercilla', 'Lonquimay', 'Los Sauces', 'Lumaco', 'Purén', 'Renaico', 'Traiguén', 'Victoria']],
            ]],
            ['numero' => 'X',    'nombre' => 'Los Lagos',               'capital' => 'Puerto Montt',
             'provincias' => [
                ['nombre' => 'Llanquihue',   'comunas' => ['Puerto Montt', 'Calbuco', 'Cochamó', 'Fresia', 'Frutillar', 'Los Muermos', 'Llanquihue', 'Maullín', 'Puerto Varas']],
                ['nombre' => 'Chiloé',       'comunas' => ['Castro', 'Ancud', 'Chonchi', 'Curaco de Vélez', 'Dalcahue', 'Puqueldón', 'Queilén', 'Quellón', 'Quemchi', 'Quinchao']],
                ['nombre' => 'Osorno',       'comunas' => ['Osorno', 'Puerto Octay', 'Purranque', 'Puyehue', 'Río Negro', 'San Juan de la Costa', 'San Pablo']],
                ['nombre' => 'Palena',       'comunas' => ['Chaitén', 'Futaleufú', 'Hualaihué', 'Palena']],
            ]],
            ['numero' => 'XI',   'nombre' => 'Aysén',                   'capital' => 'Coyhaique',
             'provincias' => [
                ['nombre' => 'Coyhaique',    'comunas' => ['Coyhaique', 'Lago Verde']],
                ['nombre' => 'Aysén',        'comunas' => ['Aysén', 'Cisnes', 'Guaitecas']],
                ['nombre' => 'Capitán Prat', 'comunas' => ['Cochrane', 'O\'Higgins', 'Tortel']],
                ['nombre' => 'General Carrera', 'comunas' => ['Chile Chico', 'Río Ibáñez']],
            ]],
            ['numero' => 'XII',  'nombre' => 'Magallanes',              'capital' => 'Punta Arenas',
             'provincias' => [
                ['nombre' => 'Magallanes',   'comunas' => ['Punta Arenas', 'Laguna Blanca', 'Río Verde', 'San Gregorio']],
                ['nombre' => 'Antártica Chilena', 'comunas' => ['Cabo de Hornos', 'Antártica']],
                ['nombre' => 'Tierra del Fuego', 'comunas' => ['Porvenir', 'Primavera', 'Timaukel']],
                ['nombre' => 'Última Esperanza', 'comunas' => ['Natales', 'Torres del Paine']],
            ]],
            ['numero' => 'XIII', 'nombre' => 'Metropolitana',           'capital' => 'Santiago',
             'provincias' => [
                ['nombre' => 'Santiago',     'comunas' => ['Santiago', 'Cerrillos', 'Cerro Navia', 'Conchalí', 'El Bosque', 'Estación Central', 'Huechuraba', 'Independencia', 'La Cisterna', 'La Florida', 'La Granja', 'La Pintana', 'La Reina', 'Las Condes', 'Lo Barnechea', 'Lo Espejo', 'Lo Prado', 'Macul', 'Maipú', 'Ñuñoa', 'Pedro Aguirre Cerda', 'Peñalolén', 'Providencia', 'Pudahuel', 'Quilicura', 'Quinta Normal', 'Recoleta', 'Renca', 'San Joaquín', 'San Miguel', 'San Ramón', 'Vitacura']],
                ['nombre' => 'Chacabuco',    'comunas' => ['Colina', 'Lampa', 'Tiltil']],
                ['nombre' => 'Maipo',        'comunas' => ['San Bernardo', 'Buin', 'Calera de Tango', 'Paine']],
                ['nombre' => 'Melipilla',    'comunas' => ['Melipilla', 'Alhué', 'Curacaví', 'María Pinto', 'San Pedro']],
                ['nombre' => 'Cordillera',   'comunas' => ['Puente Alto', 'Pirque', 'San José de Maipo']],
                ['nombre' => 'Talagante',    'comunas' => ['Talagante', 'El Monte', 'Isla de Maipo', 'Padre Hurtado', 'Peñaflor']],
            ]],
            ['numero' => 'XIV',  'nombre' => 'Los Ríos',                'capital' => 'Valdivia',
             'provincias' => [
                ['nombre' => 'Valdivia',     'comunas' => ['Valdivia', 'Corral', 'Futrono', 'La Unión', 'Lago Ranco', 'Lanco', 'Los Lagos', 'Máfil', 'Mariquina', 'Paillaco', 'Panguipulli', 'Río Bueno']],
                ['nombre' => 'Ranco',        'comunas' => ['La Unión', 'Futrono', 'Lago Ranco', 'Río Bueno']],
            ]],
            ['numero' => 'XV',   'nombre' => 'Arica y Parinacota',      'capital' => 'Arica',
             'provincias' => [
                ['nombre' => 'Arica',        'comunas' => ['Arica', 'Camarones']],
                ['nombre' => 'Parinacota',   'comunas' => ['Putre', 'General Lagos']],
            ]],
            ['numero' => 'XVI',  'nombre' => 'Ñuble',                   'capital' => 'Chillán',
             'provincias' => [
                ['nombre' => 'Diguillín',    'comunas' => ['Chillán', 'Bulnes', 'Chillán Viejo', 'El Carmen', 'Pemuco', 'Pinto', 'Quillón', 'San Ignacio', 'Yungay']],
                ['nombre' => 'Itata',        'comunas' => ['Quirihue', 'Cobquecura', 'Coelemu', 'Ninhue', 'Portezuelo', 'Ranquil', 'Treguaco']],
                ['nombre' => 'Punilla',      'comunas' => ['San Carlos', 'Coihueco', 'Ñiquén', 'San Fabián', 'San Nicolás']],
            ]],
        ];

        foreach ($data as $regionData) {
            $region = Region::create([
                'numero'  => $regionData['numero'],
                'nombre'  => $regionData['nombre'],
                'capital' => $regionData['capital'],
            ]);

            foreach ($regionData['provincias'] as $provinciaData) {
                $provincia = Provincia::create([
                    'id_region' => $region->id,
                    'nombre'    => $provinciaData['nombre'],
                ]);

                foreach ($provinciaData['comunas'] as $nombreComuna) {
                    Comuna::create([
                        'id_provincia' => $provincia->id,
                        'nombre'       => $nombreComuna,
                    ]);
                }
            }
        }

        $this->command->info('✅ Regiones, provincias y comunas de Chile cargadas.');
    }
}