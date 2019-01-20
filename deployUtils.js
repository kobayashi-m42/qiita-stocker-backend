/**
 * 許可されたデプロイステージかどうか判定する
 *
 * @param deployStage
 * @return {boolean}
 */
exports.isAllowedDeployStage = deployStage =>
  deployStage === "local" ||
  deployStage === "dev" ||
  deployStage === "stg" ||
  deployStage === "prod";

/**
 * SecretIdsを取得する
 *
 * @param deployStage
 * @return {string[]}
 */
exports.findSecretIds = deployStage => [`${deployStage}/qiita-stocker`];

/**
 * AWSのプロファイル名を取得する
 *
 * @param deployStage
 * @return {string}
 */
exports.findAwsProfile = deployStage => {
  if (deployStage === "prod") {
    return "qiita-stocker-prod";
  }

  return "qiita-stocker-dev";
};

/**
 * DBのホスト名を取得する
 *
 * @param deployStage
 * @return {string}
 */
exports.findDbHost = deployStage => {
  if (deployStage === "local") {
    return "mysql";
  }

  return `qiita-stocker-db.${deployStage}`;
};
