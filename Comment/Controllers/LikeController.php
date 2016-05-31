<?php
/**
 * Created by PhpStorm.
 * User: Thinh. Le Phu
 * Date: 23/03/2016
 * Time: 3:43 PM
 */

namespace Bcore\Comment\Controllers;

use Bcore\Models\LikeByArticle;
use Bcore\Models\LikeByOwner;

class LikeController extends ControllerBase
{
	public function likeAction()
	{

		if ($this->request->isPost() && $this->request->getPost('user_name') == $this->authentication->currentUser()['user_name']) {

			$data['user_id']        = $this->authentication->currentUser()['user_id'];
			$data['username']       = $this->authentication->currentUser()['user_name'];
			$data['user_full_name'] = $this->authentication->currentUser()['user_full_name'];
			$data['comment_id']     = $this->request->getPost('comment_id');
			$data['article_id']     = $this->request->getPost('article_id');
			$data['owner_id']       = $this->request->getPost('owner');

			// assign data like by article, get comment liked
			$likeByArticle = new LikeByArticle($this->cassandra);
			$likeByArticle->assign($data);
			// assign data like by Owner, get owner has liked.
			$likeByOwner = new LikeByOwner($this->cassandra);
			$likeByOwner->assign($data);

			// Check user like or not
			$resultQuery = LikeByArticle::checkLiked($this->cassandra, $data['article_id'], $data['comment_id'], $data['user_id']);
			$result      = [];
			foreach ($resultQuery as $item) {
				$result['checkLiked'] = $item['count'] / 1; // bigint convert to int
			}

			if ($result['checkLiked'] == 1) {
				// delete like by article, get comment liked
				$likeByArticle->delete();
				// delete like by owner, get owner has liked.
				$likeByOwner->delete();

				$response = new \Phalcon\Http\Response();
				$response->setContentType('application/json');
				$result = ['error' => true];

				$response->setStatusCode(500, '');
				$response->setJsonContent($result);

				return $response;
			} else {
				// add like by article, get comment liked
				$likeByArticle->insert();
				// add like by Owner, get owner has liked.
				$likeByOwner->insert();

				$response = new \Phalcon\Http\Response();
				$response->setContentType('application/json');
				$result = ['success' => true];

				$response->setStatusCode(200, 'OK');
				$response->setJsonContent($result);

				return $response;
			}
		}

	}

	public function countLikeByCommentAction() {

	}


}