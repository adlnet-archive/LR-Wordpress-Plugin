<div class="hide-frame">
  <div class="innerTimelineContent giveBackground">
    <div class="row-fluid" style="text-align: center;">
      <h2 data-bind="html:currentObject().title">Loading...</h2>      
      <a href="#" data-bind="attr:{href:doTransform(currentObject().url)}">
	<img src="<?php echo plugins_url( 'templates/images/qmark.png' , __FILE__ ); ?>" style="height: 300px; width: 400px;margin: 0 auto;" data-bind="'visible': currentObject().hasScreenshot, 'attr':{'alt':currentObject().title, src:currentObject().image}" alt="Resource Screenshot"/>
	<img src="<?php echo plugins_url( 'templates/images/qmark.png' , __FILE__ ); ?>" style="height: 150px; width: 150px;margin: 0 auto;" data-bind="'visible': !currentObject().hasScreenshot, 'attr':{'src':currentObject().image}" alt="Magnifying glass image"/>
      </a>
      
      <p data-bind="visible:!currentObject().hasScreenshot" class="notFound">Screenshot not found</p><br/>
      <div style="padding-top:5px;">
	<span style="color: #888;line-height:16px;" data-bind="text: 'Source: ' + currentObject().publisher, visible: currentObject().publisher != undefined && currentObject().publisher != '' "></span>
      </div>
      <p style="margin: 2.4rem 0;text-align:justify;" data-bind="html:currentObject().description"></p>
      <div style="margin-top: 10px; text-align:center;">
	<?php if(empty($options['metadata'])): ?>
	<button class="btn btn-info lrSubmitNoFloat" style="border: 1px solid #d9d9d9 !important;" data-bind="click: handleDataClick, visible: isMetadataHidden() || isMetadataHidden() == -1">
	  View Metadata
	</button>
	<button class="btn btn-info lrSubmitNoFloat" style="border: 1px solid #d9d9d9 !important;" data-bind="click: handleDataHideClick, visible: isMetadataHidden() == false">
	Close Metadata
	</button>
	<?php endif; ?>
	<a data-bind="attr:{href:doTransform(currentObject().url)}">
	  <button style="border: 1px solid #d9d9d9 !important;" class="btn lrSubmitNoFloat">
	    Go to resource
	  </button>
	</a>
      </div>
    </div>
  </div>
</div>	
<a id="flagToggle" class="post-edit-link" style="cursor: pointer;">Flag</a>
<div id="flag-form" class="collapse in">
  <form class="form-horizontal" id="fmr">
    <fieldset>
      <div id="result"></div>
      <input type="hidden" name="json" value="flagged.flag_item" ></input>
      <input type="hidden" name="id" data-bind="value: currentObject().id"></input>
      <div class="control-group">
	<label for="reason" class="control-label">Reason&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
	<select name="reason" id="reason">
	  <option value="duplicate">Duplicate</option>
	  <option value="duplicate">Inappropriate</option>
	</select>	  
      </div>
      <div class="control-group">
	<label class="control-label" for="description" style="vertical-align: top;">Description</label>
	<textarea name="description" id="desc"></textarea>
      </div>
      <div class="control-group">
      	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp
	<button id="submitFlag" class="btn btn-info lrSubmitNoFloat" style="border: 1px solid #d9d9d9 !important;">Flag</button>
      </div>
    </fieldset>
  </form>
</div>
<div id="socialMediaPlugins" style="float:right;margin-top:45px;display:none;"></div>

<div class="modal" id="metadata" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bind="visible: ! isMetadataHidden()">
  <div id="modal-data-view"></div>
</div>
	
<script type="text/javascript">
		var globalSliceMax = <?php echo is_numeric($options['slice']) && $options['slice'] > 0 ? $options['slice'] : 500; ?>;
		var NODE_URL = '<?php echo empty($options['node'])?"http://node01.public.learningregistry.net/":$options['node']; ?>';
		var serviceHost = "<?php echo $host; ?>";
		var qmarkUrl = '<?php echo plugins_url( "templates/images/qmark.png" , dirname(__FILE__) ) ?>';
		var socialMediaPlugins = <?php  echo json_encode($options['social']); ?>;
</script>
<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.xml2json.js' , __FILE__ ) ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jquery.eComboBox.js' , __FILE__ ) ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( '/scripts/jit-yc.js' , __FILE__ ) ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( '/scripts/paradata.js' , __FILE__ ) ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url( '/scripts/utils.js' , __FILE__ ) ?>"></script>
<link type="text/css" href="<?php echo plugins_url( '/styles/Hypertree.css' , __FILE__ ) ?>" rel="stylesheet" />
<link type="text/css" href="<?php echo plugins_url( '/styles/prettify.css' , __FILE__ ) ?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo plugins_url( '/scripts/prettify.js' , __FILE__ ) ?>"></script>
<script type="text/javascript" src="http://malsup.github.io/min/jquery.form.min.js"></script>
<script type="text/javascript">
    <?php include_once('scripts/applicationPreview.php'); ?>
</script>
<script>
  function validate(){
   if($("#desc").val().length <= 0){
      $("#result").html("A description is required.")
                  .css("color", "red");   
      return false;
   }
   return true;
  }
  $(document).ready(function(){
    var frm = $("#flag-form");
    frm.hide();
    $("#flagToggle").click(function(){
    frm.toggle();
    });
    $("#fmr").ajaxForm({beforeSubmit: validate,
    success:function(data){
    if(data.result){
      $("#reason").attr("value", "duplicate");
      $("#desc").attr("value", "");
      $("#result").html("This resource has been flagged for review.  Thank you")
                  .css("color", "green");
    }else{
      $("#result").html("There was an issue flagging this resource, please try again later")
                  .css("color", "red");
    }
    }});
  });
</script>
