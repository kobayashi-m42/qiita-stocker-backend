<?php
/**
 * StockController
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockScenario;
use Illuminate\Http\JsonResponse;

/**
 * Class StockController
 * @package App\Http\Controllers
 */
class StockController extends Controller
{
    /**
     * StockScenario
     * @var
     */
    private $stockScenario;

    /**
     * StockController constructor.
     * @param StockScenario $stockScenario
     */
    public function __construct(StockScenario $stockScenario)
    {
        $this->stockScenario = $stockScenario;
    }

    /**
     * ストック一覧を取得する
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Models\Domain\Exceptions\LoginSessionExpiredException
     * @throws \App\Models\Domain\Exceptions\ServiceUnavailableException
     * @throws \App\Models\Domain\Exceptions\UnauthorizedException
     * @throws \App\Models\Domain\Exceptions\ValidationException
     */
    public function index(Request $request): JsonResponse
    {
        $sessionId = $request->bearerToken();
        $params = [
            'sessionId'    => $sessionId,
            'page'         => $request->query('page'),
            'perPage'      => $request->query('per_page'),
            'uri'          => env('APP_URL') . $request->getPathInfo()
        ];

        $response = $this->stockScenario->index($params);

        return response()
            ->json($response['stocks'])
            ->setStatusCode(200)
            ->header('Total-Count', $response['totalCount'])
            ->header('Link', $response['link']);
    }

    /**
     * カテゴライズされたストック一覧を取得する
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Models\Domain\Exceptions\CategoryNotFoundException
     * @throws \App\Models\Domain\Exceptions\LoginSessionExpiredException
     * @throws \App\Models\Domain\Exceptions\UnauthorizedException
     * @throws \App\Models\Domain\Exceptions\ValidationException
     */
    public function showCategorized(Request $request): JsonResponse
    {
        $sessionId = $request->bearerToken();
        $params = [
            'sessionId'    => $sessionId,
            'id'           => $request->id,
            'page'         => $request->query('page'),
            'perPage'      => $request->query('per_page'),
            'uri'          => env('APP_URL') . $request->getPathInfo()
        ];

        $response = $this->stockScenario->showCategorized($params);

        return response()
            ->json($response['stocks'])
            ->setStatusCode(200)
            ->header('Total-Count', $response['totalCount'])
            ->header('Link', $response['link']);
    }
}
