<?php
/**
 * Created by PhpStorm.
 * User: thaolang0204
 * Date: 03/04/2016
 * Time: 5:09 PM
 */

namespace Bcore\Helper;


use Cassandra\ExecutionOptions;
use Cassandra\Uuid;
use Phalcon\Di;

class Cassandra
{
	public static function counterLikeByComment($article_id, $comment_id)
	{
		$di        = Di::getDefault();
		$cassandra = $di->getShared('cassandra');

		$counterStatement = $cassandra->prepare("
		SELECT count(*) FROM like_by_article WHERE comment_id = :comment_id AND article_id = :article_id");

		$resultQuery = $cassandra->execute($counterStatement, new ExecutionOptions([
			'arguments' => [
				'comment_id' => new Uuid($comment_id),
				'article_id' => intval($article_id),
			],
		]));

		$result = [];

		foreach ($resultQuery as $item) {
			$result['countLike'] = $item['count'] / 1;
		}

		return $result;
	}

	public static function counterCommentByArticle($article_id)
	{
		$di        = Di::getDefault();
		$cassandra = $di->getShared('cassandra');

		$counterStatement = $cassandra->prepare("
		SELECT count(*) FROM comment_by_article WHERE article_id = :article_id");

		$resultQuery = $cassandra->execute($counterStatement, new ExecutionOptions([
			'arguments' => [
				'article_id' => intval($article_id),
			],
		]));

		$result = [];

		foreach ($resultQuery as $item) {
			$result['countCmt'] = $item['count'] / 1;
		}

		return $result;
	}

	public static function find2Comment($article_id)
	{
		$di         = Di::getDefault();
		$cassandra  = $di->getShared('cassandra');
		$simpleView = $di->getShared('simpleView');
		$config     = $di->getShared('config');

		$counterStatement = $cassandra->prepare("
		SELECT * FROM comment_by_article WHERE article_id = :article_id ORDER BY comment_id DESC");

		$results = $cassandra->execute($counterStatement, new ExecutionOptions([
			'page_size' => 2,
			'arguments' => [
				'article_id' => intval($article_id),
			],
		]));

		$simpleView->setViewsDir(__DIR__ . '/../Modules/Frontend/Views/');
		return $simpleView->render($config->elements->post_bit_comment, [
			'results' => $results,
		]);
	}
}