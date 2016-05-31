<?php
/**
 * Created by PhpStorm.
 * User: Thinh. Le Phu
 * Date: 01/04/2016
 * Time: 5:05 PM
 */

namespace Bcore\Models;


use Cassandra;
use Cassandra\BatchStatement;
use Cassandra\ExecutionOptions;
use Cassandra\Uuid;

class CommentNotApprove
{
	public $article_id;

	public $comment_id;

	public $user_id;

	public $username;

	public $comment_text;

	public $user_avatar;

	public $user_full_name;

	public function __construct($session)
	{
		$this->_session = $session;
		$this->_batch   = new BatchStatement(Cassandra::BATCH_LOGGED);
	}

	public function assign($data)
	{
		$this->article_id     = $data['article_id'];
		$this->comment_id     = $data['comment_id'];
		$this->user_id        = $data['user_id'];
		$this->username       = $data['username'];
		$this->comment_text   = $data['comment_text'];
		$this->user_avatar    = $data['user_avatar'];
		$this->user_full_name = $data['user_full_name'];
	}

	public function insert()
	{

		$addStatement = $this->_session->prepare(
			"INSERT INTO comment_not_approve (article_id, comment_id, user_id, username, comment_text, user_avatar, user_full_name)
			VALUES (:article_id, :comment_id, :user_id, :username, :comment_text, :user_avatar, :user_full_name)");

		$this->_batch->add($addStatement, [
			'article_id'     => intval($this->article_id),
			'comment_id'     => new Uuid($this->comment_id),
			'user_id'        => intval($this->user_id),
			'username'       => $this->username,
			'comment_text'   => $this->comment_text,
			'user_avatar'    => $this->user_avatar,
			'user_full_name' => $this->user_full_name,
		]);
		$this->_session->execute($this->_batch);

	}

	public function update()
	{
		$updateStatement = $this->_session->prepare(
			"UPDATE comment_not_approve SET comment_text = :comment_text
			WHERE comment_id = :comment_id AND user_id = :user_id AND article_id = :article_id"
		);

		$this->_batch->add($updateStatement, [
			'comment_text' => $this->comment_text,
			'comment_id'   => new Uuid($this->comment_id),
			'user_id'      => intval($this->user_id),
			'article_id'   => intval($this->article_id),
		]);
		$this->_session->execute($this->_batch);
	}

	public function delete()
	{
		$deleteStatement = $this->_session->prepare(
			"DELETE FROM comment_not_approve
			WHERE comment_id = :comment_id AND article_id = :article_id"
		);

		$this->_batch->add($deleteStatement, [
			'comment_id' => new Uuid($this->comment_id),
			'article_id' => intval($this->article_id),
		]);
		$this->_session->execute($this->_batch);
	}

	public static function findArticleId($session, $article_id)
	{
		$findStatement = $session->prepare(
			"SELECT article_id, unixTimestampOf(comment_id) as datetime_cmt, comment_id,  user_id, username, comment_text, user_avatar, user_full_name FROM comment_not_approve WHERE article_id = :article_id"
		);
		$result        = $session->execute($findStatement, new ExecutionOptions([
			'arguments' => [
				'article_id' => intval($article_id),
			],
		]));

		return $result;
	}

	public static function find($session)
	{
		$findStatement = $session->prepare(
			"SELECT article_id, unixTimestampOf(comment_id) as datetime_cmt, comment_id,  user_id, username, comment_text, user_avatar, user_full_name FROM comment_not_approve"
		);
		$result        = $session->execute($findStatement, new ExecutionOptions([
			'page_size' => 20,
		]));

		return $result;
	}

	public static function findFirstByArticle($session, $comment_id, $user_id, $article_id)
	{
		$findFirstStatement = $session->prepare(
			"SELECT * FROM comment_not_approve
			WHERE comment_id = :comment_id AND user_id = :user_id AND article_id = :article_id"
		);
		$result             = $session->execute($findFirstStatement, new ExecutionOptions([
			'arguments' => [
				'comment_id' => new Uuid($comment_id),
				'user_id'    => intval($user_id),
				'article_id' => intval($article_id),
			],
		]));

		return $result;
	}

	public static function count($session)
	{
		$countStatement = $session->prepare(
			"SELECT count(*) FROM comment_not_approve"
		);
		$result         = $session->execute($countStatement);
		$count          = '';
		foreach ($result as $item) {
			$count = $item['count'] / 1;
		}

		return $count;
	}
}