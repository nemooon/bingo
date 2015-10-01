<?php

namespace App\Http\Controllers;

use Storage;
use App\Game;
use App\Number;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Uzulla\WebApi\VoiceText\Request as VTR;
use Uzulla\WebApi\VoiceText\Query as VTQ;

class BingoController extends Controller
{

    public function index()
    {
        return view('bingo.index');
    }

    public function start()
    {
        $game = null;
        $game_token = session('game_token');

        if ($game_token)
        {
            $game = Game::where('token', $game_token)
                ->whereNull('finish_at')->latest()->first();
        }

        if (!$game)
        {
            $game = new Game;
            $game->token = str_random(10);
            $game->save();
            session(['game_token' => $game->token]);
        }

        return response()->json([
            'game_token' => $game->token,
            'numbers' => $game->numbers,
        ]);
    }

    public function call()
    {
        $game = Game::where('token', session('game_token'))->firstOrFail();
        $number = $game->numbers()->whereNull('call_at')->orderByRaw('RAND()')->first();
        $number->call_at = $number->freshTimestamp();
        $number->save();

        if ($game->numbers()->whereNull('call_at')->get()->isEmpty()) {
            $game->finish_at = $game->freshTimestamp();
            $game->save();
        }

        return response()->json([
            'call_number' => $number->id,
        ]);
    }

    public function reset()
    {
        $game = null;
        $game_token = session('game_token');

        if ($game_token)
        {
            $game = Game::where('token', $game_token)->latest()->first();
            $game->finish_at = $game->freshTimestamp();
            $game->save();
            session('game_token', false);
        }

        return response()->json([
            'status' => true,
        ]);
    }

    public function voicetext($text)
    {
        $filename = 'voicetext/'.$text.'.wav';

        if (!Storage::disk('local')->exists($filename)) {
            \Uzulla\WebApi\VoiceText\Query::$defaultApiKey = 'zn17t88ibywm8aht';
            $query = new VTQ;
            $query->text = $text;
            $query->speaker = 'show';
            $query->format = 'wav';
            $query->pitch = 125;
            $query->speed = 90;
            $query->volume = 120;
            $res = VTR::getResponse($query);
            if ($res->isSuccess()) {
                Storage::disk('local')->put($filename, file_get_contents($res->tempFileName));
            } else {
                dd($res);
            }
        }

        return response()->download(storage_path('app/'.$filename));
    }

}
