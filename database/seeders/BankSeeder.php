<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=BankSeeder
     *
     * @return void
     */
    public function run()
    {
        //Albania
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Alpha Bank (Albania)', 'website' => 'https://alphabank.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'American Bank of Investments (ABI)', 'website' => 'https://abi.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'BKT', 'website' => 'https://bkt.com.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Credins Bank (CB)', 'website' => 'https://bankacredins.com']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Fibank', 'website' => 'https://fibank.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Intesa Sanpaolo Bank (Albania)', 'website' => 'https://intesasanpaolobank.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'ProCredit Bank', 'website' => 'https://procreditbank.com.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Raiffeisen (Albania)', 'website' => 'https://raiffeisen.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'OTP Bank', 'website' => 'https://otpbank.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Tirana Bank', 'website' => 'https://tiranabank.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'Union Bank (Albania)', 'website' => 'https://unionbank.al']);
        DB::table('bank')->insert(['country' => 'al', 'name' => 'United Bank of Albania', 'website' => 'https://uba.com.al']);

        //Andorra
        DB::table('bank')->insert(['country' => 'ad', 'name' => 'Andbank', 'bic' => 'BACAADAD', 'phone' => '+376739011', 'email' => 'info@andbank.com', 'website' => 'https://www.andbank.com']);
        DB::table('bank')->insert(['country' => 'ad', 'name' => 'Crèdit Andorrà, SA', 'bic' => 'CRDAADAD', 'phone' => '+376888888', 'email' => 'info@creditandorra.ad', 'website' => 'https://comercial.creditandorragroup.ad/en']);
        DB::table('bank')->insert(['country' => 'ad', 'name' => 'Mora Banc Grup, SA', 'bic' => 'BINAADAD', 'phone' => '+376884884', 'email' => 'morabanc@morabanc.ad', 'website' => 'https://www.morabanc.ad']);
    }
}
