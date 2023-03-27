<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=BankSeeder
     */
    public function run(): void
    {
        DB::table('bank')->truncate();

        //DB::table('bank')->insert(['country' => 'mx', 'name' => '', 'phone' => '', 'email' => '', 'website' => '', 'bic' => '']);

        //Albania
        DB::table('bank')->insert(['country' => 'al', 'name' => 'American Bank of Investments (ABI)', 'bic' => 'EMPOALTR', 'website' => 'https://abi.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'BKT', 'website' => 'https://bkt.com.al', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Credins Bank (CB)', 'website' => 'https://bankacredins.com', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Fibank', 'website' => 'https://fibank.al', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Intesa Sanpaolo Bank (Albania)', 'website' => 'https://intesasanpaolobank.al', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'ProCredit Bank', 'website' => 'https://procreditbank.com.al', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Raiffeisen (Albania)', 'website' => 'https://raiffeisen.al', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'OTP Bank', 'website' => 'https://otpbank.al', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Tirana Bank', 'website' => 'https://tiranabank.al', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Union Bank (Albania)', 'website' => 'https://unionbank.al', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'United Bank of Albania', 'website' => 'https://uba.com.al', 'bic' => '']);

        //Andorra
        DB::table('bank')->insert(['country' => 'ad', 'name' => 'Andbank', 'phone' => '+376739011', 'email' => 'info@andbank.com', 'website' => 'https://www.andbank.com', 'bic' => 'BACAADAD']);
        DB::table('bank')->insert(['country' => 'ad', 'name' => 'Crèdit Andorrà, SA', 'phone' => '+376888888', 'email' => 'info@creditandorra.ad', 'website' => 'https://comercial.creditandorragroup.ad/en', 'bic' => 'CRDAADAD']);
        DB::table('bank')->insert(['country' => 'ad', 'name' => 'Mora Banc Grup, SA', 'phone' => '+376884884', 'email' => 'morabanc@morabanc.ad', 'website' => 'https://www.morabanc.ad', 'bic' => 'BINAADAD']);

        //Mexico
        DB::table('bank')->insert(['country' => 'mx', 'name' => 'ABC Capital', 'phone' => '+528002888222', 'email' => 'atencionaclientes@abccapital.com.mx', 'website' => 'https://www.abccapital.com.mx', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'mx', 'name' => 'Banca Mifel', 'phone' => '+528002264335', 'email' => 'banca.mifel@mifel.com.mx', 'website' => 'https://www.mifel.com.mx', 'bic' => '']);

        //Spain
        DB::table('bank')->insert(['country' => 'es', 'name' => 'Banco BBVA', 'phone' => '', 'email' => 'infobbvaresponde@bbva.com', 'website' => 'https://www.bbva.es', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'es', 'name' => 'Banco Sabadell', 'phone' => '+34963085000', 'email' => '', 'website' => 'https://www.bancsabadell.com', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'es', 'name' => 'Banco Santander', 'phone' => '+34915123123', 'email' => '', 'website' => 'https://www.bancosantander.es', 'bic' => '']);
        DB::table('bank')->insert(['country' => 'es', 'name' => 'CaixaBank', 'phone' => '+34938872525', 'email' => '', 'website' => 'https://www.caixabank.es', 'bic' => '']);
    }
}
