<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Workaround for Laravel 12 SQLite bug with pragma_table_xinfo
        if (DB::getDriverName() === 'sqlite') {
            // Check if columns don't exist before adding them
            $columns = DB::select("PRAGMA table_info(users)");
            $columnNames = array_column($columns, 'name');
            
            if (!in_array('company_id', $columnNames)) {
                // SQLite doesn't support adding foreign key constraints to existing tables via ALTER TABLE
                // The foreign key relationship will be maintained at the application level
                DB::statement('ALTER TABLE users ADD COLUMN company_id INTEGER NULL');
                DB::statement('CREATE INDEX IF NOT EXISTS users_company_id_index ON users(company_id)');
            }
            
            if (!in_array('role', $columnNames)) {
                DB::statement("ALTER TABLE users ADD COLUMN role VARCHAR(255) NOT NULL DEFAULT 'user'");
            }
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('role')->default('user');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $columns = DB::select("PRAGMA table_info(users)");
            $columnNames = array_column($columns, 'name');
            
            if (in_array('company_id', $columnNames)) {
                DB::statement('DROP INDEX IF EXISTS users_company_id_index');
                // SQLite doesn't support DROP COLUMN directly, so we'll need to recreate the table
                // For now, we'll just skip this in SQLite
            }
            
            if (in_array('role', $columnNames)) {
                // SQLite doesn't support DROP COLUMN directly
                // This would require recreating the table, which is complex
            }
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn(['company_id', 'role']);
            });
        }
    }
};
