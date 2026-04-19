<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: skip rename if columns already de (re-run safe)
            if (Schema::hasColumn('categories', 'name_ar')) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->renameColumn('name_ar', 'name_de');
                });
            }
            if (Schema::hasColumn('products', 'name_ar')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->renameColumn('name_ar', 'name_de');
                });
            }
            if (Schema::hasColumn('products', 'description_ar')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->renameColumn('description_ar', 'description_de');
                });
            }
            if (Schema::hasColumn('product_sizes', 'name_ar')) {
                Schema::table('product_sizes', function (Blueprint $table) {
                    $table->renameColumn('name_ar', 'name_de');
                });
            }
            if (Schema::hasColumn('product_toppings', 'name_ar')) {
                Schema::table('product_toppings', function (Blueprint $table) {
                    $table->renameColumn('name_ar', 'name_de');
                });
            }
            if (Schema::hasColumn('offers', 'name_ar')) {
                Schema::table('offers', function (Blueprint $table) {
                    $table->renameColumn('name_ar', 'name_de');
                });
            }
            if (Schema::hasColumn('offers', 'description_ar')) {
                Schema::table('offers', function (Blueprint $table) {
                    $table->renameColumn('description_ar', 'description_de');
                });
            }

            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name_ar', 'name_de');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('name_ar', 'name_de');
            $table->renameColumn('description_ar', 'description_de');
        });
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->renameColumn('name_ar', 'name_de');
        });
        Schema::table('product_toppings', function (Blueprint $table) {
            $table->renameColumn('name_ar', 'name_de');
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->renameColumn('name_ar', 'name_de');
            $table->renameColumn('description_ar', 'description_de');
        });
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            if (Schema::hasColumn('categories', 'name_de')) {
                Schema::table('categories', function (Blueprint $table) {
                    $table->renameColumn('name_de', 'name_ar');
                });
            }
            if (Schema::hasColumn('products', 'name_de')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->renameColumn('name_de', 'name_ar');
                });
            }
            if (Schema::hasColumn('products', 'description_de')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->renameColumn('description_de', 'description_ar');
                });
            }
            if (Schema::hasColumn('product_sizes', 'name_de')) {
                Schema::table('product_sizes', function (Blueprint $table) {
                    $table->renameColumn('name_de', 'name_ar');
                });
            }
            if (Schema::hasColumn('product_toppings', 'name_de')) {
                Schema::table('product_toppings', function (Blueprint $table) {
                    $table->renameColumn('name_de', 'name_ar');
                });
            }
            if (Schema::hasColumn('offers', 'name_de')) {
                Schema::table('offers', function (Blueprint $table) {
                    $table->renameColumn('name_de', 'name_ar');
                });
            }
            if (Schema::hasColumn('offers', 'description_de')) {
                Schema::table('offers', function (Blueprint $table) {
                    $table->renameColumn('description_de', 'description_ar');
                });
            }

            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name_de', 'name_ar');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('name_de', 'name_ar');
            $table->renameColumn('description_de', 'description_ar');
        });
        Schema::table('product_sizes', function (Blueprint $table) {
            $table->renameColumn('name_de', 'name_ar');
        });
        Schema::table('product_toppings', function (Blueprint $table) {
            $table->renameColumn('name_de', 'name_ar');
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->renameColumn('name_de', 'name_ar');
            $table->renameColumn('description_de', 'description_ar');
        });
    }
};
