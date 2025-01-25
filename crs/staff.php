<?php 
 
include('crssession.php');

if(!session_id())
{
  session_start();
}

include 'headerit.php';
?>

<div class="container">

IT Staff

</div>

<?php include 'footer.php';?>
