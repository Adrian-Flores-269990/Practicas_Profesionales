<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void   { Schema::disableForeignKeyConstraints();
                                    Schema::dropIfExists('encargados');
                                    Schema::enableForeignKeyConstraints(); }
    public function down(): void { /* vacío a propósito */ }
};
