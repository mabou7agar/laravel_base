<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->uuidMorphs('translatable');
            $table->string('field')->index();
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['locale','field']);
        });
        DB::statement('CREATE INDEX content_idx ON translations (content(255));');
    }
};
