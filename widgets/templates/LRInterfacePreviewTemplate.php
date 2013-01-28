<div class="hide-frame">
	<div class="innerTimelineContent giveBackground">
		<div class="row-fluid" style="text-align: center;">
			<h2 data-bind="text:currentObject().title"></h2>
			<a href="#" data-bind="attr:{href:currentObject().url}">
				<img style="height: 300px; width: 400px;margin: 0 auto;" data-bind="visible: currentObject().hasScreenshot, attr:{src:currentObject().image}" />
			</a>
				
			<p data-bind="visible:!currentObject().hasScreenshot" class="notFound">Screenshot not found</p>
				
			<p data-bind="text:currentObject().description"></p>
			<div style="margin-top: 10px; text-align:center;">
				<button class="btn btn-info" data-bind="click: handleDataClick, visible: isMetadataHidden() || isMetadataHidden() == -1">View Metadata</button>
				<button class="btn btn-info" data-bind="click: handleDataHideClick, visible: isMetadataHidden() == false">Close Metadata</button>
				<a data-bind="attr:{href:doTransform(currentObject().url)}"><button>Go to resource</button></a>
			</div>
		</div>
	</div>
</div>	


<div class="modal" id="metadata" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bind="visible: ! isMetadataHidden()">
	<div id="modal-data-view"></div>
</div>
	
	<script type="text/javascript">
		var globalSliceMax = 500;
		var NODE_URL = "http://node01.public.learningregistry.net";
		/*{{#server}}
			var temp_NODE_URL = ["","http://node01.public.learningregistry.net","http://node02.public.learningregistry.net", "http://lrtest01.public.learningregistry.net",
								 "http://sandbox.learningregistry.org/", "http://lrdev03.learningregistry.org", "http://lrdev05.learningregistry.org"];
			NODE_URL = temp_NODE_URL[{{server}}];

		{{/server}}*/
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
		var serviceHost = "<?php echo $host; ?>";
		<?php include_once('scripts/applicationPreview.php'); ?>
	</script>
