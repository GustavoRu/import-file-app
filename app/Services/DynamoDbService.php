<?php
namespace App\Services;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;

class DynamoDbService
{
    protected $client;

    public function __construct()
    {
        $this->client = new DynamoDbClient([
            'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
            'version' => 'latest',
            'endpoint' => env('DYNAMODB_LOCAL_ENDPOINT', 'http://localhost:5000'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function putItem($tableName, $item)
    {
        try {
            return $this->client->putItem([
                'TableName' => $tableName,
                'Item' => $item,
            ]);
        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error al insertar el item en DynamoDB', [
                'table' => $tableName,
                'item' => $item,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getAwsErrorCode(),
            ]);

            throw $e;
        }
    }


    public function batchWriteItems($tableName, $items)
    {
        // dd("items",$items);
        try {
            // Construir los elementos de la solicitud
            // $requestItems = array_map(function ($item) {
            //     dd($item);
            //     return [
            //         'PutRequest' => ['Item' => $item],
            //     ];
            // }, $items);

            // Ejecutar la operación BatchWriteItem
            $result = $this->client->batchWriteItem([
                'RequestItems' => [
                    $tableName => $items,
                ],
            ]);

            // Verificar si hubo elementos que no se pudieron insertar
            if (isset($result['UnprocessedItems']) && count($result['UnprocessedItems']) > 0) {
                \Log::warning('Unprocessed items found during batch write', [
                    'table' => $tableName,
                    'unprocessed_items' => $result['UnprocessedItems'],
                ]);

                // Opcional: Implementar lógica para reintentar o manejar los ítems no procesados
                // Por ahora, solo devolvemos los ítems no procesados
                return $result['UnprocessedItems'];
            }

            return $result;
        } catch (\Aws\Exception\AwsException $e) {
            \Log::error('Error during batch write operation in DynamoDB', [
                'table' => $tableName,
                'items' => $items,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getAwsErrorCode(),
            ]);

            // Re-lanzar la excepción para manejarla más arriba si es necesario
            throw $e;
        }
    }


    public function scanItems($tableName)
    {
        try {
            $result = $this->client->scan([
                'TableName' => $tableName,
            ]);
            return $result['Items'] ?? [];
        } catch (DynamoDbException $e) {
            if ($e->getAwsErrorCode() === 'ResourceNotFoundException') {
                return [];
            }

            // Relanzar la excepción si es otro error
            throw $e;
        }
    }

}
