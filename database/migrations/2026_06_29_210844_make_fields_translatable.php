<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Uptown Types Table
        Schema::table('uptown_types', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('name_en');
        });

        // Copy existing data
        DB::table('uptown_types')->update([
            'name_en' => DB::raw('name'),
            'name_ar' => DB::raw('name'),
        ]);

        Schema::table('uptown_types', function (Blueprint $table) {
            $table->string('name_en')->nullable(false)->change();
            $table->string('name_ar')->nullable(false)->change();
            $table->dropColumn('name');
        });

        // 2. Developers Table
        Schema::table('developers', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('name_en');
            $table->longText('description_en')->nullable()->after('description');
            $table->longText('description_ar')->nullable()->after('description_en');
        });

        // Copy existing data
        DB::table('developers')->update([
            'name_en' => DB::raw('name'),
            'name_ar' => DB::raw('name'),
            'description_en' => DB::raw('description'),
            'description_ar' => DB::raw('description'),
        ]);

        Schema::table('developers', function (Blueprint $table) {
            $table->string('name_en')->nullable(false)->change();
            $table->string('name_ar')->nullable(false)->change();
            $table->dropColumn('name');
            $table->dropColumn('description');
        });

        // 3. Uptowns Table
        Schema::table('uptowns', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->string('name_ar')->nullable()->after('name_en');
            $table->string('description_en')->nullable()->after('description');
            $table->string('description_ar')->nullable()->after('description_en');
        });

        // Copy existing data
        DB::table('uptowns')->update([
            'name_en' => DB::raw('name'),
            'name_ar' => DB::raw('name'),
            'description_en' => DB::raw('description'),
            'description_ar' => DB::raw('description'),
        ]);

        Schema::table('uptowns', function (Blueprint $table) {
            $table->string('name_en')->nullable(false)->change();
            $table->string('name_ar')->nullable(false)->change();
            $table->dropColumn('name');
            $table->dropColumn('description');
        });

        // 4. Ads Table
        Schema::table('ads', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->string('title_ar')->nullable()->after('title_en');
        });

        // Copy existing data
        DB::table('ads')->update([
            'title_en' => DB::raw('title'),
            'title_ar' => DB::raw('title'),
        ]);

        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Uptown Types Table
        Schema::table('uptown_types', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });
        DB::table('uptown_types')->update([
            'name' => DB::raw('name_en'),
        ]);
        Schema::table('uptown_types', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->dropColumn(['name_en', 'name_ar']);
        });

        // 2. Developers Table
        Schema::table('developers', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->longText('description')->nullable()->after('email');
        });
        DB::table('developers')->update([
            'name' => DB::raw('name_en'),
            'description' => DB::raw('description_en'),
        ]);
        Schema::table('developers', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->dropColumn(['name_en', 'name_ar', 'description_en', 'description_ar']);
        });

        // 3. Uptowns Table
        Schema::table('uptowns', function (Blueprint $table) {
            $table->string('name')->nullable()->after('uptown_type_id');
            $table->string('description')->nullable()->after('commission_price');
        });
        DB::table('uptowns')->update([
            'name' => DB::raw('name_en'),
            'description' => DB::raw('description_en'),
        ]);
        Schema::table('uptowns', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->dropColumn(['name_en', 'name_ar', 'description_en', 'description_ar']);
        });

        // 4. Ads Table
        Schema::table('ads', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
        });
        DB::table('ads')->update([
            'title' => DB::raw('title_en'),
        ]);
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'title_ar']);
        });
    }
};
