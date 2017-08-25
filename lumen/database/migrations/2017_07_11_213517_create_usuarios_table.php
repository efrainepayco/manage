<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsuariosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('usuarios', function(Blueprint $table)
		{
			$table->char('Id', 13)->default(0)->primary();
			$table->char('nombre', 50)->nullable();
			$table->char('apellido', 50)->nullable();
			$table->char('tipo', 50)->nullable();
			$table->char('login', 50)->nullable();
			$table->char('password', 50)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('usuarios');
	}

}
