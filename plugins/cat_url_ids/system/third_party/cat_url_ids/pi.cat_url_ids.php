<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** 
 * Plugin Information
 */
$plugin_info = array(
	'pi_name' => 'Category Url Ids',
	'pi_version' => '1.0.0',
	'pi_author' => 'Antonio Lettieri',
	'pi_author_url' => 'http://webtonio.com/',
	'pi_description' => 'Returns pipe separated category ids based on group and ',
	'pi_usage' => Cat_url_ids::usage()
);

/** 
 * Cat Url Ids
 *
 * Returns pipe deliminated category ids from the database based on url segment and category groups.
 */
class Cat_url_ids {
	
	//
	// Category group. Assiged during template parsing.
	//
	protected $category_group;
	//
	// Category url. Assigned during template parsing.
	//
	protected $category_url;
	//
	// Category ids. Will be our return value.
	//
	protected $category_ids;

	/*
	 * Our Construct
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		//
		// Grab the category groups. Can be single or multiple (1|2|3)
		//
		$this->category_group = $this->EE->TMPL->fetch_param( "category_group" ); 
		//
		// Grab the category url. Can be single or multiple (string|string|string)
		//
		$this->category_url = $this->EE->TMPL->fetch_param( "category_url" );
		//
		// Return the cateogry ids.
		//
		$this->return_data = $this->get_cat_ids(); 
	}

	/**
	 * Get Cat Ids
	 *
	 * @return (string) 
	 *
	 * Fetches the category ids based on segment url.
	 * We can have it return the category ids based on a specific category group as well.
	 *
	 */
	private function get_cat_ids() {
		//
		// Set the category group. Null if no groups if specified.
		//
		$cat_groups = ( strlen( $this->category_group ) > 0 ) ? explode( "|", $this->category_group ) : null;
		//
		// Set the category urls. Null if no categories specified.
		// 
		$cat_urls = ( strlen( $this->category_url ) > 0 ) ? explode( "|", $this->category_url ) : null;
		//
		// Category ids we'll set after getting the ids from the database.
		//
		$cat_ids = array();
		//
		// Start our Database Query.
		//
		// Select cat_id from categories
		//
		$query = $this->EE->db->select( 'cat_id' )->from( 'categories' );

		//
		// Should we query by category url(s)? 
		//
		if( isset( $cat_urls ) && count( $cat_urls ) > 0 )
			//
			// Where cat_url_title in (string, string, string)
			//
			$query->where_in( "cat_url_title", $cat_urls );
		//
		// Should we query by category group(s)?
		//
		if( isset( $cat_groups ) && count( $cat_groups ) > 0 )
			//
			// Where group_id in (#,#,#)
			//
			$query->where_in( "group_id", $cat_groups );
		//
		// Now loop through our result set.
		//
		foreach( $query->get()->result_array() as $category ) {
			//
			// Place the category id variable into the cat_ids collection.
			//
			$cat_ids[] = $category[ "cat_id" ];
		}
		
		//
		// Now, pipe deliminate our category ids.
		//
		$this->category_ids = implode( "|", $cat_ids );
		//
		// Return the processed output.
		//
		return $this->get_output();
	}
	/**
	 * Get Output
	 *
	 * @return (string)
	 *
	 * Either returns a template tag or string
	 *
	 */
	function get_output() {
		//
		// Grab the tagdata from the current template that calls this plugin.
		//
		$tagdata = $this->EE->TMPL->tagdata;
		//
		// Handle conditionals in the template.
		//
		$tagdata = $this->EE->functions->prep_conditionals( $tagdata, $this->EE->TMPL->var_single );
		//
		// Are we processing tagdata? 
		//
		if( $tagdata ) {
			//
			// We are, let's see if our plugins tagadata is contained.
			//
			foreach( $this->EE->TMPL->var_single as $key => $val ) {
				//
				// Did the template include {cat_url_ids}
				//
				if( $key == "cat_url_ids" )
					//
					// Swap out the {cat_url_ids} tag with the category_ids string.
					//
					$tagdata = $this->EE->TMPL->swap_var_single( $val, $this->category_ids, $tagdata );
			}
			//
			// Return the processed tagdata.
			//
			return $tagdata;
		}
		//
		// If we don't have any tagdata to process, return the string of category_ids.
		//
		return $this->category_ids;
	}
	
	
	
	/* 
	 * Displays Usage info on plugin page 
	 */
	public function usage(){
		ob_start(); 
		?>
			Usage:

			Return pipe deliminated categories by group and url:

			{exp:cat_url_ids category_group="1|2|3" category_url="{segment_1}|{segment_2}"}

			Return pipe deliminated categories by url:

			{exp:cat_url_ids category_url="{segment_1}|{segment_2}"}

			Return all categories, if you're feeling like it.

			{exp:cat_url_ids}

			Or, use it as a template pair using the {cat_url_ids} template tag.

			Ex:

			{exp:cat_url_ids category_group="1|2|3" category_url="{segment_1}|{segment_2}" parse="inward"}
				{exp:channel:entries channel="news" category_group="1|2|3" category="{cat_url_ids}" status="open"}
					...
				{/exp:channel:entries}
			{/exp:cat_url_ids}
		
			Note:
			The parse='inward' parameter is imperative here, you want the plugin to parse earlier than the exp:channel:entries module.

			Rock! It!

		<?php 
		$buffer = ob_get_contents();

		ob_end_clean(); 

		return $buffer;
	}
}

/* End of file pi.cat_url_ids.php */ 
/* Location: ./system/expressionengine/third_party/plugin_name/pi.cat_url_ids.php */