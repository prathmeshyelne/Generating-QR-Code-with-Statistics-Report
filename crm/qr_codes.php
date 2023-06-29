<?php 
include('header.php');
check_auth();

$condition="";
if($_SESSION['QR_USER_ROLE']==1){
	$condition=" and added_by='".$_SESSION['QR_USER_ID']."'";
}

if(isset($_GET['type']) && $_GET['type']=='download'){
	$link="https://chart.apis.google.com/chart?cht=qr&chs=".$_GET['chs']."&chco=".$_GET['chco']."&chl=".$_GET['chl'];
	header('Content-type: application/x-file-to-save');
	header('Content-Disposition: attachment;filename='.time().'.jpg');
	ob_end_clean();
	readfile($link);
}

if(isset($_GET['status']) && $_GET['status']!='' && isset($_GET['id']) && $_GET['id']>0){
	$status=get_safe_value($_GET['status']);
	$id=get_safe_value($_GET['id']);
	
	if($status=="active"){
		$status=1;
	}else{
		$status=0;
	}
	
	mysqli_query($con,"update qr_code set status='$status' where id='$id' $condition");
	redirect('qr_codes.php');
}

$res=mysqli_query($con,"select qr_code.*,users.email from qr_code,users where 1 and qr_code.added_by=users.id  $condition order by qr_code.added_on desc");
?>
<div class="page-wrapper">
            <div class="page-breadcrumb">
               <div class="row align-items-center">
                  <div class="col-md-6 col-8 align-self-center">
                     <h3 class="page-title mb-0 p-0">QR Codes</h3>
					 <h5 class="mb-0 p-0">
						<a href="manage_qr_code.php">Add QR Code</a>
					 </h3>
                  </div>
               </div>
            </div>
	
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<?php if(mysqli_num_rows($res)>0){?>	
					<div class="table-responsive">
						<table class="table user-table">
							<thead>
								<tr>
									<th class="border-top-0">#</th>
									<th class="border-top-0">Name</th>
									<th class="border-top-0">QR Code</th>
									<th class="border-top-0">Link</th>
									<th class="border-top-0">Color</th>
									<th class="border-top-0">Size</th>
									<th class="border-top-0">Added On</th>
									<th class="border-top-0">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i=1;
								while($row=mysqli_fetch_assoc($res)){?>
								<tr>
									<td><?php echo $i++?></td>
									<td><?php echo $row['name']?><br/>
									<?php
									if($_SESSION['QR_USER_ROLE']==0){?>
									Added By: <b><?php echo $row['email']?></b><br/>
									<?php } ?>
									<a href="qr_report.php?id=<?php echo $row['id']?>">Report</a>
									</td>
									<td style="text-align:center;">
										<a target="_blank" href="https://chart.apis.google.com/chart?cht=qr&chs=<?php echo $row['size']?>&chco=<?php echo $row['color']?>&chl=<?php echo $qr_file_path?>?id=<?php echo $row['id']?>"><img src="https://chart.apis.google.com/chart?cht=qr&chs=<?php echo $row['size']?>&chco=<?php echo $row['color']?>&chl=<?php echo $qr_file_path?>?id=<?php echo $row['id']?>" width="100px"/></a>
										<br/>
										<b><a href="?type=download&chs=<?php echo $row['size']?>&chco=<?php echo $row['color']?>&chl=<?php echo $qr_file_path?>?id=<?php echo $row['id']?>">download</a></b>
										
										
									</td>
									<td><?php echo $row['link']?></td>
									<td><?php echo $row['color']?>
									</td>
									<td><?php echo $row['size']?></td>
									<td><?php echo getCustomDate($row['added_on'])?></td>
									<td>
									<a href="manage_qr_code.php?id=<?php echo $row['id']?>">Edit</a>&nbsp;	
									<?php	
									$status="active";
									$strStatus="Deactive";
									if($row['status']==1){
										$status="deactive";
										$strStatus="Active";
									}
									?>
								
								<a href="?id=<?php echo $row['id']?>&status=<?php echo $status?>"><?php echo $strStatus?></a>
						   </td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
						<?php } else{
				echo "No data found";  
			  }
			  ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('footer.php')?>