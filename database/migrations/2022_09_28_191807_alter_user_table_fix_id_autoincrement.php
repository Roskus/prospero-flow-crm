<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'pgsql') {
                // Aquí colocas el código específico para PostgreSQL
                $sequenceExists = DB::select("SELECT to_regclass('public.user_id_seq')");

                if (empty($sequenceExists)) {
                    DB::statement('CREATE SEQUENCE user_id_seq');
                    DB::statement("ALTER TABLE user ALTER COLUMN id SET DEFAULT nextval('user_id_seq')");
                }
            } elseif ($driver === 'mysql') {
                Schema::disableForeignKeyConstraints();
                Schema::table('calendar', function (BluePrint $table) {
                    $table->dropForeign('calendar_user_id_foreign');
                });
                try {
                    $table->unsignedBigInteger('id')->autoIncrement()->change();
                } catch (Throwable $th) {
                    Log::error($th->getMessage());
                }
                Schema::enableForeignKeyConstraints();
            } elseif ($driver === 'sqlite') {
                // SQLite does not support altering autoincrement, so skip this step
                // Optionally, log or comment here for future reference
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
