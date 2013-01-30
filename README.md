Learning Registry Wordpress Plugin
===================
##Introduction
Some info about the plugin and its usage here.
## Install
0. [Download](https://github.com/mickmuzac/LR-Wordpress-Widget/archive/master.zip) the LR Wordpress plugin
0. [Install and activate](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) the LR Wordpress plugin via the upload tab in the admin panel in Wordpress
0. Install and activate [Widgetize Pages Light](http://wordpress.org/extend/plugins/widgetize-pages-light/) via the admin panel in Wordpress  
  * Widgetize Pages Light allows you to create a "sidebar" that is displayed in the main content area of a page
  * You may follow [Widgetize's sidebar creation instructions](http://otwthemes.com/online-documentation-widgetize-pages-light/) whenever this document asks you to create a new sidebar

##Configuration
###Global Plugin Settings
0. Click on the newly created `LR Interface` link in the admin panel
  * `WebService Endpoint` is mandatory and is set to `http://12.109.40.31` by default. Changing this value is generally not needed
  * `Learning Registry Node` is mandatory and is set to `http://node01.public.learningregistry.net/` by default.
  * `Maximum Slice` is mandatory and is set to `500` by default. This value is the maximum number of resources to return when performing a slice operation against a node. The number of values actually displayed on a single page can be changed in the next section
0. Click `Save Changes`

### Displaying Search Results
0. Create a new sidebar. Name it `Results Sidebar`
0. [Create a new Wordpress page](http://codex.wordpress.org/Pages#Creating_Pages) to display the results. Before publishing this new page, click the blue icon labeled `Insert Sidebar ShortCode`. Select `Results Sidebar` and then click "Insert". You may now save and publish the page.
0. Add the `LR Interface Results` widget to `Results Sidebar` 
   * [How to activate and configure widgets](http://en.support.wordpress.com/widgets/)
0. Configure the `LR Interface Results` widget
  * `Search Method` is mandatory and is set to `Indexed Search` by defualt. Slice is also an option
  * `Number of Results Per Page` is mandatory and is set to `50` by defualt.

### Displaying a Search Bar
Note: This section is placed after displaying the results because the search bar requires an existing results page to function properly.

0. Add the `LR Interface Search Bar` widget to a sidebar (will appear as a search box on your live site).
0. Configure the `LR Interface Search Bar` widget
  * `Search placeholder` is optional and is displayed whenever the search bar is empty
  * `Results` is mandatory and will determine where the search results are displayed
0. To-do...

### Displaying the Standards Browser
0. Create a new sidebar. Name it `Standards Sidebar`
0. Create a new Wordpress page to display the standards. Before publishing this new page, click the blue icon labeled `Insert Sidebar ShortCode`. Select `Standards Sidebar` and then click "Insert". You may now save and publish the page.
0. Add the `LR Interface Standards Browser` widget to `Standards Sidebar` 
0. Configure the `LR Interface Standards Browser` widget
  * To-do
0. To-do
