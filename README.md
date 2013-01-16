LR Wordpress Plugin
===================
### Install
0. After downloading this plugin, extract it
0. Upload the extracted folder to "http://your_wordpress_directory/wp-content/plugins/"
0. Install and activate the LR Wordpress plugin via the admin panel in Wordpress
0. Install and activate [Widgetize Pages Light](http://wordpress.org/extend/plugins/widgetize-pages-light/) via the admin panel in Wordpress  
  * Widgetize Pages Light allows you to create a "sidebar" that is displayed in the main content area of a page
  * You may follow [Widgetize's instructions](http://otwthemes.com/online-documentation-widgetize-pages-light/) whenever this document asks you to create a sidebar

### Displaying the Results
0. Follow instructions above to create a new sidebar. Name it `Results Sidebar`
0. Create a new Wordpress page to display the results. Before publishing this new page, click the blue icon labeled labeled `Insert Sidebar ShortCode`, select `Results Sidebar` when given the option, and click "Insert". You may now save and publish the page.
0. Add the `LR Interface Results` widget to `Results Sidebar` 
   * [How to activate and configure widgets](http://en.support.wordpress.com/widgets/)
0. Configure the `LR Interface Results` widget
  * `DataService Endpoint` is mandatory and is set to `http://12.109.40.31` by default. Changing this value is generally not needed
0. To-do

### Displaying a Search Bar
Note: This section is placed after displaying the results because the search bar requires an existing results page to function properly.

0. Add the `LR Interface Search Bar` widget to a sidebar (will appear as a search box on your live site).
0. Configure the `LR Interface Search Bar` widget
  * `Search placeholder` is optional and is displayed whenever the search bar is empty
  * `Results` is mandatory and will determine where the search results are displayed
  * `Search Method` is also mandatory and is set to `Indexed Search` by defualt. Slice is also an option
0. To-do...
