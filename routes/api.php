<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CEOController;
use App\Http\Controllers\API\TarifasController;
use App\Http\Controllers\API\IdiomasController;
use App\Http\Controllers\API\WSDynController;
use App\Models\User;
use App\Models\Role;
use App\Models\Agreement;
use App\Models\CEO;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Passport\Token;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->post('/logout', function (Request $request) {
    $token = $request->user()->token();
    $token->revoke();
    $response = ['message' => 'You have been successfully logged out!'];
    return response($response, 200);
});

Route::middleware('auth:api')->post('/token', function (Request $request) {
    $token = $request->user()->token();
    $response = ['message' => $token];
    return response($response, 200);
});

Route::middleware('auth:api')->post('/scope', function (Request $request) {
    $scopes = $request->user()->token()->scopes[0];
    $response = ['role' => $scopes];
    return response($response, 200);
});

Route::middleware('auth:api')->group(function () {
    // our routes to be protected will go in here
    Route::post('/isvalid', [AuthController::class, 'isvalid']);
});

//Route::apiResource('/ceos', CEOController::class)->middleware('auth:api');
//Route::apiResource('/tarifa', TarifasController::class)->middleware('auth:api');

/*Route::middleware('auth:api')->get('ceos', function(Request $request) {
    Route::post('/ceos', [CEOController::class, 'index']);
});*/

// reglas para roles de USERS
Route::middleware(['auth:api', 'role'])->group(function () {

    // List users
    Route::middleware(['scope:admin'])->post('/users', function (Request $request) {
        //return User::get();
        return [
            'total' => User::count(),
            'totalNotFiltered' => 800,
            'rows' => User::with('role')->get()
        ];
    });

    // Add/Edit User
    Route::middleware(['scope:admin'])->post('/user', function (Request $request) {
        $json = json_encode($request->all());
        $dataRequest = json_decode($json, true);
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'user_dyn' => 'max:255',
            'telefono' => 'required|max:255',
            'celular' => 'required|max:255',
            'agentePadre' => 'max:255',
            'status' => 'max:1'
        ]);

        $roleDefaul = "agente";

        $validatedData['password'] = Hash::make($request->password);
        $user = User::create($validatedData);
        $userRole = Role::create([
            'user_id' => $user->id,
            'role' => $dataRequest['role']
        ]);


        return response([
            'user' => $user,
        ]);
    });

    Route::middleware(['scope:admin'])->put('/user/{userId}', function (Request $request, $userId) {
        $tokensRevoke = 0;
        try {
            $json = json_encode($request->all());
            $dataRequest = json_decode($json, true);
            $user = User::findOrFail($userId);
            $role = Role::where('user_id', $userId)->get();
            $userRole = null;
            if (count($role) == 0) {
                $userRole = Role::create([
                    'user_id' => $userId,
                    'role' => $dataRequest['role']
                ]);
            } else if (count($role) == 1) {
                $userRole = Role::where('user_id', $userId)
                    ->update(['role' => $dataRequest['role']]);
            } else {
                $userRole = Role::where('user_id', $userId)
                    ->delete(['role' => $dataRequest['role']]);
                $userRole = Role::create([
                    'user_id' => $userId,
                    'role' => $dataRequest['role']
                ]);
            }
            $validateArr = [
                'name' => 'max:255',
                'lastname' => 'max:255',
                'email' => 'email|unique:users,email,' . $userId,
                'user_dyn' => 'max:255',
                'telefono' => 'max:255',
                'celular' => 'max:255',
                'agentePadre' => 'max:255',
                'status' => 'max:1'
            ];

            if (isset($request->all()['password'])) {
                $validateArr['password'] = 'required|confirmed';
                $validatedData = $request->validate($validateArr);
                $validatedData['password'] = Hash::make($request->password);
            } else {
                $validatedData = $request->validate($validateArr);
            }

            $user->update($validatedData);
            if (isset($validatedData['status']) &&  $validatedData['status'] != 'A') {
                $tokensRevoke = Token::where('user_id', $userId)
                    ->update(['revoked' => true]);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.'
            ], 403);
        }

        return response()->json([
            'message' => 'User updated successfully.',
            'userId' => $userId,
            'data' => $request->all(),
            'tokens' => $validatedData,
            'ROLE' => $userRole
        ]);
    });


    // Delete User
    Route::middleware(['scope:admin'])->delete('/user/{userId}', function (Request $request, $userId) {

        try {
            $user = User::findOfFail($userId);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.'
            ], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    });
});

// reglas para roles de CEO
Route::middleware(['auth:api', 'role'])->group(function () {

    // List Ceo
    Route::middleware(['scope:admin'])->get('/ceos', [CEOController::class, 'index']);
    //Guardar
    Route::middleware(['scope:admin'])->post('/ceo', [CEOController::class, 'store']);
    //ver Detalle
    Route::middleware(['scope:admin'])->post('/ceo/{ceoId}', [CEOController::class, 'show']);
    //Actualizar
    Route::middleware(['scope:admin'])->put('/ceo/{ceoId}', [CEOController::class, 'update']);
    //eliminar
    Route::middleware(['scope:admin'])->delete('/ceo/{ceoId}', [CEOController::class, 'destroy']);
});

// reglas para roles de Tarifas
Route::middleware(['auth:api', 'role'])->group(function () {

    // Listar
    Route::middleware(['scope:admin,agente'])->get('/tarifas', [TarifasController::class, 'index']);
    //Guardar
    Route::middleware(['scope:admin'])->post('/tarifa', [TarifasController::class, 'store']);
    //ver Detalle
    Route::middleware(['scope:admin,agente'])->post('/tarifa/{tarifaId}', [TarifasController::class, 'show']);
    //Actualizar
    Route::middleware(['scope:admin'])->put('/tarifa/{tarifaId}', [TarifasController::class, 'update']);
    //eliminar
    Route::middleware(['scope:admin'])->delete('/tarifa/{tarifaId}', [TarifasController::class, 'destroy']);
});

// reglas para roles de Idiomas
Route::middleware(['auth:api', 'role'])->group(function () {

    // Listar
    Route::middleware(['scope:admin'])->post('/idiomas', [IdiomasController::class, 'index']);
    //Guardar
    Route::middleware(['scope:admin'])->post('/idioma', [IdiomasController::class, 'store']);
    //ver Detalle
    Route::middleware(['scope:admin,agente'])->post('/idioma/{idiomaId}', [IdiomasController::class, 'show']);
    //Actualizar
    Route::middleware(['scope:admin'])->put('/idioma/{idiomaId}', [IdiomasController::class, 'update']);
    //eliminar
    Route::middleware(['scope:admin'])->delete('/idioma/{idioma}', [IdiomasController::class, 'destroy']);
    // Listar
    Route::middleware(['scope:admin,agente'])->post('/idioma/{idiomaId}/{sectionId}', [IdiomasController::class, 'showpart']);

    Route::middleware(['scope:admin'])->post('/languages', [IdiomasController::class, 'lstLanguages']);

    Route::middleware(['scope:admin,agente'])->post('/idiomasSecciones/{idiomaId}', [IdiomasController::class, 'lstSectionsLanguage']);
});

// reglas para roles de WSDynController
Route::middleware(['auth:api', 'role'])->group(function () {

    // Llamar metodo agentList WS
    Route::middleware(['scope:admin,agente'])->post('/agentList', [WSDynController::class, 'agentList']);
    Route::middleware(['scope:admin,agente'])->post('/createContactDetails', [WSDynController::class, 'createContactDetails']);
    Route::middleware(['scope:admin,agente'])->post('/createAgreementDetail', [WSDynController::class, 'createAgreementDetail']);
    Route::middleware(['scope:admin,agente'])->post('/registrarContrato', [WSDynController::class, 'registrarContrato']);
});

Route::post('/idioma/{idiomaId}/{sectionId}', [IdiomasController::class, 'showpart']);

Route::middleware(['auth:api', 'role'])->group(function () {
// Ruta para listado contratos
Route::middleware(['scope:admin,agente'])->post('/lstcontrato', function (Request $request) {
        $contratos = Agreement::where('user_id', auth()->user()->id)->get();
        return [
            'total' => $contratos->count(),
            'totalNotFiltered' => 800,
            'rows' => $contratos
        ];
    });
});
