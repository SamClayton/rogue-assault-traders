<?php
include("config/config.php");
include ("languages/$langdir/lang_readmail.inc");
include ("languages/$langdir/lang_mailto2.inc");
include("languages/$langdir/lang_teams.inc");
include("languages/$langdir/lang_forums.inc");

$title=$l_forums_titlemain;

if (checklogin()) {
	include ("footer.php");
	die();
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

if ((!isset($swordfish)) || ($swordfish == ''))
{
$swordfish='';
}

if ((!isset($command)) || ($command == ''))
{
$command = 'showtopics';
}

$time = date("Y-m-d H:i:s");

/* Get user info */
$result		= $db->Execute("SELECT $dbtables[players].*, $dbtables[teams].team_name, $dbtables[teams].description, $dbtables[teams].creator, $dbtables[teams].id
						FROM $dbtables[players]
						LEFT JOIN $dbtables[teams] ON $dbtables[players].team = $dbtables[teams].id
						WHERE $dbtables[players].email='$username'");
$playerinfo	= $result->fields;

/*
   Get Team Info
*/
$result_team   = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
$team		  = $result_team->fields;

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

if($playerinfo['team'] != 0){
		$result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
		$whichteam = $result->fields;
		$isowner = ($playerinfo['player_id'] == $whichteam['creator']);

	if($command == "showtopics"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[topics] where forum_id=$forumdata[forum_id] order by topic_status desc,lastpostdate desc");
		db_op_result($debug_query,__LINE__,__FILE__);
		$reccount = $debug_query->RecordCount();

		$smarty->assign("forumname", $forumdata['forum_name']);
		$smarty->assign("l_forums_title", $l_forums_title);
		$smarty->assign("l_forums_topics", $l_forums_topics);
		$smarty->assign("reccount", $reccount);
		$smarty->assign("l_forums_date", $l_forums_date);
		$smarty->assign("showdate", date($local_date_full_format, strtotime($time)));
		$smarty->assign("templatename", $templatename);
		$smarty->assign("istopics", ($debug_query && $reccount > 0));

		if ($debug_query && $reccount > 0){
			$smarty->assign("l_forums_topic2", $l_forums_topic2);
			$smarty->assign("l_forums_author", $l_forums_author);
			$smarty->assign("l_forums_date2", $l_forums_date2);
			$smarty->assign("l_forums_posts", $l_forums_posts);
			$smarty->assign("l_forums_new", $l_forums_new);
			$smarty->assign("l_forums_views", $l_forums_views);
			$count = 0;
			while (!$debug_query->EOF){
				$topicinfo = $debug_query->fields;

				$topictype = "";
				if($topicinfo['topic_status'] == 9)
					$topictype = $l_forums_sticky;

				if($topicinfo['topic_status'] == 0)
					$topictype = $l_forums_locked;

				$query2 = $db->Execute("select * from $dbtables[posts] where topic_id=$topicinfo[topic_id] order by post_time");
				db_op_result($query2,__LINE__,__FILE__);
				$num2 = $query2->RecordCount();

				if($num2 > 0){
					$post_player_id = $query2->fields['post_player_id'];
				}

				$query3 = $db->Execute("select * from $dbtables[fplayers] where player_id='$post_player_id'");
				db_op_result($query3,__LINE__,__FILE__);
				$admins = $query3->RecordCount();

				if($admins > 0) {
					$admin = $query3->fields['admin'];
				}

				$accounttype = "";
				if($admin==1)
					$accounttype = $l_forums_coord;
				if($admin==2)
					$accounttype = $l_forums_admin;

				$query2 = $db->Execute("select * from $dbtables[posts] where topic_id=$topicinfo[topic_id] and post_time>='$forumplayer[lastonline]' order by post_time");
				db_op_result($query2,__LINE__,__FILE__);
				$newposts = $query2->RecordCount();
				$post_id = $query2->fields['post_id'];

				if(!isset($post_id))
					$post_id=0;

				$query2 = $db->Execute("select * from $dbtables[posts] where topic_id=$topicinfo[topic_id] and post_player_id='$forumplayer[player_id]'");
				db_op_result($query2,__LINE__,__FILE__);
				$clientmatch = $query2->RecordCount();

				if($clientmatch != 0)
					$clientmatch = "yellow";
				else $clientmatch = "white";

/*				print "$topicinfo[topic_id]<br>";
				print "$newposts<br>";
				print "$topictype$topicinfo[topic_title]<br>";
				print "$accounttype$topicinfo[topic_poster]<br>";
				print "$topicinfo[topic_time]<br>";
				print "$num2<br>";
				print "$topicinfo[topic_views]<br>";
				print "$topicinfo[topic_status]<br>";
				print "$clientmatch<br><br>";
*/
				$client[$count] = $clientmatch;
				$newpost[$count] = $newposts;
				$topicid[$count] = $topicinfo['topic_id'];
				$postid[$count] = $post_id;
				$topictypes[$count] = $topictype;
				$topictitle[$count] = $topicinfo['topic_title'];
				$accounttypes[$count] = $accounttype;
				$topicposter[$count] = $topicinfo['topic_poster'];
				$topicdates[$count] = date($local_date_full_format, strtotime($topicinfo['topic_time']));
				$number[$count] = $num2;
				$topicviews[$count] = $topicinfo['topic_views'];
				$count++;
				$debug_query->MoveNext();
			}
			$smarty->assign("client", $client);
			$smarty->assign("newpost", $newpost);
			$smarty->assign("topicid", $topicid);
			$smarty->assign("postid", $postid);
			$smarty->assign("topictypes", $topictypes);
			$smarty->assign("topictitle", $topictitle);
			$smarty->assign("accounttypes", $accounttypes);
			$smarty->assign("topicposter", $topicposter);
			$smarty->assign("topicdates", $topicdates);
			$smarty->assign("number", $number);
			$smarty->assign("topicviews", $topicviews);
			$smarty->assign("count", $count);
		}else{
			$smarty->assign("l_readm_nomessage", $l_readm_nomessage);
		}
		$smarty->assign("l_forums_posttopic", $l_forums_posttopic);
		$smarty->assign("l_team_notmember", $l_team_notmember);
		$smarty->assign("l_forums_showtopic", $l_forums_showtopic);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teamforum-showtopics.tpl");
		include ("footer.php");
		die();
	}

	if($command == "posttopic"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		if(trim($l_forums_subject) == '' || !isset($l_forums_subject))
			$l_forums_subject = $l_none;

		$smarty->assign("l_forums_subject", $l_forums_subject);
		$smarty->assign("l_forums_message", $l_forums_message);
		$smarty->assign("l_forums_normaltopic", $l_forums_normaltopic);
		$smarty->assign("l_forums_stickytopic", $l_forums_stickytopic);
		$smarty->assign("isadmin", $forumplayer['admin']);
		$smarty->assign("l_team_notmember", $l_team_notmember);
		$smarty->assign("l_forums_showtopic", $l_forums_showtopic);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teamforum-posttopic.tpl");
		include ("footer.php");
		die();
	}

	if($command == "finishtopic"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$query=$db->Execute("select * from $dbtables[forums] where forum_id=$forumdata[forum_id] and private=1");
		db_op_result($query,__LINE__,__FILE__);
		$private=$query->RecordCount();

		$query=$db->Execute("insert into $dbtables[topics] (topic_title, topic_poster, topic_time, topic_views, topic_replies, forum_id, topic_status, lastpostdate) values ('". clean_words($topictitle) . "', '$playerinfo[character_name]', '$time', 1, 0, $forumdata[forum_id], $sticky, '$time')");
		db_op_result($query,__LINE__,__FILE__);

		$debug_query = $db->Execute("select * from $dbtables[topics] where topic_title='". clean_words($topictitle) . "' and topic_poster='$playerinfo[character_name]' and topic_time='$time' and topic_views=1 and topic_replies=0 and forum_id=$forumdata[forum_id] and topic_status=$sticky and lastpostdate='$time'");
		db_op_result($debug_query,__LINE__,__FILE__);
		$topic_id = $debug_query->fields['topic_id'];

		$query=$db->Execute("insert into $dbtables[posts] (topic_id, forum_id, post_time, post_edit_time, post_edit_count, post_username, post_player_id) values ($topic_id, $forumdata[forum_id], '$time', '$time', 0, '$playerinfo[character_name]', $playerinfo[player_id])");
		db_op_result($query,__LINE__,__FILE__);

		$debug_query = $db->Execute("select * from $dbtables[posts] where topic_id='$topic_id' and forum_id=$forumdata[forum_id] and post_time='$time' and post_edit_time='$time' and post_edit_count=0 and post_username='$playerinfo[character_name]' and post_player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$post_id = $debug_query->fields['post_id'];

		$query=$db->Execute("insert into $dbtables[posts_text] (post_id, post_text, topic_id, forum_id) values ($post_id, '". clean_words($topicmessage) . "', $topic_id, $forumdata[forum_id])");
		db_op_result($query,__LINE__,__FILE__);

		$query=$db->Execute("select * from $dbtables[topics] where forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);
		$topics=$query->RecordCount();

		$query=$db->Execute("select * from $dbtables[posts] where forum_id=$forumdata[forum_id] and topic_id=$topic_id");
		db_op_result($query,__LINE__,__FILE__);
		$topicposts=$query->RecordCount();

		$query=$db->Execute("update $dbtables[topics] set topic_replies=$topicposts, lastpostdate='$time' where topic_id=$topic_id");
		db_op_result($query,__LINE__,__FILE__);

		$query=$db->Execute("select * from $dbtables[posts] where forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);
		$posts=$query->RecordCount();

		$query=$db->Execute("update $dbtables[forums] set forum_topics=$topics, forum_posts=$posts, lastposttime='$time' where forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);

		$forumplayer['postcount']++;
		$query=$db->Execute("update $dbtables[fplayers] set postcount=$forumplayer[postcount] where player_id='$playerinfo[player_id]'");
		db_op_result($query,__LINE__,__FILE__);

		unset($_SESSION['currentprogram'], $currentprogram);
		close_database();
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=team-forum.php?command=readtopic&topic_id=$topic_id\">";
	}

	if($command == "readtopic"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$query=$db->Execute("select * from $dbtables[topics] where topic_id=$topic_id and forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);
		$num=$query->RecordCount();
		if($num > 0) {
			$topic_views = $query->fields['topic_views'];
			$topic_status = $query->fields['topic_status'];
			$topic_title = $query->fields['topic_title'];
			$topic_status = $query->fields['topic_status'];
		}

		$topictype = "";
		if($topic_status == 9)
			$topictype = $l_forums_sticky2;

		if($topic_status == 0)
			$topictype = $l_forums_locked2;

		$topic_views++;

		$query=$db->Execute("update $dbtables[topics] set topic_views=$topic_views where topic_id=$topic_id and forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);

		$query=$db->Execute("select * from $dbtables[posts] where topic_id=$topic_id and forum_id=$forumdata[forum_id] order by post_time");
		db_op_result($query,__LINE__,__FILE__);
		$num=$query->RecordCount();
		$smarty->assign("topic_id", $topic_id);
		$smarty->assign("topic_status", $topic_status);
		$smarty->assign("forumadmin", $forumplayer['admin']);
		$smarty->assign("topictype", $topictype);
		$smarty->assign("l_forums_reply", $l_forums_reply);
		$smarty->assign("totalposts", $num-1);
		$smarty->assign("l_forums_subject", $l_forums_subject);
		$smarty->assign("topic_title", $topic_title);
		$smarty->assign("l_forums_date", $l_forums_date);
		$smarty->assign("templatename", $templatename);
		$smarty->assign("topicstartdate", date($local_date_full_format, strtotime($time)));
		$smarty->assign("l_readm_sender", $l_readm_sender);
		$smarty->assign("l_forums_edited", $l_forums_edited);
		$smarty->assign("l_forums_lastedit", $l_forums_lastedit);
		$smarty->assign("totalposts2", $num);

		if($num > 0) {
			$count = 0;
			while (!$query->EOF){
				$postinfo = $query->fields;

/*				$post_id = mysql_result($result, $i, "post_id");
				$post_username = mysql_result($result, $i, "post_username");
				$post_edit_time = mysql_result($result, $i, "post_edit_time");
				$post_edit_count = mysql_result($result, $i, "post_edit_count");
				$post_time = mysql_result($result, $i, "post_time");
				$post_player_id = mysql_result($result, $i, "post_player_id");
*/
				$query3 = $db->Execute("select * from $dbtables[fplayers] where player_id='$postinfo[post_player_id]'");
				db_op_result($query3,__LINE__,__FILE__);
				$admins = $query3->RecordCount();

				if($admins > 0) {
					$admin = $query3->fields['admin'];
				}

				$accounttype = "";
				if($admin==1)
					$accounttype = $l_forums_coord;
				if($admin==2)
					$accounttype = $l_forums_admin;

				$date = explode(" ", $post_time);
				$temp = explode("-", $date[0]);
				$clock = explode(":", $date[1]);

				$newdate = mktime($clock[0], $clock[1], $clock[2], $temp[1], $temp[2], $temp[0]);

				$post_time = date("M j, Y g:ia", $newdate);

				$date = explode(" ", $post_edit_time);
				$temp = explode("-", $date[0]);
				$clock = explode(":", $date[1]);

				$newdate = mktime($clock[0], $clock[1], $clock[2], $temp[1], $temp[2], $temp[0]);

				$post_edit_time = date("M j, Y g:ia", $newdate);

				$query2=$db->Execute("select * from $dbtables[posts_text] where post_id=$postinfo[post_id]");
				db_op_result($query2,__LINE__,__FILE__);
				$num2=$query2->RecordCount();

				if($num2 > 0) {
					$post_text = $query2->fields['post_text'];
				}

				$query3 = $db->Execute("select * from $dbtables[players] where player_id='$postinfo[post_player_id]'");
				db_op_result($query3,__LINE__,__FILE__);
				$avatar = $query3->fields['avatar'];

/*				print "$postinfo[post_id]<br>";
				print "$accounttype$postinfo[post_username]\t$postinfo[post_player_id]<br>";
				print "$postinfo[post_edit_time]<br>";
				print "$postinfo[post_edit_count]<br>";
				print "$postinfo[post_time]<br>";
				print "$post_text<br>";
				print "$topic_status<br><br>";
*/
				$postid[$count] = $postinfo['post_id'];
				$avatarimg[$count] = $avatar;
				$accounttypes[$count] = $accounttype;
				$postusername[$count] = $postinfo['post_username'];
				$postdate[$count] = date($local_date_full_format, strtotime($postinfo['post_time']));
				$posttext[$count]=nl2br(str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", str_replace("  ","&nbsp;&nbsp;",$post_text)));
				$posteditcount[$count] = 0;
				if($postinfo['post_edit_count'] != 0){
					if($postinfo['post_edit_count'] > 1)
						$shows[$count] = "times";
					else $shows[$count] = "time";

					$posteditdate[$count] = date($local_date_full_format, strtotime($postinfo['post_edit_time']));
					$posteditcount[$count] = $postinfo['post_edit_count'];
				}
				$topicstatus[$count] = $topic_status;
				$isposter[$count] = ($forumplayer['player_id'] == $postinfo['post_player_id']);
				$count++;
				$query->MoveNext();
			}
		}

		$smarty->assign("count", $count);
		$smarty->assign("postid", $postid);
		$smarty->assign("avatarimg", $avatarimg);
		$smarty->assign("accounttypes", $accounttypes);
		$smarty->assign("postusername", $postusername);
		$smarty->assign("postdate", $postdate);
		$smarty->assign("posttext", $posttext);
		$smarty->assign("posteditdate", $posteditdate);
		$smarty->assign("posteditcount", $posteditcount);
		$smarty->assign("shows", $shows);
		$smarty->assign("l_readm_del", $l_readm_del);
		$smarty->assign("l_forums_edit", $l_forums_edit);
		$smarty->assign("l_readm_repl", $l_readm_repl);
		$smarty->assign("isposter", $isposter);
		$smarty->assign("l_forums_lock", $l_forums_lock);
		$smarty->assign("l_team_notmember", $l_team_notmember);
		$smarty->assign("l_forums_showtopic", $l_forums_showtopic);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teamforum-readtopic.tpl");
		include ("footer.php");
		die();
	}

	if($command == "postreply"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$smarty->assign("topic_id", $topic_id);
		$smarty->assign("l_forums_showtopic", $l_forums_showtopic);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teamforum-postreply.tpl");
		include ("footer.php");
		die();
	}

	if($command == "finishreply"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$query=$db->Execute("select * from $dbtables[forums] where forum_id=$forumdata[forum_id] and private=1");
		db_op_result($query,__LINE__,__FILE__);
		$private=$query->RecordCount();

		$query=$db->Execute("insert into $dbtables[posts] (topic_id, forum_id, post_time, post_edit_time, post_edit_count, post_username, post_player_id) values ($topic_id, $forumdata[forum_id], '$time', '$time', 0, '$playerinfo[character_name]', $playerinfo[player_id])");
		db_op_result($query,__LINE__,__FILE__);

		$debug_query = $db->Execute("select * from $dbtables[posts] where topic_id='$topic_id' and forum_id=$forumdata[forum_id] and post_time='$time' and post_edit_time='$time' and post_edit_count=0 and post_username='$playerinfo[character_name]' and post_player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$post_id = $debug_query->fields['post_id'];

		$query=$db->Execute("insert into $dbtables[posts_text] (post_id, post_text, topic_id, forum_id) values ($post_id, '". clean_words($topicmessage). "', $topic_id, $forumdata[forum_id])");
		db_op_result($query,__LINE__,__FILE__);

		$query=$db->Execute("select * from $dbtables[posts] where forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);
		$posts=$query->RecordCount();

		$query=$db->Execute("update $dbtables[forums] set forum_posts=$posts, lastposttime='$time' where forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);

		$query=$db->Execute("select * from $dbtables[posts] where forum_id=$forumdata[forum_id] and topic_id=$topic_id");
		db_op_result($query,__LINE__,__FILE__);
		$posts=$query->RecordCount();

		$query=$db->Execute("update $dbtables[topics] set topic_replies=$posts, lastpostdate='$time' where topic_id=$topic_id");
		db_op_result($query,__LINE__,__FILE__);

		$forumplayer['postcount']++;
		$query=$db->Execute("update $dbtables[fplayers] set postcount=$forumplayer[postcount] where player_id='$playerinfo[player_id]'");
		db_op_result($query,__LINE__,__FILE__);

		unset($_SESSION['currentprogram'], $currentprogram);
		close_database();
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=team-forum.php?command=readtopic&topic_id=$topic_id#$post_id\">";
	}

	if($command == "edit"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$query=$db->Execute("select * from $dbtables[posts_text] where post_id=$post_id and forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);
		$posttext = $query->fields['post_text'];

		$smarty->assign("post_id", $post_id);
		$smarty->assign("topic_id", $topic_id);
		$smarty->assign("posttext", $posttext);
		$smarty->assign("l_forums_showtopic", $l_forums_showtopic);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teamforum-edit.tpl");
		include ("footer.php");
		die();
	}

	if($command == "finishedit"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$query=$db->Execute("select * from $dbtables[posts] where post_id=$post_id");
		db_op_result($query,__LINE__,__FILE__);
		$post_edit_count = $query->fields['post_edit_count'];

		$post_edit_count++;

		$query=$db->Execute("update $dbtables[posts] set post_edit_time='$time', post_edit_count=$post_edit_count where post_id=$post_id");
		db_op_result($query,__LINE__,__FILE__);

		$query=$db->Execute("update $dbtables[posts_text] set post_text='". clean_words($topicmessage) ."' where post_id=$post_id");
		db_op_result($query,__LINE__,__FILE__);

		unset($_SESSION['currentprogram'], $currentprogram);
		close_database();
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=team-forum.php?command=readtopic&topic_id=$topic_id#$post_id\">";
	}

	if($command == "delete"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$query=$db->Execute("delete from $dbtables[posts] where post_id=$post_id and forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);
		$query=$db->Execute("delete from $dbtables[posts_text] where post_id=$post_id and forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);

		$query=$db->Execute("select * from $dbtables[posts] where forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);
		$posts=$query->RecordCount();

		$query=$db->Execute("update $dbtables[forums] set forum_posts=$posts, lastposttime='$time' where forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);

		$query=$db->Execute("select * from $dbtables[posts] where topic_id=$topic_id order by post_time");
		db_op_result($query,__LINE__,__FILE__);
		$num=$query->RecordCount();

		if($num == 0){
			$query=$db->Execute("delete from $dbtables[topics] where topic_id=$topic_id and forum_id=$forumdata[forum_id]");
			db_op_result($query,__LINE__,__FILE__);

			$query=$db->Execute("select * from $dbtables[topics] where forum_id=$forumdata[forum_id]");
			db_op_result($query,__LINE__,__FILE__);
			$topics=$query->RecordCount();

			$query=$db->Execute("select * from $dbtables[posts] where forum_id=$forumdata[forum_id]");
			db_op_result($query,__LINE__,__FILE__);
			$posts=$query->RecordCount();

			$query=$db->Execute("update $dbtables[forums] set forum_topics=$topics, forum_posts=$posts, lastposttime='$time' where forum_id=$forumdata[forum_id]");
			db_op_result($query,__LINE__,__FILE__);

			unset($_SESSION['currentprogram'], $currentprogram);
			close_database();
			echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=team-forum.php?command=showtopics\">";
		}else{
			unset($_SESSION['currentprogram'], $currentprogram);
			close_database();
			echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=team-forum.php?command=readtopic&topic_id=$topic_id\">";
		}
	}

	if($command == "lock"){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select * from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$query=$db->Execute("select * from $dbtables[topics] where forum_id=$forumdata[forum_id] and topic_id=$topic_id");
		db_op_result($query,__LINE__,__FILE__);
		$topics=$query->RecordCount();

		if($topics>0){
				$topic_status = $query->fields['topic_status'];
		}
		$topic_status = 1 - $topic_status;

		$query=$db->Execute("update $dbtables[topics] set topic_status=$topic_status where topic_id=$topic_id and forum_id=$forumdata[forum_id]");
		db_op_result($query,__LINE__,__FILE__);

		unset($_SESSION['currentprogram'], $currentprogram);
		close_database();
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=team-forum.php?command=readtopic&topic_id=$topic_id\">";
	}

}else{
	$smarty->assign("l_team_notmember", $l_team_notmember);
	$smarty->assign("l_forums_showtopic", $l_forums_showtopic);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."teamforum-die.tpl");
	include ("footer.php");
	die();
}

?>

