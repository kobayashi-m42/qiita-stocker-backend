<?php
/**
 * QiitaApiRepository
 */

namespace App\Infrastructure\Repositories\Api;

use App\Models\Domain\Stock\StockValue;
use App\Models\Domain\Stock\StockValues;
use App\Models\Domain\Account\AccountEntity;
use App\Models\Domain\Stock\FetchStockValues;
use App\Models\Domain\Stock\StockValueBuilder;

/**
 * Class QiitaApiRepository
 * @package App\Infrastructure\Repositories\Qiita
 */
class QiitaApiRepository extends Repository implements \App\Models\Domain\QiitaApiRepository
{
    /**
     * ストック一覧を取得する
     *
     * @param AccountEntity $accountEntity
     * @param int $page
     * @param int $perPage
     * @return FetchStockValues
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchStocks(AccountEntity $accountEntity, int $page, int $perPage): FetchStockValues
    {
        $response = $this->requestToStockApi($accountEntity->getUserName(), $accountEntity->getAccessToken(), $page, $perPage);

        $responseArray = json_decode($response->getBody());

        $stockTotalCount = $response->getHeader('total-count');

        $stockValues = [];
        foreach ($responseArray as $stock) {
            $stockValue = $this->buildStockValue($stock);
            array_push($stockValues, $stockValue);
        }

        return new FetchStockValues($stockTotalCount[0], ...$stockValues);
    }

    /**
     * Stock APIにリクエストを行う
     *
     * @param string $qiitaUserName
     * @param string $accessToken
     * @param int $page
     * @param int $perPage
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function requestToStockApi(string $qiitaUserName, string $accessToken, int $page, int $perPage)
    {
        $uri = sprintf(
            'https://qiita.com/api/v2/users/%s/stocks?page=%d&per_page=%d',
            $qiitaUserName,
            $page,
            $perPage
        );

        return $this->getClient()->request(
            'GET',
            $uri,
            ['headers' => ['Authorization' => 'Bearer '. $accessToken]]
        );
    }

    /**
     * StockValue を作成する
     *
     * @param object $stock
     * @return StockValue
     */
    private function buildStockValue(object $stock): StockValue
    {
        $articleCreatedAt = new \DateTime($stock->created_at);
        $tagNames = $this->buildTagNamesArray($stock->tags);

        $stockValueBuilder = new StockValueBuilder();
        $stockValueBuilder->setArticleId($stock->id);
        $stockValueBuilder->setTitle($stock->title);
        $stockValueBuilder->setUserId($stock->user->id);
        $stockValueBuilder->setProfileImageUrl($stock->user->profile_image_url);
        $stockValueBuilder->setArticleCreatedAt($articleCreatedAt);
        $stockValueBuilder->setTags($tagNames);

        return $stockValueBuilder->build();
    }

    /**
     * タグ名の配列を取得する
     *
     * @param array $tags
     * @return array
     */
    private function buildTagNamesArray(array $tags): array
    {
        $tagNames = [];
        foreach ($tags as $tag) {
            $tagName = $tag->name;
            array_push($tagNames, $tagName);
        }
        return $tagNames;
    }

    /**
     * ArticleIDのリストからアイテム一覧を取得する
     *
     * @param AccountEntity $accountEntity
     * @param array $stockArticleIdList
     * @return StockValues
     */
    public function fetchItemsByArticleIds(AccountEntity $accountEntity, array $stockArticleIdList): StockValues
    {
        $promises = [];
        foreach ($stockArticleIdList as $articleId) {
            $uri = sprintf('https://qiita.com/api/v2/items/%s', $articleId);
            $promises[] = $this->getClient()->requestAsync(
                'GET',
                $uri,
                ['headers' => ['Authorization' => 'Bearer '. $accountEntity->getAccessToken()]]
            );
        }

        $responses = \GuzzleHttp\Promise\all($promises)->wait();

        $stockValues = [];
        foreach ($responses as $response) {
            $stock = json_decode($response->getBody());
            $stockValue = $this->buildStockValue($stock);
            array_push($stockValues, $stockValue);
        }

        return new StockValues(...$stockValues);
    }
}
