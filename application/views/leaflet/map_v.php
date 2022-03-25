<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
   integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
   crossorigin=""/>
   <link rel="stylesheet" href="<?= base_url() ?>css/style.css?v1.00">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
    integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
    crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Maps</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6" style="height: 50vh !important;">
                <div class="card mt-5">
                    <div class="card-body">
                        <form method="POST" action="<?= base_url() ?>gis/upload_file" enctype="multipart/form-data">
                            <label for="">Upload Shapefile :</label>
                            <input type="file" class="form-control" name="files[]" multiple required>
                            <button class="btn btn-success btn-sm float-end mt-4">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <div id="map" class="d-flex align-items-center justify-content-center">
                            <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Geojson Data
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <th class="text-center">#</th>
                                <th class="text-center" style="width: 10em;">Action</th>
                                <th>Shapefile</th>
                                <th class="text-center" style="width: 12em;">Date Added</th>
                                <th class="text-center" style="width: 5em;">Status</th>
                            </thead>
                            <tbody>
                                <?php if(isset($geojson_data)): $x = 0; ?>
                                    <?php foreach($geojson_data as $geojson): ?>
                                        <tr>
                                            <td class="text-center"><?= $x += 1; ?></td>
                                            <td class="text-center">
                                                <a class="btn btn-primary btn-sm" onclick="view_lat_long('<?= $geojson['geojson_id'] ?>')"><i class="fa fa-solid fa-location-crosshairs"></i></a>
                                                <a class="btn <?= $geojson['geojson_status'] == 1 ? 'btn-warning' : 'btn-secondary' ?> btn-sm" onclick="active_inactive_confirmation('<?= $geojson['geojson_id'] ?>',<?= $geojson['geojson_status'] ?>)"><i class="<?= $geojson['geojson_status'] == 1 ? 'fa fa-solid fa-eye' : 'fa fa-solid fa-eye-slash' ?>"></i></a>
                                                <a class="btn btn-danger btn-sm" onclick="delete_confirmation('<?= $geojson['geojson_id'] ?>')"><i class="fa fa-solid fa-trash-can"></i></a>
                                            </td>
                                            <td><?= $geojson['shp_name'] ?></td>
                                            <td class="text-center">
                                                <span class="fa fa-solid fa-clock"></span>
                                                <?= date('h:i A',strtotime($geojson['dt_added'])) ?><br>
                                                <span class="fa fa-calendar"></span>&nbsp;
                                                <?= date('d/m/Y',strtotime($geojson['dt_added'])) ?>
                                            </td>
                                            <td class="text-center">
                                                <?= geojson_status($geojson['geojson_status']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5">No data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<div class="modal fade modal-lat-long" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">GeoJSON Viewer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" cols="30" rows="20" readonly></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

</html>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var baseURL = '<?= base_url() ?>';

    $(function(){
        load_geojson();
    });

    var map = L.map('map').setView([6.0, 100.4], 9);
    var popup = L.popup();
    const tileURL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    const attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';
    const tiles = L.tileLayer( tileURL, { attribution });
    tiles.addTo( map );

    function onMapClick(e) {
        popup
            .setLatLng(e.latlng)
            .setContent("You clicked the map at " + e.latlng.toString())
            .openOn(map);
    }
    map.on('click', onMapClick);
    
    function load_geojson(){
        $.ajax({
            url: `${baseURL}gis/geojson_data`,
            dataType: 'JSON',
            success:function(response){
                response.forEach(element => {
                    loadMaps(element.data);
                });
            }
        });
    }

    function loadMaps(response){

        if (response != '') {
            var kedahData = JSON.parse(response);
            var lineData = L.geoJSON(kedahData).addTo(map);     
        }
    }

    function delete_confirmation(id){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
            if (result.isConfirmed) {
                delete_geojson(id);
            }
            })
    }

    function delete_geojson(id){
        $.ajax({
            url: `${baseURL}gis/delete_geojson`,
            data: {geojson_id:id},
            type: 'POST',
            dataType: 'JSON',
            success:function(response){
                if (response == 'success') {
                    Swal.fire(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                    ).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    })
                }else{
                    Swal.fire(
                    'Attention!',
                    'Your file error when deleting.',
                    'error'
                    )
                }
            }
        });
    }

    function active_inactive_confirmation(id,geojson_status){
        var text = geojson_status == 1 ? 'activate' : 'deactivate';
        var confirm_button = geojson_status == 1 ? 'deactivate' : 'activate';
        Swal.fire({
            title: 'Are you sure?',
            text: `You can ${text} again later!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${confirm_button} it!`
            }).then((result) => {
            if (result.isConfirmed) {
                update_geojson_status(id,geojson_status);
            }
            })
    }

    function update_geojson_status(id,geojson_status){
        var confirm_text = geojson_status == 1 ? 'deactivate' : 'activate';

        $.ajax({
            url: `${baseURL}gis/update_geojson_status`,
            data: {geojson_id:id,geojson_status:geojson_status},
            type: 'POST',
            dataType: 'JSON',
            success:function(response){
                if (response == 'success') {
                    Swal.fire(
                    'Deleted!',
                    `Your file has been ${confirm_text}.`,
                    'success'
                    ).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    })
                }else{
                    Swal.fire(
                    'Attention!',
                    `Your file error when ${confirm_text}.`,
                    'error'
                    )
                }
            }
        });
    }

    function view_lat_long(id){
        $.ajax({
            url: `${baseURL}gis/view_geojson_lat_long`,
            data: {geojson_id:id},
            type: 'POST',
            dataType: 'JSON',
            success:function(response){
                $('.modal-lat-long').find('textarea').text(JSON.stringify(JSON.parse(response),null,2));
                $('.modal-lat-long').modal('show');
            }
        });
    }

</script>