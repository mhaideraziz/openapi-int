<?php

namespace App\Http\Controllers;

use App\Services\ChatGptClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatGptController extends ChatGptClient
{
    public function completion(Request $request){
        return $this->postCompletion('completion', $request);
    }

    public function chatCompletion(Request $request){
        return $this->postChatCompletion('chat-completion', $request);
    }

    public function edits(Request $request){
        return $this->postEdits('edits', $request);
    }

    public function audioTranslation(Request $request){
        $validator = Validator::make($request->all(), [
            'file'  => 'required|mimes:mp3,mp4,mpeg,mpga,m4a,wav,webm | max:50000'
        ]);
        if($validator->fails()){
            return $validator->errors()->messages()['file'][0];
        }
        else{
            return $this->_audioTranslation('audio-translations', $request);
        }

    }

    public function getModelsList(){
        return $this->get('models');
    }

    public function getModelByName($name){
        return $this->getWithParam('models', $name);
    }

    public function generateImage(Request $request){
        return $this->_generateImage('image-generate', $request);
    }

    public function moderations(Request $request){
        return $this->_moderations('moderations', $request);
    }
}
