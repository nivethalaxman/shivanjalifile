<?php
include "config/config.php";
include "db_connection/db_connection.php";

if ($_SESSION['admin_login_id'] == '') {
    echo "<script>window.location='index.php';</script>";
}

/*search begins*/
if (isset($_POST['stud_name']) && $_POST['stud_name'] != '') {
    $stud_name = $_POST['stud_name'];
    $condi .= " AND first_name like '$stud_name%'";
}
if (isset($_POST['mob_no']) && $_POST['mob_no'] != '') {
    $mob_no = $_POST['mob_no'];
    $condi .= " AND mobile = '$mob_no'";
}
if (isset($_POST['reg_no']) && $_POST['reg_no'] != '') {
    $reg_no = $_POST['reg_no'];
    $condi .= " AND reg_no = '$reg_no'";
}
if (isset($_GET['reg_no']) && $_GET['reg_no'] != '') {
    $reg_no = base64_decode(base64_decode(base64_decode(base64_decode($_GET['reg_no']))));
    $condi .= " AND reg_no = '$reg_no'";
}

$SLCT = "SELECT * FROM students_reg WHERE students_reg_id>0 " . $condi . " ORDER BY students_reg_id DESC";
$QURY = mysql_query($SLCT);
$items = mysql_num_rows($QURY);


$thispage = $_SERVER["PHP_SELF"];
$num = $items;        // number of items in list
$per_page = 20;            // Number of items to show per page
$showeachside = 4;            //  Number of items to show either side of selected page

if (isset($_GET['start']) && $_GET['start'] != '' && $_GET['start'] != 'undefined') {
    $From = $_GET['start'];
} else {
    $From = 0;
}

if (isset($_GET['to']) && $_GET['to'] != '' && $_GET['start'] != 'undefined') {
    $To = $_GET['to'];
} else {
    $To = 20;
}

if (empty($start)) {
    if (isset($_GET['start']) != '') {
        $start = $_GET['start'];                // Current start position
    } else {
        $start = 0;
    }
}                    // Current start position

$max_pages = ceil($num / $per_page);        // Number of pages
$cur = ceil($start / $per_page) + 1;    // Current page number

// This selection Auto Complete CSS//
$SLCT_auto_comp = mysql_query("SELECT first_name FROM students_reg ORDER BY first_name ASC");
while ($FTCH_auto = mysql_fetch_array($SLCT_auto_comp)) {
    $result_name .= '"' . $FTCH_auto['first_name'] . '",';
}
// This selection Auto Complete CSS//
?>
<!DOCTYPE html>
<html>
<head>
    <?php
    include "default_head.php";
    ?>
    <title>Students List - Shivanjali</title>
</head>
<body>
<!-- Side Navbar -->
<?php
include "default_nav.php";
?>
<div class="page home-page student-list">
    <!-- navbar-->
    <?php
    include "default_header.php";
    ?>

    <section class="statistics section-padding section-no-padding-bottom section_top">
        <div class="container-fluid">
            <header>
                <h1 class="title_h1">
                    Students List &nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#edit_card" title="Add New">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New
                    </button>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#email_list" title="Copy Email">
                        <i class="fa fa-envelope" aria-hidden="true"></i> Copy Email
                    </button>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#Search" title="Search">
                        <i class="fa fa-search" aria-hidden="true"></i> Search
                    </button>
                    <a href="students_list.php" type="button" class="btn btn-info btn-sm" title="View All">
                        <i class="fa fa-search" aria-hidden="true"></i> View All
                    </a>
                    <p class="p_tag">Total no. of Students: <?php echo $num; ?></p>
                </h1>
            </header>


            <!-- Modal -->
            <div class="modal fade" id="Search" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="students_list.php" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Students Search</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Student Name</h2>
                                    <input type="text" name="stud_name" id="s_stud_name" value="<?php echo $stud_name ?>"
                                           class="text_field">
                                </div>
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Mobile No</h2>
                                    <input type="text" name="mob_no" id="s_mob_no" maxlength="15"
                                           value="<?php echo $mob_no ?>" class="text_field">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Reg No</h2>
                                    <input type="text" name="reg_no" id="s_reg_no" maxlength="15"
                                           value="<?php echo $reg_no ?>" class="text_field">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-info" onclick="return check_me()">Submit</button>
                            <button type="button" class="btn btn-danger" onclick="document.getElementById('s_stud_name').value = '',document.getElementById('s_reg_no').value = '',document.getElementById('s_mob_no').value = ''">Clear</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!--Modal2-->
            <div class="modal fade" id="edit_card" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <?php
                        $FTCH_max_id  = mysql_fetch_array(mysql_query("SELECT MAX(reg_no) as reg_no FROM students_reg"));
                        //$FTCH_roll_no = mysql_fetch_array(mysql_query("SELECT reg_no FROM students_reg WHERE students_reg_id='$FTCH_max_id[max_id]'"));
                        if($FTCH_max_id['reg_no'] != '')
                        {
                            $roll_no  = $FTCH_max_id['reg_no']+1;
                        }
                        else
                        {
                            $roll_no  = '0001';
                        }
                        ?>
                        <form action="bl/students_reg_BL.php?action=add" method="post" name="myform" enctype="multipart/form-data">
                            <input type="hidden" name="reg_no" id="reg_no" maxlength="35" value="<?php echo $roll_no; ?>" readonly="readonly" />
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Students Registration</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">First Name <span style="color:#FF0000">*</span></h2>
                                    <input type="text" name="first_name" class="text_field" id="first_name" maxlength="35" />
                                </div>
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Last Name</h2>
                                    <input type="text" name="last_name" class="text_field" id="last_name"  maxlength="35" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col_pad">
                                    <h2 class="h2_popup">Upload Photo</h2>
                                    <div class="form-group">
                                        <input type="file" name="student_photo" class="file">
                                        <div class="input-group col-xs-12">
                                            <span class="input-group-addon"><i class="fa fa-file-image-o" aria-hidden="true"></i></span>
                                            <input type="text" class="form-control input-lg" disabled placeholder="Upload Image">
                                            <span class="input-group-btn">
                                                <button class="browse btn btn-primary input-lg" type="button"><i class="fa fa-search" aria-hidden="true"></i> Browse</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Spouse / Father Name <span style="color:#FF0000">*</span></h2>
                                    <div class="row">
                                    <div class="col-md-4 col_pad">
                                    <select name="rel_type" id="rel_type"  style="width:80px" tabindex="2">
                                        <option value="">Select</option>
                                        <option value="S/O">S/O</option>
                                        <option value="D/O">D/O</option>
                                        <option value="W/O">W/O</option>
                                        <option value="H/O">H/O</option>
                                    </select>
                                    </div>
                                    <div class="col-md-8 col_pad">
                                        <input type="text" name="rel_name" class="text_field" id="rel_name" maxlength="35" />
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Gender <span style="color:#FF0000">*</span></h2>
                                    <input type="radio" name="gender" id="male" value="M" tabindex="4"/> Male &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="gender" id="female" value="F" tabindex="4"/> Female
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Date of Birth <span style="color:#FF0000">*</span></h2>
                                    <input type="text" name="dob" id="dob"  tabindex="5"/>&nbsp;<img src="date-picker-images/date-picker.gif"  onclick="displayDOBCalendar(document.getElementById('dob'),'dd-mm-yyyy',this)" style="cursor:pointer" title="Select Date"/>
                                </div>
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Date of Joining <span style="color:#FF0000">*</span></h2>
                                    <input type="text" name="doj" id="doj" value="<?php echo date("d-m-Y"); ?>"/>&nbsp;<img src="date-picker-images/date-picker.gif" onclick=" displayDOBCalendar(document.getElementById('doj'), 'dd-mm-yyyy', this)" style="cursor:pointer" title="Select Date"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Email</h2>
                                    <input type="text" name="email_id" class="text_field" id="email_id" maxlength="50" />
                                </div>
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Landline No.</h2>
                                    <input type="text" name="landline" id="landline" class="text_field">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Mobile No.1 <span style="color:#FF0000">*</span></h2>
                                    <input type="text" name="mobile" class="text_field" id="mobile" maxlength="11" />
                                </div>
                                <div class="col-md-6 col_pad">
                                    <h2 class="h2_popup">Mobile No.2</h2>
                                    <input type="text" name="mobile2" class="text_field" id="mobile2" maxlength="11" />
                                </div>
                            </div>
                            <div class="row row_pad_popup">
                                <h2 class="h2_popup">Address <span style="color:#FF0000">*</span></h2>
                                <textarea name="address" id="address" class="text_field text_area"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info" onclick="return validateMe()">Submit</button>
                            <button type="button" class="btn btn-danger" onclick="return clearField()">Clear</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="email_list" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Student Email List</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 col_pad">
                                    <?php
                                    $SLCT_STU = "SELECT email_id FROM students_reg WHERE active_status='active' AND email_id!='' ORDER BY students_reg_id";
                                    $QURY_STU = mysql_query($SLCT_STU);
                                    $num_mail = mysql_num_rows($QURY_STU);
                                    ?>
                                    <p class="fieldset" style="width: 100%">
                                        <label class="" for="signin-email"><b>E-mail ID's - <?php echo $num_mail; ?></b></label><br />
                                        <?php
                                        $flagg=0;
                                        $lop=1;

                                        while($FTCH_STU = mysql_fetch_object($QURY_STU))
                                        {
                                            if($FTCH_STU->email_id!='')
                                            {
                                                $stu_mail = $stu_mail.$FTCH_STU->email_id.", ";
                                                if($lop%200==0)
                                                {
                                                    $stu_mail = rtrim($stu_mail,", ")."<br><br>";
                                                }
                                                $lop++;
                                            }
                                        }
                                        echo rtrim($stu_mail.", ");
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $stu_mail = '';
            $SLCT = "SELECT *, DATE_FORMAT(dob, '%d-%m-%Y') AS dob, DATE_FORMAT(doj, '%d-%m-%Y') AS doj, DATE_FORMAT(post_dt, '%d-%m-%Y') AS post_dt, DATE_FORMAT(update_dt, '%d-%m-%Y') AS update_dt FROM students_reg WHERE students_reg_id>0 ".$condi." ORDER BY students_reg_id DESC LIMIT $From, $To";
            //$SLCT = "SELECT *, DATE_FORMAT(dob, '%d-%m-%Y') AS dob, DATE_FORMAT(doj, '%d-%m-%Y') AS doj, DATE_FORMAT(post_dt, '%d-%m-%Y') AS post_dt, DATE_FORMAT(update_dt, '%d-%m-%Y') AS update_dt FROM students_reg where first_name = '$stud_name' , reg_no = '$reg_no' , mobile = '$mob_no' ORDER BY students_reg_id DESC LIMIT $From, $To";
            $QURY = mysql_query($SLCT);

            if(isset($_GET['start'])=='')
            {
                $loop 	= 1;
            }
            else
            {
                $loop	= $From+1;
            }

            if(mysql_num_rows($QURY)>0)
            {
                $i = 1;
            while($FTCH = mysql_fetch_object($QURY))
            {
            $students_reg_id	=	base64_encode(base64_encode(base64_encode(base64_encode($FTCH->students_reg_id))));
            //$students_reg_id	=	$FTCH->students_reg_id;

            if($FTCH->active_status == "active")
            {
                $stat	=	"Active";
                $status = '<a href="bl/students_reg_BL.php?action=update_std_list&students_reg_id='.$students_reg_id.'&status='.$FTCH->active_status.'">'.$stat.'</a>';
            }
            else
            {
                $stat	=	"In Active";
                $status = "<a href='#' class='ico active' onclick=confirm_fee('$students_reg_id','$FTCH->active_status')>".$stat."</a>";
            }

            if($FTCH->update_dt == '00-00-0000')
            {
                $update_dt = '-';

            }
            else
            {
                $update_dt = $FTCH->update_dt;
            }

            if($loop%2 != "0")
                $bg = "";
            else
                $bg = "class='odd'";
            ?>
                <?php if ($i % 4 == 1) { ?>
                <div class="row d-flex align-items-stretch">
            <?php } ?>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-block std-lst">
                            <h2 class="h2_card"><!--GR-1438-->
                             <span class="reg_no">RN: <?php echo $FTCH->reg_no?></span>
                                <a href="students_edit.php?students_reg_id=<?php echo $students_reg_id?>" title="Edit">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                                <?php if($FTCH->active_status == "active") { ?>
                                    <a href="bl/students_reg_BL.php?action=update_std_list&students_reg_id=<?php echo $students_reg_id; ?>&status=<?php echo $FTCH->active_status; ?>" title="Active">
                                        <i class='fa fa-circle' aria-hidden='true'></i>
                                    </a>
                                <?php } else { ?>
                                    <a href="#" onclick="confirm_fee('<?php echo $students_reg_id; ?>','<?php echo $FTCH->active_status; ?>')" title="Inactive">
                                        <i class='fa fa-circle inactive' aria-hidden='true'></i>
                                    </a>
                                <?php } ?>
                                <a href="print_reg_receipt.php?students_reg_id=<?php echo $students_reg_id?>" target="_blank" title="Reg.Receipt">
                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                </a>
                            </h2>
                            <div class="card_info">
                            <div class="stud-img">
                            <img src="uploads/course/default.jpg">
                            </div>
                                <h3 class="h3_card"><?php echo wordwrap($FTCH->first_name, 25, "<br />", true);?></h3>
                                <p class="dob">DOB: <?php echo $FTCH->dob;?> / DOJ: <?php echo $FTCH->doj;?></p>
                                <div class="std-details">
                                <div class="details">
                                    <i class="fa fa-phone fa_phn_bg" aria-hidden="true"></i>
                                    <span class="phn_no"><?php echo $FTCH->mobile;?></span>
                                </div>
                                <div class="details">
                                    <i class="fa fa-envelope fa_envelope_bg" aria-hidden="true"></i>
                                    <a href="" class="mail"><?php echo $FTCH->email_id;?></a>
                                </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <?php if ($i % 4 == 0) { ?>
                </div>
            <?php }
                $i++; ?>
            <?php } ?>
                <?php if ($i % 4 != 1) echo "</div>"; ?>
            <?php } ?>

            <nav aria-label="Page navigation example" id="page_slider">
                <h1>Page <?php print($cur); ?> of <?php print($max_pages); ?>
                    <ul class="pagination">
                        <?php if (($start - $per_page) >= 0) {
                            $next = $start - $per_page; ?>
                            <li class="page-item prev">
                                <a class="page-link" href="<?php print("$thispage?to=$To" . ($next > 0 ? ("&start=") . $next : "")); ?>">Prev</a>
                            </li>
                        <?php }
                        $eitherside = ($showeachside * $per_page);
                        $pg = 1;
                        for ($y = 0; $y < $num; $y += $per_page) {
                            $class = ($y == $start) ? "active" : "";
                            if (($y > ($start - $eitherside)) && ($y < ($start + $eitherside))) { ?>
                                <li class="page-item <?php echo $class; ?>">
                                    <a class="page-link" href="<?php print("$thispage?to=$To" . ($y > 0 ? ("&start=") . $y : "")); ?>"><?php echo $pg; ?></a>
                                </li>
                                <?php
                            }
                            $pg++;
                        }
                        if ($start + $per_page < $num) { ?>
                            <li class="page-item next">
                                <a class="page-link" href="<?php print("$thispage?to=$To&start=" . max(0, $start + $per_page)); ?>">Next</a>
                            </li>
                        <?php } ?>
                    </ul>
                </h1>
            </nav>

        </div>

    </section>
    <?php include "default_footer.php"; ?>
</div>

<?php include "default_footer_scripts.php"; ?>

<script type="text/javascript">
    function check_me() {
        var stud_name = document.getElementById('s_stud_name').value.replace(/^[\s]+/, '').replace(/[\s]+$/, '');
        document.getElementById('s_stud_name').value = stud_name;

        var reg_no = document.getElementById('s_reg_no').value.replace(/^[\s]+/, '').replace(/[\s]+$/, '');
        document.getElementById('s_reg_no').value = reg_no;

        var mob_no = document.getElementById('s_mob_no').value.replace(/^[\s]+/, '').replace(/[\s]+$/, '');
        document.getElementById('s_mob_no').value = mob_no;

        if (stud_name == '' && reg_no == '' && mob_no == '') {
            alert("Please Enter Atleast One of these...!");
            document.getElementById('s_stud_name').focus();
            return false;
        }
    }
</script>
<script type="text/javascript">
    function focus_fun() {
        //document.getElementById('screen_links').focus();
    }
    function confirm_fee(id, status_act, from, to) {
        var student_id = id;
        var status_active = status_act;
        var page_from = from;
        var page_to = to;
        if (confirm("Do u want to genearate Registration Fees for this Student")) {
            window.location = "bl/students_reg_BL.php?action=update_std_list&students_reg_id=" + student_id + "&status=" + status_active + "&to=" + page_to + "&start=" + page_from + '&post_dt=yes';
        }
        else {
            window.location = "bl/students_reg_BL.php?action=update_std_list&students_reg_id=" + student_id + "&status=" + status_active + "&to=" + page_to + "&start=" + page_from + '&post_dt=no';
        }
    }
</script>
<script type="text/javascript" src="js-old/students_JS.js"></script>
<script type="text/javascript" src="date-picker1/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>
<link rel="stylesheet" href="date-picker1/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" media="screen"></link>
<script type="text/javascript" src="date-picker1/dhtmlgoodies_dob_calendar/dhtmlgoodies_dob_calendar.js"></script>
<link rel="stylesheet" href="date-picker1/dhtmlgoodies_dob_calendar/dhtmlgoodies_dob_calendar.css"></link>

<!--<script type="text/javascript" src="autocomplete/jquery.autocomplete.js"></script><!--In autocomplete generating the raw material name-->-->
<!--<link href="autocomplete/jquery.autocomplete.css" type="text/css" rel="stylesheet" /><!--In autocomplete generating the raw material name-->-->

<script type="text/javascript" src="js/autocomplete.js"></script>
<script type="text/javascript">
    var mate_name = [<?php echo $result_name;?>];

    /*$().ready(function() {
        $("#s_stud_name").autocomplete(mate_name);

    });*/
    $(document).ready(function(){
        // Sonstructs the suggestion engine
        var roll_no = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            // The url points to a json file that contains an array of country names
            local: mate_name
        });

        // Initializing the typeahead with remote dataset
        $('#s_stud_name').typeahead(null, {
            name: 'roll_no',
            source: roll_no,
            limit: 10 /* Specify maximum number of suggestions to be displayed */
        });
    });

    $(document).on('click', '.browse', function(){
        var file = $(this).parent().parent().parent().find('.file');
        file.trigger('click');
    });
    $(document).on('change', '.file', function(){
        $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
</script>
</body>
</html>