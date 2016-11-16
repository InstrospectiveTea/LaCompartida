<?php

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;

class DynamoDb {

	private $client;

	public function __construct(Array $config = []) {
		$config += Conf::AmazonKey();
		try {
			$this->client = DynamoDbClient::factory($config);
		} catch (DynamoDbException $e) {
			throw new Exception('The item could not be retrieved.');
		}
	}

	public function get($request, $parsed = true) {
		try {
			$result = $this->client->getItem($request);
		} catch (DynamoDbException $e) {
			throw new Exception('The item could not be retrieved.');
		}
		return $parsed ? $this->parseFields($result['Item']) : $result['Item'];
	}

	public function update($table, $key, $values) {
		$this->client->updateItem(array(
			'TableName' => $table,
			'Key' => $key,
			'AttributeUpdates' => $values
		));
	}

	public function listTable($table) {
		$iterator = $this->client->getIterator('Scan', array(
			'TableName' => $table
		));
		$collection = [];
		foreach ($iterator as $item) {
			$collection[] = $this->parseFields($item);
		}
		return $collection;
	}

	public function parseFields($fields) {
		$values = [];
		foreach ($fields as $field => $value) {
			if (is_string($value)) {
				$values[$field] = $value;
			} else if (isset($value['S'])) {
				$values[$field] = $value['S'];
			} else if (isset($value['N'])) {
				$values[$field] = $value['N'];
			}
		}
		return $values;
	}

}
