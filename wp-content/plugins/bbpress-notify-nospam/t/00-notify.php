<?php
/**
 * @group bbpnns
 * @group bbpnns_notify
 */
require_once(ABSPATH . '/wp-content/plugins/bbpress/bbpress.php');
require_once(ABSPATH . '/wp-content/plugins/bbpress-notify-nospam/bbpress-notify-nospam.php');

class Tests_bbPress_notify_no_spam_notify_new extends WP_UnitTestCase 
{
	public $forum_id;
	public $topic_id;
	public $reply_id;
	
	public $topic_body;
	public $topic_body_clean;
	public $reply_body;
	public $reply_body_clean;
	
	public function __construct()
	{
		$this->topic_body = "<p>This is <br> a <br /> test paragraph for topic</p>\n\n<p>And a new <br/>paragraph</p>";
		
		$this->reply_body = "<p>This is <br> a <br /> test paragraph for reply</p>\n\n<p>And a new <br/>paragraph</p>";
		
		$this->topic_body_clean = "This is  a  test paragraph for topic\n\nAnd a new paragraph\n";
		
		$this->reply_body_clean = "This is  a  test paragraph for reply\n\nAnd a new paragraph\n";
	}
	
	
	public function setUp()
	{
		parent::setUp();
		
		// Create new forum
		$this->forum_id = bbp_insert_forum(
			array(
				'post_title'  => 'test-forum',
				'post_status' => 'publish'
			)
		);
		
		// Create new topic
		$this->topic_id = bbp_insert_topic(
			array(
				'post_parent' => $this->forum_id,
				'post_title'  => 'test-topic',
				'post_content' => $this->topic_body
			),
			array(
				'forum_id' => $this->forum_id		
			)
		);
		
		// Create new reply
		$this->reply_id = bbp_insert_reply(
			array(
				'post_parent' => $this->topic_id,
				'post_title'  => 'test-reply',
				'post_content' => $this->reply_body
			),
			array(
				'forum_id' => $this->forum_id,
				'topic_id' => $this->topic_id		
			)
		);
		
		add_filter('bbpnns_dry_run', '__return_true');
		
		// Non-spam, non-empty recipents
		$recipients = array('administrator', 'subscriber');
		update_option('bbpress_notify_newtopic_recipients', $recipients);
		$subs_id = $this->factory->user->create( array( 'role' => 'subscriber' ));
		
		
	}
	
	public function tearDown()
	{
		parent::tearDown();
		
		remove_all_filters('bbpnns_dry_run');
		remove_all_filters('bbpnns_skip_reply_notification');
		remove_all_filters('bbpnns_skip_topic_notification');
	}
	
	
	public function test_topic_recipient_filter()
	{
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		
		$this->assertTrue((bool) has_filter('bbpress_notify_recipients_hidden_forum', array(&$bbpnns, 'munge_newtopic_recipients')), 
				'bbpress_notify_recipients_hidden_forum filter exists');
		
		$expected = array('foo', 'bar');
		$recipients = apply_filters('bbpress_notify_recipients_hidden_forum', $expected, $this->forum_id);
		
		$this->assertEquals($expected, $recipients, 'Filter returns input array for non-hidden forum');

		//hide forum
		bbp_hide_forum($this->forum_id);
		
		$recipients = apply_filters('bbpress_notify_recipients_hidden_forum', $expected, $this->forum_id);
		$this->assertEquals('administrator', $recipients, 'Filter returns \'administrator\' for non-hidden forum');
		
	}
	
	
	public function test_notify_topic()
	{
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		
		// Spam, returns -1
		bbp_spam_topic($this->topic_id);
		$status = $bbpnns->notify_new_topic($this->topic_id, $this->forum_id);
		$this->assertEquals(-1, $status, 'Spam topic returns -1');
		
		// Non-spam, empty recipients returns -2
		bbp_unspam_topic($this->topic_id);
		delete_option('bbpress_notify_newtopic_recipients');
		$status = $bbpnns->notify_new_topic($this->topic_id, $this->forum_id);
		$this->assertEquals(-2, $status, 'Empty Recipients -2');
		
		
		update_option('bbpress_notify_newtopic_email_body', '[topic-content]');
		
		// Non-spam, non-empty recipents
		$recipients = array('administrator', 'subscriber');
		update_option('bbpress_notify_newtopic_recipients', $recipients);
		$arry = $bbpnns->notify_new_topic($this->topic_id, $this->forum_id);
		$this->assertTrue(is_array($arry), 'Good notify returns array in test mode');
		
		list($recipients, $body) = $arry;
		$this->assertEquals($this->topic_body_clean, $body, 'Topic body munged correctly');
		
		
		// Force skip
		add_filter('bbpnns_skip_topic_notification', '__return_true');
		$status = $bbpnns->notify_new_topic($this->topic_id, $this->forum_id);
		$this->assertEquals(-3, $status, 'Force skip -3');
		
	}
	
	
	public function test_notify_reply()
	{
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		
		// Spam, returns -1
		bbp_spam_reply($this->reply_id);
		$status = $bbpnns->notify_new_reply($this->reply_id, $this->topic_id, $this->forum_id);
		$this->assertEquals(-1, $status, 'Spam reply returns -1');
		
		// Clear recipients
		$expected_recipients = array();
		update_option('bbpress_notify_newreply_recipients', $recipients);
		
		// Non-spam, empty recipients returns -2
		bbp_unspam_reply($this->reply_id);
		$status = $bbpnns->notify_new_reply($this->reply_id, $this->topic_id, $this->forum_id);
		$this->assertEquals(-2, $status, 'Empty Recipients -2');
		
		update_option('bbpress_notify_newreply_email_body', '[reply-content]');
		
		// Non-spam, non-empty recipents
		update_option('bbpress_notify_newreply_recipients', array('administrator', 'subscriber'));
		$arry = $bbpnns->notify_new_reply($this->reply_id, $this->topic_id, $this->forum_id);
		$this->assertTrue(is_array($arry), 'Good notify returns array in test mode');
		
		list($recipients, $body) = $arry;
		$this->assertEquals($this->reply_body_clean, $body, 'Reply body munged correctly');
		
		// Force skip
		add_filter('bbpnns_skip_reply_notification', '__return_true');
		$status = $bbpnns->notify_new_reply($this->topic_id);
		$this->assertEquals(-3, $status, 'Force skip -3');
	}
	
	
	public function test_send_notification()
	{
		$expected_recipients = array('administrator', 'subscriber');
		
		// Non-hidden forum
		update_option('bbpress_notify_newtopic_recipients', $recipients);
		
		$bbpnns = bbPress_Notify_NoSpam::bootstrap();
		list($got_recipients, $body) = $bbpnns->send_notification($expected_recipients, 'test subject', 'test_body');
		$this->assertEquals($expected_recipients, $got_recipients, 'Test mode got expected recipients');
		
		// Hidden forum returns admins only
		bbp_hide_forum($this->forum_id);
		$recipients = apply_filters('bbpress_notify_recipients_hidden_forum', $expected_recipients, $this->forum_id);
		list($got_recipients, $body) = $bbpnns->send_notification($recipients, 'test subject', 'test_body');
		$this->assertEquals('administrator', $got_recipients, 'Filtered send_notification returns administrator');
	}
		
}

/* End of 00-notify.php */
/* Location: bbpress-notify-no-spam/t/00-notify.php */