<?php

use App\Models\User;
use App\Services\Users;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCreateUserAttributes17April22 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * @var Users
         */
        $userService    =   app()->make( Users::class );
        
        User::get()->each( fn( $user ) => $userService->createAttribute( $user ) );
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
}
