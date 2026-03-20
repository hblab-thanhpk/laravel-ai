<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->uuid('parent_id')->nullable()->after('id');
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            $table->unsignedInteger('_lft')->default(0)->after('sort_order');
            $table->unsignedInteger('_rgt')->default(1)->after('_lft');
            $table->unsignedTinyInteger('depth')->default(0)->after('_rgt');

            $table->foreign('parent_id')->references('id')->on('categories')->nullOnDelete();
            $table->index(['_lft', '_rgt']);
            $table->index('parent_id');
        });

        // Initialize existing root categories with proper NSM values
        $categories = DB::table('categories')->orderBy('created_at')->get();
        $counter = 0;
        foreach ($categories as $i => $category) {
            $lft = ++$counter;
            $rgt = ++$counter;
            DB::table('categories')->where('id', $category->id)->update([
                '_lft'       => $lft,
                '_rgt'       => $rgt,
                'depth'      => 0,
                'sort_order' => $i + 1,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['_lft', '_rgt']);
            $table->dropIndex(['parent_id']);
            $table->dropColumn(['parent_id', 'sort_order', '_lft', '_rgt', 'depth']);
        });
    }
};
