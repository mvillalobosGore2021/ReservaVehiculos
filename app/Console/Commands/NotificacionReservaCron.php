<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\CorreoAnulacion;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Exception;

class NotificacionReservaCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificacionreserva:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de correo de notificación de reserva';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       

        Log::info("Enviando correo ". Carbon::now()->format('d/m/Y H:i'));
        try {
            Log::info("Enviando correo Try ". Carbon::now()->format('d/m/Y H:i'));
           
            // $data = array('name'=>"Virat Gandhi");
   
            // Mail::send(['text'=>'mail'], $data, function($message) {
            //    $message->to('villalobosmario@gmail.com', 'Tutorials Point')->subject
            //       ('Laravel Basic Testing Mail');
            //    $message->from('villalobosmario@gmail.com','Virat Gandhi');
            // });


            Mail::send("correonotifreserva", ['funcionario'=>'Oscar Cruces', 'fecha' => Carbon::now()->format('d/m/Y H:i')], function ($message) {
                $message->to('villalobosmario@gmail.com', 'Notificación Reserva')->subject
                  ('Probando Cron Job Notificación Reserva');
               $message->from('villalobosmario@gmail.com','Notificación Reserva');
            });
        } catch (exception $e) {
            Log::info("Enviando correo Catch ". Carbon::now()->format('d/m/Y H:i'));
            Log::info($e->getMessage());
        }
    }
}
