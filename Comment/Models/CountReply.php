<?php
/**
 * Created by PhpStorm.
 * User: Thinh. Le Phu
 * Date: 26/03/2016
 * Time: 2:11 PM
 */

namespace Bcore\Models;

use Cassandra;
use Cassandra\BatchStatement;
use Cassandra\ExecutionOptions;
use Cassandra\Timeuuid;
use Cassandra\Uuid;

class CountReply
{
	public $article_id;

	public $comment_id;

	public $counter_reply;

	public function __construct($session)
	{
		$this->_session = $session;
		$this->_batch   = new BatchStatement(Cassandra::BATCH_LOGGED);
	}

	public function assign($data)
	{

		$this->article_id    = $data['article_id'];
		$this->comment_id    = $data['comment_id'];
		$this->counter_reply = $data['counter_reply'];
	}

	public function insert()
	{

		$prepareStatement = $this->_session->prepare(
			"INSERT INTO count_reply_by_comment (article_id, comment_id, counter_reply)
			VALUES (:article_id, :comment_id, :counter_reply)"
		);

		$this->_batch->add($prepareStatement, [
			'article_id'    => $this->article_id,
			'comment_id'    => new Uuid($this->comment_id),
			'counter_reply' => $this->counter_reply,
		]);

		$this->_session->execute($this->_batch);
	}

	public static function findByComment($session, $article_id, $comment_id)
	{
		$prepareStatement = $session->prepare(
			"SELECT * FROM count_reply_by_comment WHERE article_id = :article_id AND comment_id = :comment_id"
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
			"DELETE FROM count_reply_by_comment WHERE article_id = :article_id AND comment_id = :comment_id"
		);

		$this->_batch->add($prepareStatement, [
			'article_id' => intval($this->article_id),
			'comment_id' => new Uuid($this->comment_id),
		]);
	}

//	public function update()
//	{
//		$prepareStatement = $this->_session->prepare(
//			"UPDATE reply_by_comment SET counter_reply .= :counter_reply
// 			WHERE article_id = :article_id AND comment_id = :comment_id"
//		);
//
//		$this->_batch->add($prepareStatement, [
//			'article_id' => intval($this->article_id),
//			'comment_id' => new Uuid($this->comment_id),
//		]);
//	}

	public static function increase($session, $article_id, $comment_id, $counter_reply)
	{
		$prepareStatement = $session->prepare(
			"UPDATE reply_by_comment SET counter_reply = counter_reply + :counter_reply
 			WHERE article_id = :article_id AND comment_id = :comment_id"
		);

		$session->execute($prepareStatement, new ExecutionOptions([
			'counter_reply' => intval($counter_reply),
			'article_id'    => $article_id,
			'comment_id'    => new Uuid($comment_id),
		]));
	}

	public static function decrease($session, $article_id, $comment_id, $counter_reply)
	{
		$prepareStatement = $session->prepare(
			"UPDATE reply_by_comment SET counter_reply = counter_reply - :counter_reply
 			WHERE article_id = :article_id AND comment_id = :comment_id"
		);

		$session->execute($prepareStatement, new ExecutionOptions([
			'counter_reply' => intval($counter_reply),
			'article_id'    => $article_id,
			'comment_id'    => new Uuid($comment_id),
		]));
	}
}