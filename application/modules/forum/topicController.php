<?php


/**
 * topicController
 *
 * Topic controller of forum module
 */

namespace modules\forum;

class topicController extends \BaseController
{


    /**
     * indexAction
     *
     * Index action of topic controller forum module
     *
     * @return null
     */

    public function indexAction()
    {
        \View::addLanguageItem('forumTopicController');

        // validate request params
        $gtForm = \App::getInstance('\modules\forum\GetTopicForm');
        $gtForm->validate();
        // invalid request params
        if (!$gtForm->isValid()) {
            throw new \SystemErrorException(array(
                'title'       => \View::$language->forum_topic_error,
                'description' => \View::$language->forum_topic_request_invalid
            ));
        }
        // get request data
        $gtData = $gtForm->getData();

        // get topic
        $topic = TopicHelper::getTopicById($gtData->id);
        if (!$topic) {
            throw new \MemberErrorException(array(
                'code'        => 404,
                'title'       => \View::$language->forum_topic_error,
                'description' => \View::$language->forum_topic_topic_not_found
            ));
        }

        // get topic posts
        $posts = TopicPostsHelper::getPostsByTopicId(
            $gtData->id,
            $gtData->page,
            10 // TODO posts per page from forum(default)/member(custom) settings
        );
        if (!$posts) {
            $description = $gtData->page == 1
                ? \View::$language->forum_topic_first_post_not_found
                : \View::$language->forum_topic_page_not_found;
            throw new \SystemErrorException(array(
                'title'       => \View::$language->forum_topic_error,
                'description' => $description
            ));
        }

        // append breadcrumbs
        \common\BreadCrumbs::appendItems(
            array(
                // add forum item
                new \common\BreadCrumbsItem(
                    '/forum?id=' . $topic->forum_id,
                    $topic->forum_title
                ),
                // add subforum item
                new \common\BreadCrumbsItem(
                    '/forum/sub-forum?id=' . $topic->subforum_id,
                    $topic->subforum_title
                ),
                // add topic (current) item
                new \common\BreadCrumbsItem(null, $topic->topic_title)
            )
        );

        // assign data into view
        \View::assign('posts', $posts);
        // set output layout
        \View::setLayout('forum-topic.phtml');
    }
}
