<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Exception;
use App\Mail\CorreoNotificacion;

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


        Log::info("Enviando correo " . Carbon::now()->format('d/m/Y H:i'));
        try {
            Log::info("Enviando correo Try " . Carbon::now()->format('d/m/Y H:i'));


            // $data = array('name'=>"Virat Gandhi");

            // Mail::send(['text'=>'mail'], $data, function($message) {
            //    $message->to('villalobosmario@gmail.com', 'Tutorials Point')->subject
            //       ('Laravel Basic Testing Mail');
            //    $message->from('villalobosmario@gmail.com','Virat Gandhi');
            // });


            $mailData = [
                'asunto' => "Notificación: Anulación de Reserva de Vehículo",
                'resumen' => "Probando El Cron Job",
                'funcionario' => "Mario Villalobos",
                'sexo' => "M",
                'fechaCreacion' =>  "19-10-2022",
                'fechaReserva' => "20-10-2022",
                'horaInicio' => "15:07",
                'horaFin' => "16:10",
                'descripcionEstado' => "Confirmada",
                'codEstado' => 2,
                // 'usaVehiculoPersonal' => $objInput->flgUsoVehiculoPersonal == 0?'No':'Si',
                'motivo' => "Motivo Cron Job",
            ];

            try {
                Log::info("Enviando correo de notificacion");
                Mail::to("villalobosmario@gmail.com")->send(new CorreoNotificacion($mailData));
            } catch (exception $e) {
                Log::info($e->getMessage());
            }


            try {
            //Inicio Envio de correo con archivo adjunto
            $data["email"] = "villalobosmario@gmail.com";
            $data["title"] = "Sistema de Reservas";
            $data["body"] = "Prueba de envio de correo con archivos adjuntos";

            $files = [
                public_path('archivospdf/IntegracionApiFirmav2.pdf'),
                public_path('archivospdf/EspecificacionesStorage.pdf'),
            ];

            Mail::send('emails.myTestMail', $data, function ($message) use ($data, $files) {
                $message->to($data["email"], $data["email"])
                    ->subject($data["title"]);

                foreach ($files as $file) {
                    $message->attach($file);
                }
            });
        } catch (exception $e) {
            Log::info("Envio de correo con archivo adjunto: ".$e->getMessage());
        }
            //Fin Envio de correo con archivo adjunto

            //Otra forma de enviar correo
            // Mail::send("correonotifreserva", ['funcionario'=>"Oscar Cruces", 'fecha' => Carbon::now()->format('d/m/Y H:i')], 
            // function ($message) {
            //     $message->to('villalobosmario@gmail.com', "Notificación Reserva AAA")->subject
            //       ("Probando Cron Job Notificación Reserva>");
            //    $message->from('villalobosmario@gmail.com',"Notificación Reserva JJJ"); 
            // });
        } catch (exception $e) {
            Log::info("Enviando correo Catch " . Carbon::now()->format('d/m/Y H:i'));
            Log::info($e->getMessage());
        }
    }
}
