<?php
/**
 * Created by PhpStorm.
 * User: Thinh. Le Phu
 * Date: 23/03/2016
 * Time: 1:02 PM
 */

namespace Bcore\Models;


use Cassandra;
use Cassandra\BatchStatement;
use Cassandra\ExecutionOptions;
use Cassandra\Uuid;

class LikeByArticle
{

	public $article_id;

	public $comment_id;

	public $user_id;

	public $username;

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
		$this->user_full_name = $data['user_full_name'];
	}

	public function insert()
	{
		$prepareStatement = $this->_session->prepare(
			"INSERT INTO like_by_article (article_id, comment_id, user_id, username, user_full_name)
			VALUES (:article_id, :comment_id, :user_id, :username, :user_full_name)"
		);

		$this->_batch->add($prepareStatement, [
			'article_id'     => intval($this->article_id),
			'comment_id'     => new Uuid($this->comment_id),
			'user_id'        => intval($this->user_id),
			'username'       => $this->username,
			'user_full_name' => $this->user_full_name,
		]);

		$this->_session->execute($this->_batch);
	}

	public static function findByOwner($session, $article_id)
	{
		$prepareStatement = $session->prepare(
			"SELECT * FROM like_by_article WHERE article_id = :article_id"
		);

		$result = $session->execute($prepareStatement, new ExecutionOptions([
			'arguments' => [
				'article_id' => $article_id,
			],
		]));

		return $result;
	}

	public static function findFirst($session, $article_id, $comment_id, $user_id)
	{
		$prepareStatement = $session->prepare(
			"SELECT JSON article_id, comment_id, user_id, username, user_full_name FROM like_by_article WHERE article_id = :article_id AND comment_id = :comment_id AND user_id = :user_id"
		);

		$result = $session->execute($prepareStatement, new ExecutionOptions([
			'arguments' => [
				'article_id' => intval($article_id),
				'comment_id' => new Uuid($comment_id),
				'user_id'    => intval($user_id),
			],
		]));

		return $result;
	}

	public static function checkLiked($session, $article_id, $comment_id, $user_id)
	{
		$prepareStatement = $session->prepare(
			"SELECT count(*) FROM like_by_article WHERE article_id = :article_id AND comment_id = :comment_id AND user_id = :user_id"
		);

		$result = $session->execute($prepareStatement, new ExecutionOptions([
			'arguments' => [
				'article_id' => intval($article_id),
				'comment_id' => new Uuid($comment_id),
				'user_id'    => intval($user_id),
			],
		]));

		return $result;
	}

	public static function countLikeByComment($session, $article_id, $comment_id)
	{
		$prepareStatement = $session->prepare(
			"SELECT count(*) FROM like_by_article WHERE article_id = :article_id AND comment_id = :comment_id"
		);

		$result = $session->execute($prepareStatement, new ExecutionOptions([
			'arguments' => [
				'article_id' => intval($article_id),
				'comment_id' => new Uuid($comment_id),
			],
		]));

		return $result;
	}

	public function delete()
	{
		$prepareStatement = $this->_session->prepare(
			"DELETE FROM  like_by_article
		  	WHERE article_id = :article_id AND comment_id =  :comment_id AND user_id = :user_id"
		);

		$this->_batch->add($prepareStatement, [
			'article_id' => intval($this->article_id),
			'comment_id' => new Uuid($this->comment_id),
			'user_id'    => intval($this->user_id),
		]);

		$this->_session->execute($this->_batch);
	}
}