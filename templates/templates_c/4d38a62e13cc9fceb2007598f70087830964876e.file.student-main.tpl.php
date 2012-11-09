<?php /* Smarty version Smarty-3.1.12, created on 2012-11-07 12:33:56
         compiled from "/usr/local/zend/apache2/htdocs/CSCC01/templates/templates/student-main.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14721793775098a826e4d0a1-27129758%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4d38a62e13cc9fceb2007598f70087830964876e' => 
    array (
      0 => '/usr/local/zend/apache2/htdocs/CSCC01/templates/templates/student-main.tpl',
      1 => 1352307738,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14721793775098a826e4d0a1-27129758',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5098a826e6fd28_27068150',
  'variables' => 
  array (
    'javascript' => 0,
    'userlogin' => 0,
    'userfirstname' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5098a826e6fd28_27068150')) {function content_5098a826e6fd28_27068150($_smarty_tpl) {?><!doctype html>
<html>
<head>
<meta charset="UTF-8">
<link href="css/styles.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="css/base/jquery-ui.css" />
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.9.1.custom.css">
<style type="text/css">
.validateTips { border: 1px solid transparent; padding: 0.3em; }
input.text { margin-bottom:12px; width:95%; padding: .4em; }
</style>
<script src="js/jquery-1.8.2.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.1.custom.min.js" type="text/javascript" ></script>
<script src="js/jquery.raty.js" type="text/javascript" ></script>
<?php echo $_smarty_tpl->tpl_vars['javascript']->value;?>

<script src="js/script.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
        $( "#student_menu" ).menu({
			select: function (event, ui) {
						switch(ui.item.text()) {
							case 'My Courses':
								xajax_showMyCourses('<?php echo $_smarty_tpl->tpl_vars['userlogin']->value;?>
');
								break;
							case 'My Tutors':
								xajax_showMyTutors('<?php echo $_smarty_tpl->tpl_vars['userlogin']->value;?>
');
								break;
							case 'Register for courses':
								xajax_showCoursesStudent();
								break;
							case 'Look for tutors':
								xajax_showTutors();
								break;
							case 'Log Out':
								xajax_logOut();
								break;
						}
					}
		});
		
		$('#star').raty({
			click: function(score, evt) {
						$('#course_rating').val(score);
					}
		});
		
		$('#startutor').raty({
			click: function(score, evt) {
						$('#tutor_rating').val(score);
					}
		});
		
		$( "#dialog-course-form" ).dialog({
            autoOpen: false,
            height: 250,
            width: 400,
            modal: true,
            buttons: {
                "Save": function() {
                    	xajax_saveCourseCommentRate(xajax.getFormValues('course-form'));
                	},
                Cancel: function() {
                    	$( this ).dialog( "close" );
                	}
            },
            close: function() {       }
        });
		
		$( "#dialog-tutor-form" ).dialog({
            autoOpen: false,
            height: 250,
            width: 400,
            modal: true,
            buttons: {
                "Save": function() {
                    	xajax_saveTutorCommentRate(xajax.getFormValues('tutor-form'));
                	},
                Cancel: function() {
                    	$( this ).dialog( "close" );
                	}
            },
            close: function() {       }
        });
	});
	
	function showCourse(id) {
		xajax_loadCourseCommentRate(id)
	}
	
	function showTutor(id) {
		xajax_loadTutorCommentRate(id)
	}
</script>
<title>Tutoring - Student menu</title>
</head>

<body>
<div id="dialog-course-form" title="Comment and rate this course">
  <form id="course-form">
    <fieldset>
      <label for="course_rating">Rate this course:</label>
      <div id="star"></div><input name="course_id" id="course_id" type="hidden" value="">
      <input name="course_rating" id="course_rating" type="hidden" value="0">
      <label for="course_comment">Comments</label>
      <textarea name="course_comment" cols="60" rows="3" class="text ui-widget-content ui-corner-all" id="course_comment"></textarea>
    </fieldset>
  </form>
</div>
<div id="dialog-tutor-form" title="Comment and rate this tutor">
  <form id="tutor-form">
    <fieldset>
      <label for="tutor_rating">Rate this tutor:</label>
      <div id="startutor"></div><input name="tutor_id" id="tutor_id" type="hidden" value="">
      <input name="tutor_rating" id="tutor_rating" type="hidden" value="0">
      <label for="tutor_comment">Comments</label>
      <textarea name="tutor_comment" cols="60" rows="3" class="text ui-widget-content ui-corner-all" id="tutor_comment"></textarea>
    </fieldset>
  </form>
</div>
<div class="container" style="position:relative;z-index:1;">
	<div id="menu-div" style="z-index: 10; float: left; left: 0; top: 0;">
    <ul id="student_menu">
    <li>
    	<a href="#">My Account</a>
        <ul>
        	<li><a href="#">My Courses</a></li>
    		<li><a href="#">My Tutors</a></li>
        </ul>
    </li>
    <li><a href="#">Register for courses</a></li>
    <li><a href="#">Look for tutors</a></li>
    <li class="ui-state-disabled"><a href="#">&nbsp;</a></li>
    <li><a href="#">Log Out</a></li>
</ul>
  </div>
    <div id="main" style="padding-left: 150px">
      <h1>Welcome <?php echo $_smarty_tpl->tpl_vars['userfirstname']->value;?>
!</h1>
    </div>
    <div id="footer"></div>
</div>
</body>
</html>
<?php }} ?>