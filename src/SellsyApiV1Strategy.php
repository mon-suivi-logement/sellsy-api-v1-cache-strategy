<?php

namespace MonSuiviLogement\GuzzleCache\Strategy;

use Kevinrob\GuzzleCache\KeyValueHttpHeader;
use Psr\Http\Message\RequestInterface;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
/**
 * This strategy represents a Sellsy Api v1 Client on top of a "greedy" HTTP client.
 *
 */
class SellsyApiV1Strategy extends GreedyCacheStrategy
{

    protected function getCacheKey(RequestInterface $request, KeyValueHttpHeader $varyHeaders = null)
    {
		$do_in_content = $this->extractSellsyDoInContent($request);
		if (null === $varyHeaders || $varyHeaders->isEmpty()) {
			return hash(
				'sha256',
				'greedy'.$request->getMethod().$request->getUri().json_encode($do_in_content)
			);
		}

		$cacheHeaders = [];
		foreach ($varyHeaders as $key => $value) {
			if ($request->hasHeader($key)) {
				$cacheHeaders[$key] = $request->getHeader($key);
			}
		}

		return hash(
			'sha256',
			'greedy'.$request->getMethod().$request->getUri().json_encode($cacheHeaders).json_encode($do_in_content)
		);
	}

	protected function extractSellsyDoInContent(RequestInterface $request)
	{
		$body = $request->getBody();
		$body->rewind();

		$boundary = $body->getBoundary();

		$form_data_blocks = preg_split("/-+$boundary/", $body->getContents());
		array_pop($form_data_blocks);

		foreach ($form_data_blocks as $form_data_block)
		{
			if (empty($form_data_block)) {continue;}

			preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $form_data_block, $matches);

			if($matches[1] == 'do_in') {return $matches[2];}
		}

		return uniqid();//Don't cache
	}
}
