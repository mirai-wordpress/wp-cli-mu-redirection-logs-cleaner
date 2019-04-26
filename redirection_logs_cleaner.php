<?php

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	class Redirection_Logs_Cleaner extends WP_CLI_Command {

		protected $db;

		private $args, $assoc_args;

		public function __construct() {
			$this->db = $GLOBALS['wpdb'];
		}
				
		/* Custom functions here */
		
		public function clean_404_logs ($args, $assoc_args)
		{
			//USAGE: wp rlogscleaner clean_404_logs --days=3 will clean 404 error logs older than 3 days
			//default is 7 days

			$this->args       = $args;
			$this->assoc_args = $assoc_args;

			if (isset ($assoc_args['days']) && ($assoc_args['days']) > 0)
				$days = $assoc_args['days'];
			else
				$days = "7";

			$this->clean_logs ($days,'redirection_404');

		}

		public function clean_redirection_logs ($args, $assoc_args)
		{
			//USAGE: wp rlogscleaner clean_redirection_logs --days=3 will clean redirection logs older than 3 days
			//default is 7 days

			$this->args       = $args;
			$this->assoc_args = $assoc_args;

			if (isset ($assoc_args['days']) && ($assoc_args['days']) > 0)
				$days = $assoc_args['days'];
			else
				$days = "7";

			$this->clean_logs ($days,'redirection_logs');

		}

		public function clean_both_logs ($args, $assoc_args)
		{
			//USAGE: wp rlogscleaner clean_both_logs --days=3 will clean redirection and 404 logs older than 3 days
			//default is 7 days

			$this->args       = $args;
			$this->assoc_args = $assoc_args;

			if (isset ($assoc_args['days']) && ($assoc_args['days']) > 0)
				$days = $assoc_args['days'];
			else
				$days = "7";

			$this->clean_logs ($days,'redirection_logs');
			$this->clean_logs ($days,'redirection_404');

		}

		/*clean log*/
	    private function clean_logs($days, $log_type) {

			$dbname = $this->db->dbname;

			//get existing blogs IDs
			$blogs = $this->db->get_results( "SELECT blog_id,domain FROM ".$dbname."_blogs" );
			
			foreach($blogs as $blog)
			{
				$table = $dbname . "_" . $blog->blog_id . "_".$log_type;

				$delete_query = $this->db->query( "DELETE FROM $table WHERE created < (CURRENT_DATE() - INTERVAL ".$days." DAY)" );

				if($delete_query > 0)
					$optimize_query = $this->db->query( "OPTIMIZE TABLE $table" );

				echo "Site: ".$blog->domain;
				echo "\nDeleted ".$delete_query." rows\n\n";
			}
		}
	}

	WP_CLI::add_command( 'rlogscleaner', 'Redirection_Logs_Cleaner' );
}
