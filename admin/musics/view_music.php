<?php 
if(isset($_GET['id']) && is_scalar($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT) !== false && (int) $_GET['id'] > 0){
    $music_id = (int) $_GET['id'];
    $qry = $conn->query("SELECT * from `music_list` where id = {$music_id} and delete_flag = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
		redirect('admin/?page=musics');
	}
}else{
	redirect('admin/?page=musics');
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<style>
	.music-img{
		width:3em;
		height:3em;
		object-fit:cover;
		object-position:center center;
	}
	img#BannerViewer{
		height: 30vh;
		width: 100%;
		object-fit: scale-down;
		object-position:center center;
		/* border-radius: 100% 100%; */
	}
</style>
<div class="card card-outline rounded-0 card-purple">
	<div class="card-header">
		<h3 class="card-title">Music Details</h3>
		<div class="card-tools">
			<a href="<?= e(base_url."admin/?page=musics/manage_music&id=".(int) $id) ?>" class="btn btn-flat btn-primary bg-primary"><span class="fas fa-edit"></span> Edit</a>
			<button id="delete_data" type="button" class="btn btn-flat btn-danger bg-danger"><span class="fas fa-trash"></span> Delete</button>
			<a href="<?= base_url."admin/?page=musics"?>" class="btn btn-flat btn-light bg-light"><span class="fas fa-angle-left"></span>  Back to List</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<div class="form-group">
				<label for="title" class="control-label">Title:</label>
				<div class="pl-4"><?= e($title ?? '') ?></div>
			</div>
			<div class="form-group">
				<label for="artist" class="control-label">Artist</label>
				<div class="pl-4"><?= e($artist ?? '') ?></div>
			</div>
			<div class="form-group">
				<label for="category_id" class="control-label">Category</label>
				<div class="pl-4"><?= e($category_name ?? '') ?></div>
			</div>
			<div class="form-group">
				<label for="description" class="control-label">Description</label>
				<div class="pl-4"><?= isset($description) ? nl2br(e(html_entity_decode($description, ENT_QUOTES | ENT_HTML5, 'UTF-8')), false) : '' ?></div>
			</div>
			<div class="form-group">
				<label for="" class="control-label">Music Banner</label>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?= e(validate_image($banner_path ?? '')) ?>" alt="Music banner" id="BannerViewer" class="img-fluid img-thumbnail bg-gradient-dark border-dark">
			</div>
			<div class="form-group">
				<label for="" class="control-label">Audio File</label>
				<?php if(isset($audio_path) && !empty($audio_path)): ?>
					<div class="pl-4">
					<audio src="<?= e(base_url.$audio_path) ?>" controls></audio>
					</div>
					<div class="pl-4">
					<a href="<?= e(base_url.$audio_path) ?>" target="_blank" rel="noopener noreferrer"><?= e(pathinfo($audio_path, PATHINFO_FILENAME).'.'.pathinfo($audio_path, PATHINFO_EXTENSION)) ?></a>

					</div>
				<?php else: ?>
					<div class="pl-4"><span class="text-muted">No Audio File Added.</span></div>
				<?php endif; ?>
			</div>
			<div class="form-group">
				<label for="status" class="control-label">Status</label>
				<div class="pl-4"><span class="badge <?= isset($status) && $status == 1 ? 'badge-success' : 'badge-danger' ?> rounded-pill px-4"><?= isset($status) && $status == 1 ? 'Active' : 'Inactive' ?></span></div>
			</div>
		</div>
	</div>
	
</div>
<script>
	$(document).ready(function(){
		$('#delete_data').click(function(){
			_conf("Are you sure you want to delete this music?", "delete_music", [<?= (int) $id ?>]);
		});
	})
	
	function delete_music($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_music",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.replace(<?= json_encode(base_url . 'admin/?page=musics', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>
