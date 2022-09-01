<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('industry')->insert(['name' => 'Aerospace']);
        DB::table('industry')->insert(['name' => 'Agricultural']);
        DB::table('industry')->insert(['name' => 'Alimentation and drinks']);
        DB::table('industry')->insert(['name' => 'Architecture']);
        DB::table('industry')->insert(['name' => 'Automotive']);
        DB::table('industry')->insert(['name' => 'Banking']);
        DB::table('industry')->insert(['name' => 'Biotechnology']);
        DB::table('industry')->insert(['name' => 'Computer']);
        DB::table('industry')->insert(['name' => 'Construction']);
        DB::table('industry')->insert(['name' => 'Consulting']);
        DB::table('industry')->insert(['name' => 'Creative']);
        DB::table('industry')->insert(['name' => 'Culture']);

        DB::table('industry')->insert(['name' => 'Education']);
        DB::table('industry')->insert(['name' => 'Electronics']);
        DB::table('industry')->insert(['name' => 'Energy']);
        DB::table('industry')->insert(['name' => 'Entertainment & Leisure']);

        DB::table('industry')->insert(['name' => 'Healthcare']);

        DB::table('industry')->insert(['name' => 'Real Estate']);
        DB::table('industry')->insert(['name' => 'Venture Capital']);
        DB::table('industry')->insert(['name' => 'Water']);

        /*
        <option value="Sports">Deportes</option>

        <option value="Manufacturing">Fabricación</option>
        <option value="Finance">Finanzas</option>
        <option value="Hospitality">Hostelería</option>
        <option value="Legal">Jurídica</option>
        <option value="Marketing">Marketing</option>
        <option value="Building Materials &amp; Equipment">Materiales de construcción y equipamiento</option>
        <option value="Mass Media">Medios masivos</option>
        <option value="Mining">Minería</option>
        <option value="Music">Música</option>
        <option value="Shipping">Naviera</option><option value="Petroleum">Petróleo</option><option value="Testing, Inspection &amp; Certification">Pruebas, inspección y certificación</option><option value="Publishing">Publicaciones</option><option value="Advertising">Publicidad</option><option value="Chemical">Química</option><option value="Apparel &amp; Accessories">Ropa y accesorios</option><option value="Insurance">Seguros</option><option value="Service">Servicio</option><option value="Software">Software</option><option value="Support">Soporte</option><option value="Technology">Tecnología</option><option value="Telecommunications">Telecomunicaciones</option><option value="Television">Televisión</option><option value="Transportation">Transporte</option><option value="Wholesale">Venta al por mayor</option><option value="Retail">Venta al por menor</option><option value="Travel">Viajes</option>

         */
    }
}
