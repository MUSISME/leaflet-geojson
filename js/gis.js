
    $(function(){
        load_geojson();
    });

    var map = L.map('map').setView([6.0, 100.4], 9);
    var popup = L.popup();
    const tileURL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    const attribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';
    const tiles = L.tileLayer( tileURL, { attribution });
    tiles.addTo( map );

    var CyclOSM = L.tileLayer('https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });

    var Esri_WorldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 17
    });

    var Esri_WorldStreetMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012'
    });

    var baseMaps = {
        "<span>OpenStreetMap</span>": tiles,
        "OSM": CyclOSM,
        "Street": Esri_WorldStreetMap,
        "World 3D": Esri_WorldImagery,
    };

    var overlays =  {//add any overlays here

        };

    L.control.layers(baseMaps,overlays).addTo(map);

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

        if (isJSON(response)) {
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
                    'Attention!',
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
                var x = $('.modal-lat-long');
                if (isJSON(response)) {
                    x.find('textarea').text(JSON.stringify(JSON.parse(response),null,2));
                    x.find('.geojson_id').val(id);
                    x.modal('show');    
                }else{
                    x.find('.geojson_id').val(id);
                    x.find('textarea').val(response);
                    x.modal('show'); 
                }
            }
        });
    }

    function edit_lat_long(e){
        var x = $('.modal-lat-long');
        var edit_status = $(e).text();
        if (edit_status == 'Edit') {
            $(e).text('Update');
            x.find('.modal-title').text('GeoJSON Editor');
            x.find('textarea').removeAttr('readonly');
            x.find('.close-btn').hide();
        }else{
            var geojson_id = x.find('.geojson_id').val();
            var geojson = x.find('textarea').val();

            $.ajax({
            url: `${baseURL}gis/edit_geojson`,
            data: {geojson_id:geojson_id,geojson:geojson},
            type: 'POST',
            dataType: 'JSON',
            success:function(response){
                if (response == 'success') {
                    Swal.fire(
                    'Attention!',
                    `Your file has been updated.`,
                    'success'
                    ).then((result) => {
                        if (result.isConfirmed) {
                            x.find('.close-btn').show();
                            location.reload();
                        }
                    })
                }else{
                    Swal.fire(
                    'Attention!',
                    `Your file error when updated. Please try again later.`,
                    'error'
                    )
                }
            }
        });
            
        }
    }

    function isJSON(str) {
        try {
            return (JSON.parse(str) && !!str);
        } catch (e) {
            return false;
        }
    }

    $('.modal-lat-long').on('hidden.bs.modal', function () {
        $(this).find('.btn-edit').text('Edit');
        $(this).find('textarea').prop('readonly',true);
        $(this).find('.modal-title').text('GeoJSON Viewer');
        $(this).find('.close-btn').show();
    })