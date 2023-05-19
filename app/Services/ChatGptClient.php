<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatGptClient
{
    const imageFolderName = 'translationFiles';
    protected $orgId;
    protected $authId;
    protected $baseUrl;
    protected $urls = [
        'models' => 'models',
        'completion' => 'completions',
        'chat-completion' => 'chat/completions',
        'edits' => 'edits',
        'image-generate' => 'images/generations',
        'moderations' => 'moderations',
        'audio-translations' => 'audio/translations',
    ];
    protected $httpClient;


    public function __construct()
    {
        $this->orgId = env('ORGANIZATION_ID');
        $this->authId = env('AUTHORIZATION_TOKEN');
        $this->baseUrl = 'https://api.openai.com/v1/';
        $this->httpClient = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->authId,
            'OpenAI-Organization' => $this->orgId,
            "Content-Type" => 'application/json'
        ]);
    }

    protected function get($method)
    {
        $response = $this->httpClient->get($this->baseUrl . $this->urls[$method]);
        $data['response'] = json_decode($response->getBody()->getContents(), true);
        $data['status'] = $response->getStatusCode();
        return $data;
    }

    protected function getWithParam($method, $param)
    {
        $response = $this->httpClient->get($this->baseUrl . $this->urls[$method] . '/' . $param);
        $data['response'] = json_decode($response->getBody()->getContents(), true);
        $data['status'] = $response->getStatusCode();
        return $data;
    }

    protected function postCompletion($method, $body)
    {
        $response = $this->httpClient->post($this->baseUrl . $this->urls[$method], [
            "model" => $body->has('model') ? $body->model : 'text-davinci-003',
            "prompt" => $body->has('prompt') ? $body->prompt : 'Hello, How are you?',
            "max_tokens" => $body->has('max_tokens') ? $body->max_tokens : 2048,
            "temperature" => $body->has('temperature') ? $body->temperature : 1,
        ]);
        $data['response'] = json_decode($response->getBody()->getContents(), true);
        $data['status'] = $response->getStatusCode();
        return $data;
    }

    protected function _generateImage($method, $body)
    {
        $response = $this->httpClient->post($this->baseUrl . $this->urls[$method], [
            "prompt" => $body->has('prompt') ? $body->prompt : 'Spiderman',
            "n" => $body->has('n') ? $body->n : 1,
            "size" => $body->has('size') ? $body->size : '1024x1024',
        ]);
        $data['response'] = json_decode($response->getBody()->getContents(), true);
        $data['status'] = $response->getStatusCode();
        return $data;
    }

    protected function postChatCompletion($method, $body)
    {
        $response = $this->httpClient->post($this->baseUrl . $this->urls[$method], [
            "model" => $body->has('model') ? $body->model : 'gpt-3.5-turbo',
            "messages" => $body->has('messages') ? $body->messages : [
                [
                    "role" => "user",
                    "content" => "Who won the world series in 2020?"
                ],
                [
                    "role" => "assistant",
                    "content" => "The Los Angeles Dodgers won the World Series in 2020."
                ],
                [
                    "role" => "user",
                    "content" => "Where was it played?"
                ]
            ],
            "max_tokens" => $body->has('max_tokens') ? $body->max_tokens : 2048,
            "temperature" => $body->has('temperature') ? $body->temperature : 1,
            "top_p" => $body->has('top_p') ? $body->top_p : 1,
            "n" => $body->has('n') ? $body->n : 1,
            "stream" => $body->has('stream') ? $body->stream : false,
            "presence_penalty" => $body->has('presence_penalty') ? $body->presence_penalty : 0,
            "frequency_penalty" => $body->has('frequency_penalty') ? $body->frequency_penalty : 0,
        ]);
        $data['response'] = json_decode($response->getBody()->getContents(), true);
        $data['status'] = $response->getStatusCode();
        return $data;
    }

    protected function postEdits($method, $body)
    {
        $response = $this->httpClient->post($this->baseUrl . $this->urls[$method], [
            "model" => $body->has('model') ? $body->model : 'text-davinci-edit-001',
            "input" => $body->has('input') ? $body->input : 'What day of the wek is it?',
            "instruction" => $body->has('instruction') ? $body->instruction : 'Fix the spelling mistakes',
        ]);
        $data['response'] = json_decode($response->getBody()->getContents(), true);
        $data['status'] = $response->getStatusCode();
        return $data;
    }

    protected function _moderations($method, $body)
    {
        $response = $this->httpClient->post($this->baseUrl . $this->urls[$method], [
            "input" => $body->has('input') ? $body->input : 'I want to kill them.',
        ]);
        $data['response'] = json_decode($response->getBody()->getContents(), true);
        $data['status'] = $response->getStatusCode();
        return $data;
    }

    protected function _audioTranslation($method, $body)
    {
        $bodyFile = $body->file('file');
        $fileUploadedPath = $bodyFile->store(self::imageFolderName, 'public');
//        dd($fileUploadedPath);

        $response = $this->httpClient
            ->attach('file', $fileUploadedPath, 'video/mp4')
            ->post($this->baseUrl . $this->urls[$method], [
//                "file" => $fileUploadedPath,
                "model" => $body->has('model') ? $body->model : 'whisper-1',
                "response_format" => $body->has('response_format') ? $body->response_format : 'json',
                "temperature" => $body->has('temperature') ? $body->temperature : 0,
        ]);
        $data['response'] = json_decode($response->getBody()->getContents(), true);
        $data['status'] = $response->getStatusCode();
        return $data;
    }
}
