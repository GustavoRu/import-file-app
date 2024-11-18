<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Aws\DynamoDb\DynamoDbClient;
use App\Services\DynamoDbService;

class Debtor extends Model
{
    protected $fillable = ['cuit', 'worst_situation', 'loan_sum'];

    protected static function getDynamoDbClient()
    {
        $dynamoDbService = app(DynamoDbService::class);
        return $dynamoDbService->getClient();
    }

    public static function createTable()
    {
        $client = self::getDynamoDbClient();

        // Verificar si la tabla ya existe
        $existingTables = $client->listTables()['TableNames'];

        if (in_array('Debtors', $existingTables)) {
            // echo "La tabla 'Debtors' ya existe.\n";
            return;
        }

        $result = $client->createTable([
            'TableName' => 'Debtors',
            'KeySchema' => [
                [
                    'AttributeName' => 'cuit',
                    'KeyType' => 'HASH', // Partition key
                ],
            ],
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'cuit',
                    'AttributeType' => 'N',
                ],
            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits' => 5,
                'WriteCapacityUnits' => 5,
            ],
        ]);

        return $result;
    }
}
