<?php
/*
Plugin Name: Comment XMPP notifier
Plugin URI: http://pasero.net/~mako/
Description: A simple comment notifier
Depend: XMPP Enabled
Version: 0.1
Author: Mako N
Author URI: http://pasero.net/~mako/
License: GNU GPL v2
*/
$admin_jabber = 'admin@xmpp.example.net'; // 投稿の作者のJIDがセットされていない場合、サイト管理者のJIDに送る

add_action('comment_post', 'mako_xmpp_comment_post');

function mako_xmpp_comment_post($comment_id)
{
    $include_text = true;

    if(!function_exists('xmpp_send')) // no xmpp sender plugin
    {
        return;
    }

    $comment = & get_comment($comment_id);

    $post_id = $comment->comment_post_ID;
    $post = & get_post($post_id);
    $post_link = get_permalink($post_id);

    $author = get_userdata($post->post_author);
    $author_jabber = $author->jabber; // プロフィールで jabber が登録されていること

    if(empty($author_jabber))
    {
        $author_jabber = $admin_jabber;
    }

    /*
    if($post->post_type != 'post') // no pages or attachments!
    {
        return;
    }
    */

    $message .= $post->post_title . "へのコメント:\n";

    if($include_text)
    {
        $message .= get_comment_excerpt($comment_id). "\n\n";
    }

    $message .= $post_link . '#comment-' . $comment_id;

    xmpp_send($author_jabber, $message);
}
?>
