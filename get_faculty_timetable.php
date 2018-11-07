<?php

require( 'db.php' );

$con = connect_db();

if( ! isset( $_POST['regno'] ) ){
    echo json_encode( array( 'status'=>0, 'text'=>'Invalid request'));
    exit();
}

$regno = $_POST['regno'];

// get those classess he teaches at
$sql = "SELECT * FROM teaches_at WHERE faculty_id = '$regno'";
$result = $con->query( $sql );

if( $result->num_rows <= 0 ){
    echo json_encode( array( 'status'=>0, 'text'=>'No Classes found', 'batches'=> array() ));
    exit();
}

// get today
$today = (int) date('N');

$hours = array(0,0,0,0,0,0);

function get_hours_of_subject( $timetable, $subject ){
    foreach( $timetable as $hour=>$sub ){
        if( $sub == $subject ){
            $h = (int) substr( $hour, -1);
            $hours[ $h - 1 ] = $sub;
        }
    }
}

while( $row = $result->fetch_assoc() ){
    $branch = $row['branch'];
    $sem = $row['sem'];
    $batch = $row['batch'];
    $subject = $row['subject'];

    // get todays timetable for this batch
    $sql = "SELECT * FROM timetable WHERE weekday = $today and branch = $branch and sem = $sem and batch = $batch";
    $res = $con->query( $sql );

    $timetable = $res->fetch_assoc();
    
    // removing unwanted fields
    unset( $timetable['branch']);
    unset( $timetable['batch']);
    unset( $timetable['sem']);
    unset( $timetable['weekday']);
    // now only hour_1, hour_2, ....


    get_hours_of_subject( $timetable, $subject );
}

print_r( $hours );


?>