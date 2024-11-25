<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('return_detail_temps', function (Blueprint $table) {
            $table->id();
						$table->text('NumAtCard');
						$table->string('ItemCode', 25);
						$table->string('Dscription', 150);
						$table->string('UnitMsr', 25);
						$table->string('TaxCode', 25);
						$table->string('WhsCode', 25);
						$table->string('OcrCode', 25);
						$table->string('OcrCode2', 25);
						$table->string('OcrCode3', 25);
						$table->decimal('Quantity',30,0);
						$table->decimal('Price',30,2);
						$table->decimal('U_DISC1',10,2);
						$table->decimal('U_DISCVALUE1',30,2);
						$table->decimal('U_DISC2',10,2);
						$table->decimal('U_DISCVALUE2',30,2);
						$table->decimal('U_DISC3',10,2);
						$table->decimal('U_DISCVALUE3',30,2);
						$table->decimal('U_DISC4',10,2);
						$table->decimal('U_DISCVALUE4',30,2);
						$table->decimal('U_DISC5',10,2);
						$table->decimal('U_DISCVALUE5',30,2);
						$table->decimal('U_DISC6',10,2);
						$table->decimal('U_DISCVALUE6',30,2);
						$table->decimal('U_DISC7',10,2);
						$table->decimal('U_DISCVALUE7',30,2);
						$table->decimal('U_DISC8',10,2);
						$table->decimal('U_DISCVALUE8',30,2);
						$table->decimal('DiscountPercent',30,2);
						$table->decimal('LineTotal',30,2);
						$table->integer('users_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_detail_temps');
    }
};
