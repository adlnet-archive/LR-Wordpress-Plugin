<div class="hide-frame">
	<div class="innerTimelineContent giveBackground">
		<div class="row-fluid" style="text-align: center;">
			<h2 data-bind="html:currentObject().title">Loading...</h2>
			
			<a href="#" data-bind="attr:{href:doTransform(currentObject().url)}">
				<img src="<?php echo plugins_url( 'templates/images/qmark.png' , __FILE__ ); ?>" style="height: 300px; width: 400px;margin: 0 auto;" data-bind="'visible': currentObject().hasScreenshot, 'attr':{'alt':currentObject().title, src:currentObject().image}" alt="Resource Screenshot"/>
				<img src="<?php echo plugins_url( 'templates/images/qmark.png' , __FILE__ ); ?>" style="height: 150px; width: 150px;margin: 0 auto;" data-bind="'visible': !currentObject().hasScreenshot, 'attr':{'src':currentObject().image}" alt="Question mark image"/>
			</a>
				
			<p data-bind="visible:!currentObject().hasScreenshot" class="notFound">Screenshot not found</p><br/>
			<div style="padding-top:5px;">
				<span style="color: #888;line-height:16px;" data-bind="text: 'Source: ' + currentObject().publisher, visible: currentObject().publisher != undefined && currentObject().publisher != '' "></span>
			</div>
			<p style="margin: 2.4rem auto" data-bind="html:currentObject().description"></p>
			<div style="margin-top: 10px; text-align:center;">
				<?php if(empty($options['metadata'])): ?>
				<button class="btn btn-info lrSubmitNoFloat" style="border: 1px solid #d9d9d9 !important;" data-bind="click: handleDataClick, visible: isMetadataHidden() || isMetadataHidden() == -1">View Metadata</button>
				<button class="btn btn-info lrSubmitNoFloat" style="border: 1px solid #d9d9d9 !important;" data-bind="click: handleDataHideClick, visible: isMetadataHidden() == false">Close Metadata</button>
				<?php endif; ?>
				<a data-bind="attr:{href:doTransform(currentObject().url)}">
					<button style="border: 1px solid #d9d9d9 !important;" class="btn lrSubmitNoFloat">Go to resource</button>
				</a>
			</div>
		</div>
	</div>
</div>	

<div class="modal" id="metadata" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bind="visible: ! isMetadataHidden()">
	<div id="modal-data-view"></div>
</div>
	
	<script type="text/javascript">
		var globalSliceMax = <?php echo is_numeric($options['slice']) && $options['slice'] > 0 ? $options['slice'] : 500; ?>;
		var NODE_URL = '<?php echo empty($options['node'])?"http://node01.public.learningregistry.net/":$options['node']; ?>';
		var serviceHost = "<?php echo $host; ?>";
		var qmarkUrl = '<?php echo plugins_url( "templates/images/qmark.png" , dirname(__FILE__) ) ?>';
	</script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.xml2json.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.eComboBox.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jit-yc.js' , __FILE__ ) ?>"></script>

	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/paradata.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/utils.js' , __FILE__ ) ?>"></script>
	<link type="text/css" href="<?php echo plugins_url( '/styles/Hypertree.css' , __FILE__ ) ?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo plugins_url( '/styles/prettify.css' , __FILE__ ) ?>" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/prettify.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript">
		<?php include_once('scripts/applicationPreview.php'); ?>
	</script>