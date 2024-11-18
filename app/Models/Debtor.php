<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Aws\DynamoDb\DynamoDbClient;


class Debtor extends Model
{
    protected $fillable = ['cuit', 'worst_situation', 'loan_sum'];

    protected static function getDynamoDbClient()
    {
        return new DynamoDbClient([
            'region' => env('AWS_DEFAULT_REGION', 'us-west-2'),
            'version' => 'latest',
            'endpoint' => env('DYNAMODB_LOCAL_ENDPOINT', 'http://localhost:5000'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
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
