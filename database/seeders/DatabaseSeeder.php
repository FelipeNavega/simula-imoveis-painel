<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\PartnerTypeEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executa arquivo SQL de municípios e estados
        $municipios_sql_path = database_path('municipios_estados_BR.sql');
        DB::unprepared(file_get_contents($municipios_sql_path));

        // Executa arquivo SQL para bancos
        $banks_sql_path = database_path('banks.sql');
        DB::unprepared(file_get_contents($banks_sql_path));

        // Executa arquivo SQL para formulários
        $forms_sql_path = database_path('forms.sql');
        DB::unprepared(file_get_contents($forms_sql_path));

       User::create([
           'name' => 'Admin',
           'type' => UserTypeEnum::ADMIN,
           'email' => 'admin@email.com.br',
           'password' => 'VddE!-Dn@tJIaxl3Pse8'
       ]);

        User::create([
            'name' => 'Parceiro',
            'partner_type' => PartnerTypeEnum::OUTROS,
            'type' => UserTypeEnum::PARTNER,
            'email' => 'partner@email.com.br',
            'password' => 'VddE!-Dn@tJIaxl3Pse8'
        ]);
    }
}
