<?php

declare(strict_types=1);

namespace muqsit\curlwithssl;

use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\BulkCurlTask;
use pocketmine\scheduler\BulkCurlTaskOperation;
use pocketmine\utils\InternetException;
use pocketmine\utils\InternetRequestResult;
use RuntimeException;
use function count;
use function fclose;
use function fwrite;
use function json_decode;
use function stream_get_contents;
use function stream_get_meta_data;
use function tmpfile;
use const CURLOPT_CAINFO;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_SSL_VERIFYPEER;
use const JSON_THROW_ON_ERROR;

final class Main extends PluginBase{

	public string $cainfo_path; // value to be set to CURLOPT_CAINFO

	/** @var resource */
	private mixed $_cainfo_resource; // stores CA cert info during runtime, deleted on server shutdown

	protected function onEnable() : void{
		// read contents of resources/cacert.pem
		$resource = $this->getResource("cacert.pem"); // 'resource' pointing to the file
		$contents = stream_get_contents($resource); // read contents of the file
		fclose($resource); // we don't need the 'resource' anymore after we have read contents of the file

		// PHP's cURL API does not support the "CURLOPT_CAINFO_BLOB", therefore we will be using
		// "CURLOPT_CAINFO" instead, which holds the path to a file on disk where the CA certificate
		// is stored.
		$resource = tmpfile();
		$resource !== false || throw new RuntimeException("Failed to create a temporary file to store CA certificate");
		fwrite($resource, $contents);
		$this->cainfo_path = stream_get_meta_data($resource)["uri"];
		$this->_cainfo_resource = $resource;

		$this->callApi();
	}

	protected function onDisable() : void{
		// Delete the temporary file that we created to store the CA certificate
		fclose($this->_cainfo_resource);
		unset($this->_cainfo_resource);
	}

	public function callApi() : void{
		// be sure to set the following curl options
		$curl_opts = [
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_CAINFO => $this->cainfo_path
		];

		$operations = [
			// GET request
			new BulkCurlTaskOperation("http://curlwithssltest.pages.dev/api/profile", 10, [], $curl_opts),

			// POST request
			new BulkCurlTaskOperation("http://curlwithssltest.pages.dev/api/profile", 10, [], $curl_opts + [CURLOPT_CUSTOMREQUEST => "POST"])
		];

		$on_completion = function(array $results) : void{
			$this->getLogger()->notice("Validating responses...");
			try{
				/** @var list<InternetRequestResult> $results */
				count($results) === 2 || throw new RuntimeException("Expected 2 results, got " . count($results));

				!($results[0] instanceof InternetException) || throw new RuntimeException("Validation of first response failed: {$results[0]->getMessage()}", $results[0]->getCode(), $results[0]);
				$result = json_decode($results[0]->getBody(), true, 512, JSON_THROW_ON_ERROR);
				$result === ["name" => "John Doe"] || throw new RuntimeException("Validation of first response failed");

				!($results[1] instanceof InternetException) || throw new RuntimeException("Validation of second response failed: {$results[1]->getMessage()}", $results[1]->getCode(), $results[1]);
				$result = json_decode($results[1]->getBody(), true, 512, JSON_THROW_ON_ERROR);
				$result === ["message" => "Successfully updated profile information."] || throw new RuntimeException("Validation of second response failed");
			}catch(RuntimeException $e){
				$this->getLogger()->notice("Response validation failed: {$e->getMessage()}");
				return;
			}
			$this->getLogger()->notice("Successfully validated responses");
		};

		$this->getLogger()->notice("Calling API...");
		$this->getServer()->getAsyncPool()->submitTask(new BulkCurlTask($operations, $on_completion));
	}
}