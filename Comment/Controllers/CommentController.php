<?php
/**
 * Created by PhpStorm.
 * User: Thinh. Le Phu
 * Date: 22/03/2016
 * Time: 10:44 AM
 */

namespace Bcore\Comment\Controllers;


use Bcore\Models\CommentByUser;
use Bcore\Models\CommentNotApprove;
use Bcore\Models\CountReply;
use Bcore\Models\LikeByArticle;
use Bcore\Models\LikeByOwner;
use Bcore\Models\ReplyByComment;
use Bcore\Models\ReplyNotApprove;
use Cassandra;
use Cassandra\BatchStatement;
use Cassandra\Timeuuid;
use Bcore\Models\CommentByArticle;
use Cassandra\Type;


class CommentController extends ControllerBase
{
	public function indexAction()
	{

	}

	public function addCommentAction()
	{
		if ($this->request->isPost()) {

			// Assign data
			$data                = $this->request->getPost();
			$data['comment_id']  = new Timeuuid(time());
			$data['user_avatar'] = $this->request->getPost('user_avatar');


			// get username from session, if null, status = 0, user_id = 0
			if ($this->authentication->currentUser()['user_id'] == 0) {

				$data['username']       = $this->request->getPost('username');
				$data['user_full_name'] = $this->request->getPost('user_full_name');
				$data['user_id']        = $this->request->getPost('user_id');

				// Add a comment of anonymous visitor
				$commentNotApproved = new CommentNotApprove($this->cassandra);
				$commentNotApproved->assign($data);
				$commentNotApproved->insert();

			} else {
				$data['username']       = $this->authentication->currentUser()['user_name'];
				$data['user_full_name'] = $this->authentication->currentUser()['user_full_name'];
				$data['user_id']        = $this->authentication->currentUser()['user_id'];

				// Add comment into table comment_by_article
				$commentByArticle = new CommentByArticle($this->cassandra);
				$commentByArticle->assign($data);
				$commentByArticle->insert();

				// Add comment into table comment_by_user
				$commentByUser = new CommentByUser($this->cassandra);
				$commentByUser->assign($data);
				$commentByUser->insert();
			}

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = [
				'success' => true,
				'comment_id' => $data['comment_id']
			];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;
		}

	}

	public function editCommentAction()
	{

		//$result   = CommentByArticle::findFirstByArticle($this->cassandra, $comment_id, $user_id, $article_id, $status);
		//$username = 'thinhlp';
		// get username from session. check user_id, if == then edit, else not allow edit
		if ($this->request->isPost() &&
			($this->request->getPost('owner_cmt') == $this->authentication->currentUser()['user_id']
				|| in_array('admin', $this->authentication->currentUser()['role'], true))
		) {

			$data['comment_id']     = $this->request->getPost('comment_id');
			$data['article_id']     = $this->request->getPost('article_id');
			$data['comment_text']   = $this->request->getPost('comment_text');
			$data['user_avatar']    = $this->request->getPost('user_avatar');
			$data['user_id']        = $this->authentication->currentUser()['user_id']; //get user_id from session.
			$data['username']       = $this->authentication->currentUser()['user_name'];
			$data['user_full_name'] = $this->authentication->currentUser()['user_full_name'];

			// edit comment by article
			$commentByArticle = new CommentByArticle($this->cassandra);
			$commentByArticle->assign($data);
			$commentByArticle->update();

			// edit comment by user
			$commentByUser = new CommentByUser($this->cassandra);
			$commentByUser->assign($data);
			$commentByUser->update();

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;
		} else {

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'You are not allow edit this comment';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}

	}

	public function deleteCommentAction()
	{
		// check owner this comment to allow delete comment
		if ($this->request->isPost() &&
			($this->request->getPost('owner_cmt') == $this->authentication->currentUser()['user_id']
				|| in_array('admin', $this->authentication->currentUser()['role'], true))
		) {

			$data['comment_id']   = $this->request->getPost('comment_id');
			$data['article_id']   = $this->request->getPost('article_id');
			$data['comment_text'] = $this->request->getPost('comment_text');
			$data['user_avatar']  = $this->request->getPost('user_avatar');


			$data['parent_id']  = $this->request->getPost('comment_id');
			$data['reply_text'] = $this->request->getPost('comment_text');
			$data['reply_id']   = 0;

			$data['user_id']        = $this->authentication->currentUser()['user_id'];
			$data['username']       = $this->authentication->currentUser()['user_name'];
			$data['user_full_name'] = $this->authentication->currentUser()['user_full_name'];

			// Delete comment in reply_by_comment
			$deleteReplyByComment = new ReplyByComment($this->cassandra);
			$deleteReplyByComment->assign($data);
			$deleteReplyByComment->deleteByComment();

			// Delete comment by user
			$deleteCommentByUser = new CommentByUser($this->cassandra);
			$deleteCommentByUser->assign($data);
			$deleteCommentByUser->delete();

			// Delete comment by article
			$deleteCommentByArticle = new CommentByArticle($this->cassandra);
			$deleteCommentByArticle->assign($data);
			$deleteCommentByArticle->delete();

			// Delete comment not approve
			$deleteCommentNotApprove = new CommentNotApprove($this->cassandra);
			$deleteCommentNotApprove->assign($data);
			$deleteCommentNotApprove->delete();

			// Delete comment_id in table like_by_article
			$deleteLikeByArticle = new LikeByArticle($this->cassandra);
			$deleteLikeByArticle->assign($data);
			$deleteLikeByArticle->delete();

			// Delete comment_id in table like_by_owner
			$data['owner_id']  = $this->request->getPost('owner_cmt');
			$deleteLikeByOwner = new LikeByOwner($this->cassandra);
			$deleteLikeByOwner->assign($data);
			$deleteLikeByOwner->delete();

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;

		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'You are not allow delete this comment';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}

	}

	// Load comment in article
	public function viewArticleAction($article_id)
	{
		$user_id        = $this->authentication->currentUser()['user_id'];
		$user_name      = $this->authentication->currentUser()['user_name'];
		$user_full_name = $this->authentication->currentUser()['user_full_name'];

		$result = CommentByArticle::findArticleId($this->cassandra, $article_id);

		$this->view->setVars([
			'article_id'     => $article_id,
			'result'         => $result,
			'user_id'        => $user_id,
			'user_name'      => $user_name,
			'user_full_name' => $user_full_name,
		]);
	}

	public function counterReplyByComment($article_id, $comment_id)
	{

		$counts = CountReply::findByComment($this->cassandra, $article_id, $comment_id);

		return $counts;
	}


	// Reply
	public function replyAction()
	{
		if ($this->request->isPost()) {

			// Assign data
			$data['reply_id']    = new Timeuuid(time());
			$data['reply_text']  = $this->request->getPost('reply_text');
			$data['parent_id']   = $this->request->getPost('comment_id');
			$data['article_id']  = $this->request->getPost('article_id');
			$data['user_avatar'] = $this->request->getPost('user_avatar');

			$data['comment_id']   = $data['reply_id'];  // data save in comment_by_user
			$data['comment_text'] = $this->request->getPost('reply_text'); // data save in comment_by_user

			// get username from session, if null, status = 0, user_id = 0
			if ($this->authentication->currentUser()['user_id'] == 0) {

				$data['username']       = $this->request->getPost('username');
				$data['user_full_name'] = $this->request->getPost('user_full_name');
				$data['user_id']        = $this->request->getPost('user_id');

				// Add a comment of anonymous visitor
				$replyNotApproved = new ReplyNotApprove($this->cassandra);
				$replyNotApproved->assign($data);
				$replyNotApproved->insert();
			} else {

				$data['username']       = $this->authentication->currentUser()['user_name'];
				$data['user_full_name'] = $this->authentication->currentUser()['user_full_name'];
				$data['user_id']        = $this->authentication->currentUser()['user_id'];

				// Add reply into reply_by_comment
				$reply = new ReplyByComment($this->cassandra);
				$reply->assign($data);
				$reply->insert();

				// Add comment into table comment_by_user
				$commentByUser = new CommentByUser($this->cassandra);
				$commentByUser->assign($data);
				$commentByUser->insert();
			}

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = [
				'success' => true,
				'reply_id' => $data['reply_id']
			];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;
		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'You are not allow reply this comment';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}
	}

	public function editReplyAction()
	{

		if ($this->request->isPost() &&
			($this->request->getPost('owner_rep') == $this->authentication->currentUser()['user_id']
				|| in_array('admin', $this->authentication->currentUser()['role'], true))
		) {

			// Check user owner this comment, true: allow edit, false: not allow edit.
			$data['user_full_name'] = $this->authentication->currentUser()['user_full_name'];
			$data['username']       = $this->authentication->currentUser()['user_name'];
			$data['user_id']        = $this->authentication->currentUser()['user_id'];
			$data['reply_text']     = $this->request->getPost('reply_text');
			$data['article_id']     = $this->request->getPost('article_id');
			$data['reply_id']       = $this->request->getPost('reply_id');
			$data['parent_id']      = $this->request->getPost('parent_id');
			$data['user_avatar']    = $this->request->getPost('user_avatar');

			$data['comment_id']   = $this->request->getPost('reply_id');  // data save in comment_by_user
			$data['comment_text'] = $this->request->getPost('reply_text'); // data save in comment_by_user

			// Edit reply
			$editReply = new ReplyByComment($this->cassandra);
			$editReply->assign($data);
			$editReply->update();

			// Update comment into table comment_by_user
			$commentByUser = new CommentByUser($this->cassandra);
			$commentByUser->assign($data);
			$commentByUser->update();

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;
		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'You are not allow edit this reply';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}
	}

	public function deleteReplyAction()
	{
		if ($this->request->isPost() &&
			($this->request->getPost('owner_rep') == $this->authentication->currentUser()['user_id']
				|| in_array('admin', $this->authentication->currentUser()['role'], true))
		) {
			// Check user owner this comment, true: allow delete, false: not allow delete.

			$data['username']       = $this->authentication->currentUser()['user_name'];
			$data['user_full_name'] = $this->authentication->currentUser()['user_full_name'];
			$data['user_id']        = $this->authentication->currentUser()['user_id'];
			$data['article_id']     = $this->request->getPost('article_id');
			$data['reply_id']       = $this->request->getPost('reply_id');
			$data['reply_text']     = $this->request->getPost('reply_text');
			$data['parent_id']      = $this->request->getPost('comment_id');
			$data['user_avatar']    = $this->request->getPost('user_avatar');

			// Data for delete in table comment_by_user
			$data['comment_id']   = $this->request->getPost('reply_id');
			$data['comment_text'] = $this->request->getPost('reply_text');

			// Delete reply
			$deleteReply = new ReplyByComment($this->cassandra);
			$deleteReply->assign($data);
			$deleteReply->delete();

			// Delete reply into table comment_by_user
			$commentByUser = new CommentByUser($this->cassandra);
			$commentByUser->assign($data);
			$commentByUser->delete();

			// Delete reply in table like_by_article
			$deleteLikeByArticle = new LikeByArticle($this->cassandra);
			$deleteLikeByArticle->assign($data);
			$deleteLikeByArticle->delete();

			// Delete reply in table like_by_owner
			$data['owner_id']  = $this->request->getPost('owner_rep');
			$deleteLikeByOwner = new LikeByOwner($this->cassandra);
			$deleteLikeByOwner->assign($data);
			$deleteLikeByOwner->delete();

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;
		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'You are not allow delete this reply';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}

	}

	public function viewCommentByArticleAction()
	{
		if ($this->request->isPost()) {

			$article_id = $this->request->getPost('article_id');

			$listComment = CommentByArticle::findCommentByArticle($this->cassandra, $article_id);

			$temp = [];
			foreach ($listComment as $row) {
				$_temp = json_decode($row['[json]']);
				$countLikeByComment = LikeByArticle::countLikeByComment($this->cassandra, $article_id, $_temp->comment_id);
				foreach ($countLikeByComment as $like) {
					$_temp->checkLike = $like['count'] / 1;
				}
				array_push($temp, $_temp);
			}

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			//$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($temp);

			return $response;
		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'Have no comment';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}
	}

	public function viewReplyByCommentAction()
	{

		if ($this->request->isPost()) {

			$article_id = $this->request->getPost('article_id');
			$parent_id  = $this->request->getPost('parent_id');

			$result = ReplyByComment::findByOwner($this->cassandra, $article_id, $parent_id);
			// $this->view->result = $result;

			$temp = [];
			foreach ($result as $row) {
				array_push($temp, json_decode($row['[json]']));
			}

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			//$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($temp);

			return $response;
		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'Have no reply';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}

	}


	// Manager comment in backend module
	public function approvedAction($page = 1)
	{
		// Add js in backend
		$this->assets->collection('backendJsFooter')->addJs('backend/js/comment_manager.js');

		$user_id        = $this->authentication->currentUser()['user_id'];
		$user_name      = $this->authentication->currentUser()['user_name'];
		$user_full_name = $this->authentication->currentUser()['user_full_name'];

		// Get comment approved
		$rs_cmt_approved = CommentByArticle::find($this->cassandra);

		// Count total comment in table comment_by_article
		$count              = CommentByArticle::count($this->cassandra);
		$pages_cmt_approved = ceil($count / 20);

		if ($page > 1) {

			$i = 2;
			while ($i <= $page) {
				$rs_cmt_approved = $rs_cmt_approved->nextPage();

				if ($rs_cmt_approved->isLastPage()) {
					break;
				}
				$i++;
			}
		}

		$this->view->setVars([
			'cmt_approved'       => $rs_cmt_approved,
			'pages_cmt_approved' => $pages_cmt_approved,
			'user_id'            => $user_id,
			'user_name'          => $user_name,
			'user_full_name'     => $user_full_name,
		]);
	}

	public function notApprovedAction($page = 1)
	{
		// Add js in backend
		$this->assets->collection('backendJsFooter')->addJs('backend/js/comment_manager.js');

		$user_id        = $this->authentication->currentUser()['user_id'];
		$user_name      = $this->authentication->currentUser()['user_name'];
		$user_full_name = $this->authentication->currentUser()['user_full_name'];

		// Get comment not approved
		$rs_cmt_not_approved = CommentNotApprove::find($this->cassandra);

		// Count total comment not approved in table comment_not_approved
		$count                  = CommentNotApprove::count($this->cassandra);
		$pages_cmt_not_approved = ceil($count / 20);

		if ($page > 1) {

			$i = 2;
			while ($i <= $page) {
				$rs_cmt_not_approved = $rs_cmt_not_approved->nextPage();

				if ($rs_cmt_not_approved->isLastPage()) {
					break;
				}
				$i++;
			}
		}

		$this->view->setVars([
			'cmt_not_approved'       => $rs_cmt_not_approved,
			'pages_cmt_not_approved' => $pages_cmt_not_approved,
			'user_id'                => $user_id,
			'user_name'              => $user_name,
			'user_full_name'         => $user_full_name,
		]);
	}

	public function deActiveCmtAction()
	{

		if ($this->request->isPost()) {

			$data['article_id']     = $this->request->getPost('article_id');
			$data['comment_id']     = $this->request->getPost('comment_id');
			$data['comment_text']   = $this->request->getPost('comment_text');
			$data['user_id']        = $this->request->getPost('user_id');
			$data['username']       = $this->request->getPost('user_name');
			$data['user_avatar']    = $this->request->getPost('user_avatar');
			$data['user_full_name'] = $this->request->getPost('user_full_name');

			// Delete comment active in table comment_by_article
			$deActiveCmt = new CommentByArticle($this->cassandra);
			$deActiveCmt->assign($data);
			$deActiveCmt->delete();

			// Add comment deleted above into table comment_not_approve
			$cmtNotApprove = new CommentNotApprove($this->cassandra);
			$cmtNotApprove->assign($data);
			$cmtNotApprove->insert();

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;

		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'Something wrong.';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}
	}

	public function activeCmtAction()
	{

		if ($this->request->isPost()) {

			$data['article_id']     = $this->request->getPost('article_id');
			$data['comment_id']     = $this->request->getPost('comment_id');
			$data['comment_text']   = $this->request->getPost('comment_text');
			$data['user_id']        = $this->request->getPost('user_id');
			$data['username']       = $this->request->getPost('user_name');
			$data['user_full_name'] = $this->request->getPost('user_full_name');
			$data['user_avatar']    = $this->request->getPost('user_avatar');

			// Delete comment suspended in table comment_not_approve
			$cmtNotApprove = new CommentNotApprove($this->cassandra);
			$cmtNotApprove->assign($data);
			$cmtNotApprove->delete();

			// Add comment deleted above into table comment_by_article
			$activeCmt = new CommentByArticle($this->cassandra);
			$activeCmt->assign($data);
			$activeCmt->insert();

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;

		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'Something wrong.';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}
	}

	public function deActiveRepAction()
	{

		if ($this->request->isPost()) {

			$data['article_id']     = $this->request->getPost('article_id');
			$data['reply_id']       = $this->request->getPost('reply_id');
			$data['reply_text']     = $this->request->getPost('reply_text');
			$data['user_id']        = $this->request->getPost('user_id');
			$data['username']       = $this->request->getPost('user_name');
			$data['user_full_name'] = $this->request->getPost('user_full_name');
			$data['parent_id']      = $this->request->getPost('parent_id');
			$data['user_avatar']    = $this->request->getPost('user_avatar');

			// Delete reply active in table reply_by_comment
			$deActiveRep = new ReplyByComment($this->cassandra);
			$deActiveRep->assign($data);
			$deActiveRep->delete();

			// Add comment deleted above into table comment_not_approve
			$repNotApprove = new ReplyNotApprove($this->cassandra);
			$repNotApprove->assign($data);
			$repNotApprove->insert();

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;

		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'Something wrong.';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}
	}

	public function activeRepAction()
	{

		if ($this->request->isPost()) {

			$data['article_id']     = $this->request->getPost('article_id');
			$data['reply_id']       = $this->request->getPost('reply_id');
			$data['reply_text']     = $this->request->getPost('reply_text');
			$data['user_id']        = $this->request->getPost('user_id');
			$data['username']       = $this->request->getPost('user_name');
			$data['user_full_name'] = $this->request->getPost('user_full_name');
			$data['parent_id']      = $this->request->getPost('parent_id');
			$data['user_avatar']    = $this->request->getPost('user_avatar');

			// Delete reply suspended in table reply_not_approve
			$repNotApprove = new ReplyNotApprove($this->cassandra);
			$repNotApprove->assign($data);
			$repNotApprove->delete();

			// Add reply deleted above into table reply_by_comment
			$activeRep = new ReplyByComment($this->cassandra);
			$activeRep->assign($data);
			$activeRep->insert();

			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = ['success' => true];

			$response->setStatusCode(200, 'OK');
			$response->setJsonContent($result);

			return $response;

		} else {
			$response = new \Phalcon\Http\Response();
			$response->setContentType('application/json');
			$result = 'Something wrong.';

			$response->setStatusCode(500, 'NOT OK');
			$response->setJsonContent($result);

			return $response;
		}
	}

	// Test performance cassandra
	public function viewMoreAction($next_page = 0)
	{
		// Get comment approved
		$results = CommentByArticle::find($this->cassandra);

		if ($next_page != 0) {

			$i = 1;
			while ($i <= $next_page) {
				$results = $results->nextPage();
				$i++;
			}
		}

		$this->view->setVars([
			'results' => $results,
		]);

//		$second = $cmt_approved->nextPage();
//		echo "Page 2"; echo '<br>';
//		foreach ($second as $second_row) {
//			echo 'comment_id: ' . $second_row['comment_id']; echo '<br>';
//			echo 'user_id: ' . $second_row['user_id']; echo '<br>';
//			echo 'user_full_name: ' . $second_row['user_full_name']; echo '<br>';
//			echo 'comment_text: ' . $second_row['comment_text']; echo '<br>';
//		}
//
//		$third = $cmt_approved->nextPage()->nextPage();
//		echo "Page 3"; echo '<br>';
//		foreach ($third as $third_row) {
//			echo 'comment_id: ' . $third_row['comment_id']; echo '<br>';
//			echo 'user_id: ' . $third_row['user_id']; echo '<br>';
//			echo 'user_full_name: ' . $third_row['user_full_name']; echo '<br>';
//			echo 'comment_text: ' . $third_row['comment_text']; echo '<br>';
//		}
//		exit();

	}

}