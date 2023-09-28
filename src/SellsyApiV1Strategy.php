<?php


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
        if (null === $varyHeaders || $varyHeaders->isEmpty()) {
            return hash(
                'sha256',
                'greedy'.$request->getMethod().$request->getUri().json_encode($request->getBody())
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
            'greedy'.$request->getMethod().$request->getUri().json_encode($cacheHeaders).json_encode($request->getBody())
        );
    }
}
