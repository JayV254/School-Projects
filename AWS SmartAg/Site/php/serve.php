<?php
require '../../php-sdk/aws-autoloader.php';

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

$sdk = new Aws\Sdk([
    'region'   => 'us-east-2',
    'version'  => 'latest',
]);

$dynamodb = $sdk->createDynamoDb(['profile' => 'admin']);

function get_data($measure, $interval) {
    $sdk = new Aws\Sdk([
        'region'   => 'us-east-2',
        'version'  => 'latest',
    ]);

    $dynamodb = $sdk->createDynamoDb(['profile' => 'admin']);
    
    date_default_timezone_set("America/Kentucky/Louisville");
    $end = date("Y-m-d H:i:s");
    if($interval == "hour") {
	$start = date("Y-m-d H:i:s", strtotime("-1 hours"));
    }
    else {
	$start = date("Y-m-d H:i:s", strtotime("-1 day"));
    }

    $marshaler = new Marshaler();
    $eav_statement =  [
        ':start' => $start,
        ':end' => $end
    ];

    $eav_json = json_encode($eav_statement);

    $eav = $marshaler->marshalJson($eav_json);

    $params = [
        'TableName' => 'SensorData',
        'FilterExpression' => '#DT between :start and :end',
        'ExpressionAttributeNames' => ['#DT' => 'DateTime'],
        'ExpressionAttributeValues'=> $eav
    ];


    // initialize arrays that will be converted to json for file storage
    $result_arr = array();

    //query DB and store data
    try {
        $query = $dynamodb->scan($params);
        foreach($query['Items'] as $entry) {
	    foreach($entry as $output) {
	        if(array_key_exists('M',$output)) {
		    $DT = $output['M']["Date_Time"]['S'];
		    $result_arr[] = array('DT' => $DT, 'value' => $output['M'][$measure]['S']);
		}
	    }
	}
	return $result_arr;		

    } catch (DynamoDbException $e) {
        echo "Unable to scan table:\n";
        echo $e->getMessage() . "\n";
    }
}
?>

