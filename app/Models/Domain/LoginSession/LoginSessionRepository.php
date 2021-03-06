<?php
/**
 * LoginSessionRepository
 */

namespace App\Models\Domain\LoginSession;

/**
 * Interface LoginSessionRepository
 * @package App\Models\Domain
 */
interface LoginSessionRepository
{
    /**
     * ログインセッションを保存する
     *
     * @param LoginSessionEntity $loginSessionEntity
     * @return mixed
     */
    public function save(LoginSessionEntity $loginSessionEntity);

    /**
     * LoginSessionEntityを取得する
     *
     * @param string $sessionId
     * @return LoginSessionEntity
     */
    public function find(string $sessionId): LoginSessionEntity;

    /**
     * アカウントに紐づくログインセッションを削除する
     *
     * @param string $accountId
     */
    public function destroyByAccountId(string $accountId);

    /**
     * ログインセッションを削除する
     *
     * @param string $sessionId
     */
    public function destroy(string $sessionId);
}
