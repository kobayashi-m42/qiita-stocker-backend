<?php
/**
 * QiitaAccountValue
 */

namespace App\Models\Domain;

use App\Models\Domain\Account\AccountEntity;
use App\Models\Domain\Account\AccountRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class QiitaAccountValue
 * @package App\Models\Domain
 */
class QiitaAccountValue
{
    /**
     * パーマネントID
     *
     * @var string
     */
    private $permanentId;

    /**
     * ユーザ名
     *
     * @var string
     */
    private $userName;

    /**
     * アクセストークン
     *
     * @var string
     */
    private $accessToken;

    /**
     * QiitaAccountValue constructor.
     * @param QiitaAccountValueBuilder $builder
     */
    public function __construct(QiitaAccountValueBuilder $builder)
    {
        $this->permanentId = $builder->getPermanentId();
        $this->accessToken = $builder->getAccessToken();
        $this->userName = $builder->getUserName();
    }

    /**
     * @return string
     */
    public function getPermanentId(): string
    {
        return $this->permanentId;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * permanentIDからAccountEntityを取得する
     *
     * @param AccountRepository $accountRepository
     * @return AccountEntity
     */
    public function findAccountEntityByPermanentId(AccountRepository $accountRepository): AccountEntity
    {
        try {
            return $accountRepository->findByPermanentId($this);
        } catch (ModelNotFoundException $e) {
            throw new \RuntimeException();
        }
    }

    /**
     * アカウントが作成済みか確認する
     *
     * @param AccountRepository $accountRepository
     * @return bool
     */
    public function isCreatedAccount(AccountRepository $accountRepository): bool
    {
        try {
            $accountRepository->findByPermanentId($this);
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * アカウント作成時にバリデーションエラーの場合に使用するメッセージ
     *
     * @return string
     */
    public static function createAccountValidationErrorMessage(): string
    {
        return '不正なリクエストが行われました。再度、アカウント登録を行なってください。';
    }

    /**
     * ログイン時にバリデーションエラーの場合に使用するメッセージ
     *
     * @return string
     */
    public static function createLoginSessionValidationErrorMessage(): string
    {
        return '不正なリクエストが行われました。再度、ログインしてください。';
    }
}
