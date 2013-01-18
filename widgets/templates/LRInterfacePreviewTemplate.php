	<div class="hide-frame">
		<div class="innerTimelineContent giveBackground">
			
			
			<div class="row-fluid">
				<div class="span5">
					
					
					<a href="#" data-bind="attr:{href:currentObject().url}">
						<img data-bind="visible: currentObject().hasScreenshot, attr:{src:currentObject().image}" />
					</a>
					
					<p data-bind="visible:!currentObject().hasScreenshot" class="notFound">Screenshot not found</p>
					<div style="margin-top: 10px; text-align:center;">
						<button class="btn btn-info" data-bind="click: handleDataClick">View Metadata</button>
						<a class="btn btn-success" data-bind="attr:{href:doTransform(currentObject().url)}">Go to resource</a>
					</div>
				</div>
				<div class="span7">
					<p data-bind="text:currentObject().title" class="searchHeader" style="padding-top:0;height:auto;"></p>
					<p data-bind="text:currentObject().description"></p>
				</div>
			</div>
		</div>
	</div>	
</div>

<div class="modal" id="metadata" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div style="height: 4%;" class="modal-header visualModal">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<div>
			<span>View Data</span>
		</div>
	</div>
	
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
	
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/lrbrowser.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/paradata.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/utils.js' , __FILE__ ) ?>"></script>
	<link type="text/css" href="<?php echo plugins_url( '/styles/Hypertree.css' , __FILE__ ) ?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo plugins_url( '/styles/prettify.css' , __FILE__ ) ?>" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/prettify.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.nicescroll.js' , __FILE__ ) ?>"></script>
	<script type="text/javascript" src="<?php echo plugins_url( '/scripts/moment.min.js' , __FILE__ ) ?>"></script>