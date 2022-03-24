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
                    <div class="card-body">
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
                                                <a class="btn btn-primary btn-sm"><i class="fa fa-solid fa-pencil"></i></a>
                                                <a class="btn btn-danger btn-sm"><i class="fa fa-solid fa-trash-can"></i></a>
                                                <a class="btn btn-warning btn-sm"><i class="fa fa-solid fa-eye"></i></a>
                                            </td>
                                            <td><?= $geojson['shp_name'] ?></td>
                                            <td class="text-center">
                                                <span class="fa fa-solid fa-clock"></span>
                                                <?= date('h:i A',strtotime($geojson['dt_added'])) ?><br>
                                                <span class="fa fa-calendar"></span>&nbsp;
                                                <?= date('d/m/Y',strtotime($geojson['dt_added'])) ?>
                                            </td>
                                            <td class="text-center"><span class="badge bg-success">Active</span></td>
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
</html>

<script>
    var baseURL = '<?= base_url() ?>';
    $.ajax({
        url: `${baseURL}gis/geojson_data`,
        dataType: 'JSON',
        success:function(response){

            loadMaps(response);
        }
    });

    function loadMaps(response){
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

        if (response != '') {
            var kedahData = JSON.parse(response);
            var lineData = L.geoJSON(kedahData).addTo(map);     
        }
    }

</script>