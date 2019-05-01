<?php
if (Session::has('success')) {
    echo 'Thank You!';
    echo  Session::get('success');
} else if (Session::has('error')) {
    echo Session::get('error');
}
?>
