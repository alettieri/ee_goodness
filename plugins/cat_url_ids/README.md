# Category Url Ids

Returns pipe (|) deliminated caetgory ids by group and url (segment). 

##Usage

####Return pipe deliminated categories by group and url

{exp:cat_url_ids category_group="1|2|3" category_url="{segment_1}|{segment_2}"}

####Return pipe deliminated categories by url

{exp:cat_url_ids category_url="{segment_1}|{segment_2}"}

####Return all categories, if you're feeling like it.

{exp:cat_url_ids}

####Or, use it as a template pair using the {cat_url_ids} template tag.

#####Ex

{exp:cat_url_ids category_group="1|2|3" category_url="{segment_1}|{segment_2}" parse="inward"}
	{exp:channel:entries channel="news" category_group="1|2|3" category="{cat_url_ids}" status="open"}
		...
	{/exp:channel:entries}
{/exp:cat_url_ids}

####Note

The parse='inward' parameter is imperative here, you want the plugin to parse earlier than the exp:channel:entries module.

####Rock! It!