<?php

namespace App\Http\Controllers\Api;

use JWTAuth;

use Validator;
use App\Models\User;
use App\Models\UserScore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;


class UserAuthController extends Controller
{
    public $token = true;
  
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), 
                      [ 
                      'name' => 'required',
                      'email' => 'required|email|unique:users',
                      'password' => 'required',  
                      'c_password' => 'required|same:password', 
                     ]);  
 
         if ($validator->fails()) {  
                return response()->json(['error'=>$validator->errors()], 401); 
            }   
 
 
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
  
        if ($this->token) {
            return $this->login($request);
        }
  
        return response()->json([
            'success' => true,
            'data' => $user
        ], Response::HTTP_OK);
    }
    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;
  
        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return response()->json([
            'success' => true,
            'token' => $jwt_token
        ]);
    }
    
    public function userProfile(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
        $user = JWTAuth::authenticate($request->token);
        $user_all_score = UserScore::where('user_id', $user->id)->orderBy('score', 'DESC')->get();
        if(!empty($user_all_score)) {
            foreach($user_all_score as $user_score) {
                $data[] = array(
                    'id' => $user_score->id,
                    'game_name' => $user_score->getGame->game_name,
                    'user_name' => $user_score->getUser->name,
                    'score' =>$user_score->score,
                );
            }
        }
        else {
            $data = array();
        }
        return response()->json(['user' => $user,'UserGameScore' => $data]);
    }

    public function saveGameScore(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'game_id' => 'required',
            'score' => 'required|numeric',
        ]);
        $user = JWTAuth::authenticate($request->token);
        $user_id = $user->id;
        $game_score = new UserScore;
        $game_score->user_id = $user_id;
        $game_score->game_id = $request->game_id;
        $game_score->score = $request->score;
        $game_score->save();
        
        $message = 'Game Score Saved Succesfully';

        return response()->json(
            [
                'success' => true,
                'Message' => $message
            ]
            ,Response::HTTP_OK
        );
    }

    public function getTopPlayer() {
        $user_all_score = UserScore::orderBy('score', 'DESC')->get()->take(10);
        if(!empty($user_all_score)) {
            foreach($user_all_score as $user_score) {
                $data[] = array(
                    'id' => $user_score->id,
                    'game_name' => $user_score->getGame->game_name,
                    'user_name' => $user_score->getUser->name,
                    'score' =>$user_score->score,
                );
            }
        }
        else {
            $data = array();
        }
        return response()->json(['UserGameScore' => $data]);
    }
    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);
  
        try {
            JWTAuth::invalidate($request->token);
  
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
