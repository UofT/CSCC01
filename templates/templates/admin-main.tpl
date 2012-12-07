<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<link href="css/styles.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="css/base/jquery-ui.css" />
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.9.1.custom.css">
<script src="js/jquery-1.8.2.js" type="text/javascript"></script>
<script src="js/jquery-ui-1.9.1.custom.min.js" type="text/javascript" ></script>
{$javascript}
<script src="js/script.js" type="text/javascript"></script>
<script type="text/javascript">
	var isSuccesful = false;
	
	$(function() {
        $( "#admin_menu" ).menu({
			select: function (event, ui) {
						switch(ui.item.text()) {
							case 'Courses':
								xajax_showCoursesAdmin();
								break;
							case 'Students':
								xajax_showStudentsAdmin();
								break;
							case 'Tutors':
								xajax_showTutorsAdmin();
								break;
							case 'Pending approvals':
								xajax_showPendingApprovals();
								break;
							case 'Log Out':
								xajax_logOut();
								break;
						}
					}
		});
		
		$( "button" ).button();
		
		function updateTips( t ) {
            tips
                .text( t )
                .addClass( "ui-state-highlight" );
            setTimeout(function() {
                tips.removeClass( "ui-state-highlight", 1500 );
            }, 500 );
        }
 
        function checkLength( o, n, min, max ) {
            if ( o.val().length > max || o.val().length < min ) {
                o.addClass( "ui-state-error" );
                updateTips( "Length of " + n + " must be between " +
                    min + " and " + max + "." );
                return false;
            } else {
                return true;
            }
        }
 
        function checkRegexp( o, regexp, n ) {
            if ( !( regexp.test( o.val() ) ) ) {
                o.addClass( "ui-state-error" );
                updateTips( n );
                return false;
            } else {
                return true;
            }
        }
		
		var course_id = $( "#course_id" ),
            course_name = $( "#course_name" ),
            course_desc = $( "#course_desc" ),
            allCourseFields = $( [] ).add( course_id ).add( course_name ).add( course_desc ),
            tips = $( ".validateTips" );
		
		$( "#dialog-course-form" ).dialog({
            autoOpen: false,
            height: 350,
            width: 350,
            modal: true,
            buttons: {
                "Create a course": function() {
                    var bValid = true;
                    allCourseFields.removeClass( "ui-state-error" );
 
                    bValid = bValid && checkLength( course_id, "id", 3, 16 );
                    bValid = bValid && checkLength( course_name, "name", 1, 50 );
 
                    if ( bValid ) {
                        xajax_createCourse(xajax.getFormValues('course-form'));
						$( this ).dialog( "close" );
                    }
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
                allCourseFields.val( "" ).removeClass( "ui-state-error" );
            }
        });
    });
	
	function delCourse(id){
		var answer = confirm("Delete course?")
		
		if (answer){
			xajax_deleteCourseAdmin(id);
		}
	}
	
	function delStudent(id){
		var answer = confirm("Delete student?")
		
		if (answer){
			xajax_deleteStudentAdmin(id);
		}
	}
	
	function delTutor(id){
		var answer = confirm("Delete tutor?")
		
		if (answer){
			xajax_deleteTutorAdmin(id);
		}
	}
	
	function approveTutor(id){
		var answer = confirm("Approve tutor?")
		
		if (answer){
			xajax_activateUser(id);
		}
	}
</script>
<style>
    .ui-menu { width: 120px; }
	div#table-contain { width: 350px; margin: 20px 0; }
    div#table-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#table-contain table td, div#table-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
	label, input { display:block; }
        input.text { margin-bottom:12px; width:95%; padding: .4em; }
        fieldset { padding:0; border:0; margin-top:25px; }
		.ui-dialog .ui-state-error { padding: .3em; }
        .validateTips { border: 1px solid transparent; padding: 0.3em; }
    </style>
<title>Tutoring - Admin menu</title>
</head>

<body>
<div id="dialog-course-form" title="Create new course">
  <p class="validateTips">All form fields are required.</p>
    <form id="course-form">
    <fieldset>
        <label for="course_id">ID</label>
        <input type="text" name="course_id" id="course_id" class="text ui-widget-content ui-corner-all" />
        <label for="course_name">Name</label>
        <input type="text" name="course_name" id="course_name" value="" class="text ui-widget-content ui-corner-all" />
        <label for="course_desc">Description</label>
        <input type="text" name="course_desc" id="course_desc" value="" class="text ui-widget-content ui-corner-all" />
    </fieldset>
    </form>
</div>

<div class="container" style="position:relative;z-index:1;">
	<div id="menu-div" style="z-index: 10; float: left; left: 0; top: 0;">
    <ul id="admin_menu">
    <li><a href="#">Courses</a></li>
    <li><a href="#">Students</a></li>
    <li><a href="#">Tutors</a></li>
    <li><a href="#">Pending approvals</a></li>
    <li class="ui-state-disabled"><a href="#">&nbsp;</a></li>
    <li><a href="#">Log Out</a></li>
</ul>
  </div>
    <div id="main" style="padding-left: 150px">
      <h1>Welcome {$userfirstname}!</h1>
    </div>
    <div id="footer"></div>
</div>
</body>
</html>
