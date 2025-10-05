<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void   { Schema::dropIfExists('encargados'); }
    public function down(): void { /* vacío a propósito */ }
};
