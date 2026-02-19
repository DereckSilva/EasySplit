<?php

namespace App\Service;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class GeminiService
{

    private Client $client;
    private string $apiKey;
    private string $baseUrl;

    public function __construct(){
        $this->apiKey = env('GEMINI_API_KEY');
        $this->client = new Client();
        $this->baseUrl = env('GEMINI_URL');
    }

    public function chatService(string $message, array $functions) {

        $url = ("{$this->baseUrl}?key={$this->apiKey}");
        $body = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $message]]
                ]
            ]
        ];

        // refatorar para não ficar gigante essa função
        $body['tools'] = [
            'function_declarations' => [
                // função para criar uma conta
                [
                    'name' => 'create_account',
                    'description' => 'Cria uma conta no sistema',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                        ]
                    ]
                ],
                // função buscar as despesas totais

                // função para buscar as despesas por categoria
            ]
        ];

        try {
            $response = $this->client->post($url, [
                'json' => $body,
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $result = json_decode($response->getBody(), true);

            // realiza processamento das funções


            // Resposta de texto normal
            return [
                'type' => 'text',
                'text' => $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Sem resposta'
            ];

        } catch (\Exception $e) {
            return [
                'type' => 'error',
                'message' => 'Erro ao processar requisição'
            ];
        }
    }

}
