<?php

use Devlab\LaravelCore\Helpers\DevlabCrypt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ZipProvincesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['code' => '02', 'iso' => 'AB', 'country_iso' => 'ES', 'city' => 'Albacete'],
            ['code' => '03', 'iso' => 'A', 'country_iso' => 'ES', 'city' => 'Alicante/Alacant'],
            ['code' => '04', 'iso' => 'AL', 'country_iso' => 'ES', 'city' => 'Almería'],
            ['code' => '01', 'iso' => 'VI', 'country_iso' => 'ES', 'city' => 'Araba/Álava'],
            ['code' => '33', 'iso' => 'O', 'country_iso' => 'ES', 'city' => 'Asturias'],
            ['code' => '05', 'iso' => 'AV', 'country_iso' => 'ES', 'city' => 'Ávila'],
            ['code' => '06', 'iso' => 'BA', 'country_iso' => 'ES', 'city' => 'Badajoz'],
            ['code' => '07', 'iso' => 'PM', 'country_iso' => 'ES', 'city' => 'Balears, Illes'],
            ['code' => '08', 'iso' => 'B', 'country_iso' => 'ES', 'city' => 'Barcelona'],
            ['code' => '48', 'iso' => 'BI', 'country_iso' => 'ES', 'city' => 'Bizkaia'],
            ['code' => '09', 'iso' => 'BU', 'country_iso' => 'ES', 'city' => 'Burgos'],
            ['code' => '10', 'iso' => 'CC', 'country_iso' => 'ES', 'city' => 'Cáceres'],
            ['code' => '11', 'iso' => 'CA', 'country_iso' => 'ES', 'city' => 'Cádiz'],
            ['code' => '39', 'iso' => 'S', 'country_iso' => 'ES', 'city' => 'Cantabria'],
            ['code' => '12', 'iso' => 'CS', 'country_iso' => 'ES', 'city' => 'Castellón/Castelló'],
            ['code' => '13', 'iso' => 'CR', 'country_iso' => 'ES', 'city' => 'Ciudad Real'],
            ['code' => '14', 'iso' => 'CO', 'country_iso' => 'ES', 'city' => 'Córdoba'],
            ['code' => '15', 'iso' => 'C', 'country_iso' => 'ES', 'city' => 'Coruña, A'],
            ['code' => '16', 'iso' => 'CU', 'country_iso' => 'ES', 'city' => 'Cuenca'],
            ['code' => '20', 'iso' => 'SS', 'country_iso' => 'ES', 'city' => 'Gipuzkoa'],
            ['code' => '17', 'iso' => 'GI', 'country_iso' => 'ES', 'city' => 'Girona'],
            ['code' => '18', 'iso' => 'GR', 'country_iso' => 'ES', 'city' => 'Granada'],
            ['code' => '19', 'iso' => 'GU', 'country_iso' => 'ES', 'city' => 'Guadalajara'],
            ['code' => '21', 'iso' => 'H', 'country_iso' => 'ES', 'city' => 'Huelva'],
            ['code' => '22', 'iso' => 'HU', 'country_iso' => 'ES', 'city' => 'Huesca'],
            ['code' => '23', 'iso' => 'J', 'country_iso' => 'ES', 'city' => 'Jaén'],
            ['code' => '24', 'iso' => 'LE', 'country_iso' => 'ES', 'city' => 'León'],
            ['code' => '25', 'iso' => 'L', 'country_iso' => 'ES', 'city' => 'Lleida'],
            ['code' => '27', 'iso' => 'LU', 'country_iso' => 'ES', 'city' => 'Lugo'],
            ['code' => '28', 'iso' => 'M', 'country_iso' => 'ES', 'city' => 'Madrid'],
            ['code' => '29', 'iso' => 'MA', 'country_iso' => 'ES', 'city' => 'Málaga'],
            ['code' => '30', 'iso' => 'MU', 'country_iso' => 'ES', 'city' => 'Murcia'],
            ['code' => '31', 'iso' => 'NA', 'country_iso' => 'ES', 'city' => 'Navarra'],
            ['code' => '32', 'iso' => 'OR', 'country_iso' => 'ES', 'city' => 'Ourense'],
            ['code' => '34', 'iso' => 'P', 'country_iso' => 'ES', 'city' => 'Palencia'],
            ['code' => '35', 'iso' => 'GC', 'country_iso' => 'ES', 'city' => 'Palmas, Las'],
            ['code' => '36', 'iso' => 'PO', 'country_iso' => 'ES', 'city' => 'Pontevedra'],
            ['code' => '26', 'iso' => 'LO', 'country_iso' => 'ES', 'city' => 'Rioja, La'],
            ['code' => '37', 'iso' => 'SA', 'country_iso' => 'ES', 'city' => 'Salamanca'],
            ['code' => '38', 'iso' => 'TF', 'country_iso' => 'ES', 'city' => 'Santa Cruz de Tenerife'],
            ['code' => '40', 'iso' => 'SG', 'country_iso' => 'ES', 'city' => 'Segovia'],
            ['code' => '41', 'iso' => 'SE', 'country_iso' => 'ES', 'city' => 'Sevilla'],
            ['code' => '42', 'iso' => 'SO', 'country_iso' => 'ES', 'city' => 'Soria'],
            ['code' => '43', 'iso' => 'T', 'country_iso' => 'ES', 'city' => 'Tarragona'],
            ['code' => '44', 'iso' => 'TE', 'country_iso' => 'ES', 'city' => 'Teruel'],
            ['code' => '45', 'iso' => 'TO', 'country_iso' => 'ES', 'city' => 'Toledo'],
            ['code' => '46', 'iso' => 'V', 'country_iso' => 'ES', 'city' => 'Valencia/València'],
            ['code' => '47', 'iso' => 'VA', 'country_iso' => 'ES', 'city' => 'Valladolid'],
            ['code' => '49', 'iso' => 'ZA', 'country_iso' => 'ES', 'city' => 'Zamora'],
            ['code' => '50', 'iso' => 'Z', 'country_iso' => 'ES', 'city' => 'Zaragoza'],
            ['code' => '51', 'iso' => 'CE', 'country_iso' => 'ES', 'city' => 'Ceuta'],
            ['code' => '52', 'iso' => 'ML', 'country_iso' => 'ES', 'city' => 'Melilla'],
        ];

        foreach ($data as $entry) {
            DB::table('code_iso')->insert([
                'code' => $entry['code'],
                'iso' => $entry['iso'],
                'country_iso' => $entry['country_iso'],
                'city' => $entry['city'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
