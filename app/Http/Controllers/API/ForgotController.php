<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ForgotRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResetRequest;
use App\Http\Resources\ForgotResource;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class ForgotController extends Controller
{
    public function forgot (ForgotRequest $request)
    {
        $email = $request->input(['email']);
        $language = $request->input(['language']);

        if(User::where('email', $email)->where('status', 'A')->doesntExist()){
            return response([
                'message' => 'Email doesn\'t Exist'
            ], 404);
        }

        try {
            DB::table('password_resets')
            ->where('email',$email)
            ->update(array(
                'status'=>'C',
            ));
            $token = Str::random(15);
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at'=> new \DateTime(),
                'status' => 'A'
            ]);
            
            //send email
            Mail::send('emails.forgot_'.$language, ['token' => $token], function ($message) use ($email, $language) {
                $message->to($email);
                $message->subject($language == 'es'? "Solicitud de Cambio de Contraseña de NASER": "NASER'S Password Change Request");
            });
            
            return response([
                'message' => 'Check your email'
            ],200);
        } catch (\Exception $exception) {
            return response ([
                'meesage' => $exception->getMessage()
            ],400);
        }

    }

    function reset (ResetRequest $request){
        $token = $request->input(['token']);
        $password = $request->input(['password']);        
        $password_confirm = $request->input(['password_confirmation']);
        $fechaMinima = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime(date('Y-m-d H:i:s'))));

        if (!$passwordResets = DB::table('password_resets')
                ->where('token', $token )
                ->where('status', 'A' )
                ->where('created_at', '>=', $fechaMinima)
                ->first()){
            return response([
                'message' => 'Invalid Token',
            ], 400);
        }

        if(!$user = User::where('email', $passwordResets->email)
                        ->where('status', 'A')->first()){
            return response([
                'message' => 'User dosen\t exist'
            ], 404);
        }

        DB::table('password_resets')
            ->where('email',$user->email)
            ->update(array(
                'status'=>'C',
            ));

        $user->password = Hash::make($password);
        $user->save();

        return response(['message'=>'success'],200);

    }
}
