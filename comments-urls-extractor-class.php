<?php

if ( !class_exists( 'cf_View' ) )
	require_once 'classes/View.php';

class CUE
{

	private $_conn = null;
	private $_view = null;
	private $_url;
	private $_plugin_name = 'comments-urls-extractor';
	
	public function __construct()
	{
		global $wpdb;
		$this->_conn = &$wpdb;
		$this->_url = 'admin.php?page='.$this->_plugin_name.'/'.$this->_plugin_name.'-class.php';

		$this->_view = new cf_View( $this->_conn );
		$this->_view->load( dirname(__FILE__).'/views/main.html' );
	}
	
	public function install()
	{
		
	}
	
	public function uninstall()
	{
		
	}
	
	public function initialize()
	{
		add_menu_page( 'Comments URLs Extractor', 'Comments URLs Extractor', 8, __FILE__, array( $this, 'controller' ) );
	}
	
	public function controller()
	{
		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'main';
		$this->$action();
	}
	
	private function main()
	{		
		$this->_view->add( 'main' );
		
		$urls = array();
		if ( $_POST['go'] && $_POST['handler'] )
		{					
			foreach ( $_POST['handler'] as $handler => $val )
				if ( method_exists( __CLASS__, $handler ) ) $urls = array_merge( $urls, $this->$handler() );

			$this->_view->urls = join( "\n", $urls );
			$this->_view->add( 'area' );
		}
				
		$this->_view->content( true );
	}
	
	private function parse_comments( $type )
	{
		$links = array();
		$res = $this->_conn->get_results( 'select * from `'.$this->_conn->prefix.'comments` where `comment_approved` = "'.$type.'"' );
		foreach ( $res as $item )
		{		
			if ( isset( $_POST['comment_content'] ) && preg_match( '#<a.+?href="(.+?)".+?</a>#is', $item->comment_content, $pockets ) )
				$links[] = $pockets[1];
				
			if ( isset( $_POST['comment_author_url'] ) && filter_var( $item->comment_author_url, FILTER_VALIDATE_URL ) ) $links[] = $item->comment_author_url;
				
		}		
		return $links;
	}
	
	private function approved_comments()
	{
		return $this->parse_comments( 1 );	 		
	}
	
	private function unapproved_comments()
	{
		return $this->parse_comments( 0 );		
	}
	
	private function spam_comments()
	{
		return $this->parse_comments( 'spam' );		
	}
	
	private function trash_comments()
	{
		return $this->parse_comments( 'trash' );		
	}
	
	private function force_redirect( $url = '' )
	{
		$url = $url ? $url : $this->_url;
		echo '<script type="text/javascript">window.location.href="'.$url.'"</script>';
		exit();		
	}
		
}

?>