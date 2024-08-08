<?php declare(strict_types = 1);

namespace Contributte\Elastica;

use Elastic\Elasticsearch\Response\Elasticsearch;
use Elastica\Client as ElasticaClient;
use Elastica\ResponseConverter;
use Nette\SmartObject;
use Psr\Http\Message\RequestInterface;
use Throwable;

class Client extends ElasticaClient
{

	use SmartObject;

	/** @var callable[] */
	public array $onSuccess = [];

	/** @var callable[] */
	public array $onFailure = [];

	public function sendRequest(RequestInterface $request): Elasticsearch
	{
		$start = microtime(true);

		try {
			$result = parent::sendRequest($request);

			$elasticaResponse = ResponseConverter::toElastica($result);
			$this->onSuccess($this, $this->getTransport()->getLastRequest(), $elasticaResponse, microtime(true) - $start);
		} catch (Throwable $e) {
			$this->onFailure($this, $this->getTransport()->getLastRequest(), $e, microtime(true) - $start);

			throw $e;
		}

		return $result;
	}

}
