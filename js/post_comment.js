function PostComment(post_id, country, ip, user_id) {
	var com = $("#comment"+post_id).val();
	if (com != "") {
		$.post("/overall/insert/insert_comment.php?post_id="+post_id+"&country="+country+"&ipaddress="+ip+"&user_id=" + user_id, { comment: com});
		$("#post_box_comment_" + post_id).hide();
	}
}