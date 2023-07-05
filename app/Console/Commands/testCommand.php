<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class testCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test console';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('Podaj imię: ');
        $email = $this->ask('Podaj email: ');
        $age = $this->ask('Podaj wiek: ');
    
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'age' => $age

        ], 

        [
            'name' => 'required|min:3|max:20',
            'email' => 'required|email',
            'age' => 'required|integer'
        ]);

        if ($validator->fails())
        {
            dd($validator->errors()->all());
        }

        DB::table('testTable')->insert([
            'name' => $name,
            'email' => $email,
            'age' => $age
        ]);

        

    }
}
