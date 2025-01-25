<?php 
include('crssession.php');

if(!session_id())
{
  session_start();
}

include 'headerstudent.php';
include 'dbconnect.php';
?>

<div class="container">
  
  <br><br><h5>Course Registration Form</h5><br>
  <form method="POST" action="courseregisterprocess.php">
  <fieldset>
    <div>
      <label for="exampleSelect1" class="form-label mt-4">Select Course</label>
      <select class="form-select" name="fcourse" id="exampleSelect1">

      <?php
        $sql="SELECT * FROM tb_course";
        $result=mysqli_query($con,$sql);

        while($row=mysqli_fetch_array($result))
        {
          echo"<option value='".$row['c_code']."'>".$row['c_name']."</option>";
        }
      ?>
      </select>
     </div><br>



    <div>
      <label for="exampleSelect1" class="form-label mt-4">Select semester</label>
      <select class="form-select" name="fsem" id="exampleSelect1">
        <option>2024/2025-1</option>
        <option>2024/2025-2</option>
        <option>2024/2025-3</option>
      </select>
     </div>

     <br><br><br>

     <button type="Submit" class="btn btn-primary" >Register</button>
     <button type="reset" class="btn btn-info" >Clear Form</button>
     <br><br><br><br><br>
   </fieldset>
 </form>
</div>



<?php include 'footer.php';?>