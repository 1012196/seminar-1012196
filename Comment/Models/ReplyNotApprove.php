<?php
/**
 * Created by PhpStorm.
 * User: Thinh. Le Phu
 * Date: 02/04/2016
 * Time: 11:51 AM
 */

namespace Bcore\Models;


use Cassandra;
use Cassandra\BatchStatement;
use Cassandra\ExecutionOptions;
use Cassandra\Uuid;

class ReplyNotApprove
{
	public $article_id;

	public $parent_id;

	public $reply_id;

	public $reply_text;

	public $username;

	public $user_id;

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
		$this->parent_id      = $data['parent_id'];
		$this->reply_id       = $data['reply_id'];
		$this->reply_text     = $data['reply_text'];
		$this->username       = $data['username'];
		$this->user_id        = $data['user_id'];
		$this->user_avatar    = $data['user_avatar'];
		$this->user_full_name = $data['user_full_name'];
	}

	public function insert()
	{

		$prepareStatement = $this->_session->prepare(
			"INSERT INTO reply_not_approve (article_id, parent_id, reply_id, reply_text, username, user_id, user_avatar, user_full_name)
			VALUES (:article_id, :parent_id, :reply_id, :reply_text, :username, :user_id, :user_avatar, :user_full_name)"
		);

		$this->_batch->add($prepareStatement, [
			'article_id'     => intval($this->article_id),
			'parent_id'      => new Uuid($this->parent_id),
			'reply_id'       => new Uuid($this->reply_id),
			'reply_text'     => $this->reply_text,
			'username'       => $this->username,
			'user_avatar'    => $this->user_avatar,
			'user_id'        => intval($this->user_id),
			'user_full_name' => $this->user_full_name,
		]);

		$this->_session->execute($this->_batch);
	}

	public static function findByOwner($session, $article_id, $parent_id)
	{
		$prepareStatement = $session->prepare(
			"SELECT JSON article_id, parent_id, reply_id, unixTimestampOf(reply_id) AS datetime_reply, reply_text, username, user_id, user_avatar, user_full_name FROM reply_not_approve WHERE article_id = :article_id AND parent_id = :parent_id ORDER BY parent_id DESC"
		);

		$result = $session->execute($prepareStatement, new ExecutionOptions([
			'arguments' => [
				'article_id' => intval($article_id),
				'parent_id'  => new Uuid($parent_id),
			],
		]));

		return $result;
	}

	public static function find($session)
	{
		$prepareStatement = $session->prepare(
			"SELECT article_id, parent_id, reply_id, unixTimestampOf(reply_id) AS datetime_reply, reply_text, username, user_id, user_avatar, user_full_name FROM reply_not_approve"
		);

		$result = $session->execute($prepareStatement);

		return $result;
	}

	public static function findFirst($session, $article_id, $parent_id, $reply_id)
	{
		$prepareStatement = $session->prepare(
			"SELECT * FROM reply_not_approve WHERE article_id = :article_id AND parent_id = :parent_id AND reply_id = :reply_id"
		);

		$result = $session->execute($prepareStatement, new ExecutionOptions([
			'arguments' => [
				'article_id' => intval($article_id),
				'parent_id'  => new Uuid($parent_id),
				'reply_id'   => new Uuid($reply_id),
			],
		]));

		return $result;
	}

	public function delete()
	{
		$prepareStatement = $this->_session->prepare(
			"DELETE FROM reply_not_approve WHERE article_id = :article_id AND reply_id = :reply_id AND parent_id = :parent_id AND user_id = :user_id"
		);

		$this->_batch->add($prepareStatement, [
			'article_id' => intval($this->article_id),
			'user_id'    => intval($this->user_id),
			'reply_id'   => new Uuid($this->reply_id),
			'parent_id'  => new Uuid($this->parent_id),
		]);

		$this->_session->execute($this->_batch);
	}

	public function update()
	{
		$prepareStatement = $this->_session->prepare(
			"UPDATE reply_not_approve SET reply_text = :reply_text
 			WHERE article_id = :article_id AND reply_id = :reply_id AND parent_id = :parent_id AND user_id = :user_id"
		);

		$this->_batch->add($prepareStatement, [
			'reply_text' => $this->reply_text,
			'article_id' => intval($this->article_id),
			'user_id'    => intval($this->user_id),
			'reply_id'   => new Uuid($this->reply_id),
			'parent_id'  => new Uuid($this->parent_id),
		]);

		$this->_session->execute($this->_batch);
	}
}