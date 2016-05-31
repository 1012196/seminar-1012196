<?php
/**
 * Created by PhpStorm.
 * User: Thinh. Le Phu
 * Date: 23/03/2016
 * Time: 11:53 AM
 */

namespace Bcore\Models;


use Cassandra;
use Cassandra\BatchStatement;
use Cassandra\ExecutionOptions;
use Cassandra\Uuid;

class CommentByUser
{

	public $user_id;

	public $comment_id;

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

		$this->user_id        = $data['user_id'];
		$this->comment_id     = $data['comment_id'];
		$this->username       = $data['username'];
		$this->comment_text   = $data['comment_text'];
		$this->user_avatar    = $data['user_avatar'];
		$this->user_full_name = $data['user_full_name'];
	}

	public function insert()
	{

		$addStatement = $this->_session->prepare(
			"INSERT INTO comment_by_user (user_id, comment_id, username, comment_text, user_avatar, user_full_name)
			VALUES (:user_id, :comment_id, :username, :comment_text, :user_avatar, :user_full_name)");

		$this->_batch->add($addStatement, [
			'user_id'        => intval($this->user_id),
			'comment_id'     => new Uuid($this->comment_id),
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
			"UPDATE comment_by_user SET comment_text = :comment_text WHERE comment_id = :comment_id AND user_id = :user_id"
		);

		$this->_batch->add($updateStatement, [
			'comment_text' => $this->comment_text,
			'comment_id'   => new Uuid($this->comment_id),
			'user_id'      => intval($this->user_id),
		]);
		$this->_session->execute($this->_batch);
	}

	public function delete()
	{
		$deleteStatement = $this->_session->prepare(
			"DELETE FROM comment_by_user WHERE comment_id = :comment_id AND user_id = :user_id"
		);

		$this->_batch->add($deleteStatement, [
			'comment_id' => new Uuid($this->comment_id),
			'user_id'    => intval($this->user_id),
		]);
		$this->_session->execute($this->_batch);
	}

	public static function findByUser($session, $user_id)
	{

		$findStatement = $session->prepare(
			"SELECT * FROM comment_by_user WHERE user_id = :user_id"
		);
		$result        = $session->execute($findStatement, new ExecutionOptions([
			'arguments' => [
				intval($user_id),
			],
		]));

		return $result;
	}

	public static function findFirstByUser($session, $comment_id, $user_id)
	{
		$findFirstStatement = $session->prepare(
			"SELECT * FROM comment_by_user WHERE comment_id = :comment_id AND user_id = :user_id"
		);
		$result             = $session->execute($findFirstStatement, new ExecutionOptions([
			'arguments' => [
				'comment_id' => new Uuid($comment_id),
				'user_id'    => intval($user_id),
			],
		]));

		return $result;
	}
}