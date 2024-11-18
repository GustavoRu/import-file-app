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
        $requestItems = array_map(function ($item) {
            return [
                'PutRequest' => ['Item' => $item],
            ];
        }, $items);

        $result = $this->client->batchWriteItem([
            'RequestItems' => [
                $tableName => $requestItems,
            ],
        ]);

        // Verificar si hubo elementos que no se pudieron insertar
        if (isset($result['UnprocessedItems']) && count($result['UnprocessedItems']) > 0) {
            // Si hay ítems no procesados, puedes intentar reinsertarlos o gestionar el error
            dd('Unprocessed items: ', $result['UnprocessedItems']);
        }

        return $result;
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
