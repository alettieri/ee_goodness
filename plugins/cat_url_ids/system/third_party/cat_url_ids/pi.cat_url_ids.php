<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
	'pi_name' => 'Category Url Ids',
	'pi_version' => '1.0.0',
	'pi_author' => 'Antonio Lettieri',
	'pi_author_url' => 'http://webtonio.com/',
	'pi_description' => 'Returns pipe separated category ids based on group and ',
	'pi_usage' => Cat_url_ids::usage()
);

class Cat_url_ids {
	
	protected $category_group;
	protected $category_url;
	protected $category_ids;

	public function __construct()
	{
		$this->EE =& get_instance();
	
		$this->category_group = $this->EE->TMPL->fetch_param( "category_group" ); 
		$this->category_url = $this->EE->TMPL->fetch_param( "category_url" );

		$this->return_data = $this->get_cat_ids(); 
	}


	private function get_cat_ids() {
		
		$cat_groups = ( strlen( $this->category_group ) > 0 ) ? explode( "|", $this->category_group ) : null;
		$cat_urls = ( strlen( $this->category_url ) > 0 ) ? explode( "|", $this->category_url ) : null;
		$cat_ids = array();

		$query = $this->EE->db->select( 'cat_id' )->from( 'categories' );

		if( isset( $cat_urls ) && count( $cat_urls ) > 0 )
			$query->where_in( "cat_url_title", $cat_urls );
		
		if( isset( $cat_groups ) && count( $cat_groups ) > 0 )
			$query->where_in( "group_id", $cat_groups );

		foreach( $query->get()->result_array() as $category ) {

			$cat_ids[] = $category[ "cat_id" ];
		}
		
		$this->category_ids = implode( "|", $cat_ids );

		return $this->get_output();
	}

	function get_output() {

		$tagdata = $this->EE->TMPL->tagdata;
		$tagdata = $this->EE->functions->prep_conditionals($tagdata, $this->EE->TMPL->var_single);

		if( $tagdata ) {
			
			foreach( $this->EE->TMPL->var_single as $key => $val ) {
				
				if( $key == "cat_url_ids" )
					$tagdata = $this->EE->TMPL->swap_var_single( $val, $this->category_ids, $tagdata );
			
			}

			return $tagdata;
		}
		return $this->category_ids;
	}
	
	
	
	/* Displays Usage info on plugin page */
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

			Rock!It!

		<?php 
		$buffer = ob_get_contents();

		ob_end_clean(); 

		return $buffer;
	}
}

/* End of file pi.cat_url_ids.php */ 
/* Location: ./system/expressionengine/third_party/plugin_name/pi.cat_url_ids.php */