<?php
/**
 * Created by PhpStorm.
 * User: Thinh. Le Phu
 * Date: 23/03/2016
 * Time: 1:03 PM
 */

namespace Bcore\Models;


use Cassandra;
use Cassandra\BatchStatement;
use Cassandra\ExecutionOptions;
use Cassandra\Uuid;

class LikeByOwner
{

	public $owner_id;

	public $comment_id;

	public $article_id;

	public $user_id;

	public $username;

	public $user_full_name;

	/**
	 * LikeByOwner constructor.
	 *
	 * @param $session
	 */
	public function __construct($session)
	{
		$this->_session = $session;
		$this->_batch   = new BatchStatement(Cassandra::BATCH_LOGGED);
	}

	public function assign($data)
	{

		$this->owner_id       = $data['owner_id'];
		$this->article_id     = $data['article_id'];
		$this->comment_id     = $data['comment_id'];
		$this->user_id        = $data['user_id'];
		$this->username       = $data['username'];
		$this->user_full_name = $data['user_full_name'];
	}

	public function insert()
	{
		$prepareStatement = $this->_session->prepare(
			"INSERT INTO like_by_owner (owner_id, article_id, comment_id, user_id, username, user_full_name)
			VALUES (:owner_id, :article_id, :comment_id, :user_id, :username, :user_full_name)"
		);

		$this->_batch->add($prepareStatement, [
			'owner_id'       => intval($this->owner_id),
			'article_id'     => intval($this->article_id),
			'comment_id'     => new Uuid($this->comment_id),
			'user_id'        => intval($this->user_id),
			'username'       => $this->username,
			'user_full_name' => $this->user_full_name,
		]);

		$this->_session->execute($this->_batch);
	}

	public static function findByOwner($session, $owner_id)
	{
		$prepareStatement = $session->prepare(
			"SELECT * FROM like_by_owner WHERE owner_id = :owner_id"
		);

		$result = $session->execute($prepareStatement, new ExecutionOptions([
			'arguments' => [
				'owner_id' => $owner_id,
			],
		]));

		return $result;
	}

	public function delete()
	{
		$prepareStatement = $this->_session->prepare(
			"DELETE FROM  like_by_owner
		  	WHERE article_id = :article_id AND comment_id = :comment_id AND owner_id = :owner_id"
		);

		$this->_batch->add($prepareStatement, [
			'article_id' => intval($this->article_id),
			'comment_id' => new Uuid($this->comment_id),
			'owner_id'   => intval($this->owner_id),
		]);

		$this->_session->execute($this->_batch);
	}
}