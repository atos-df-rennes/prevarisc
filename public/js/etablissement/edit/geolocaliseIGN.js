function geolocaliseIGN (idModal, options) {
    if($(idModal+' .modal-body').scrollTop() !== 0) {
        $(idModal+' .modal-body').scrollTop(0);
    }

    if (idModal.includes('ajout')) {
        $(idModal+' input').val('');
        $(idModal+" input[name='voie_ac'], "+idModal+" input[name='numero'], "+idModal+" input[name='complement']").val("").attr("disabled", true).blur();
        $("span.result").text("Inconnu");
    }
    
    $(`${idModal} #${options.geo_container_id}`).css('visibility', 'hidden');

    $(idModal+' #geolocme').click(function() {
        geocodeAndShowMap()
    });

    // Si une carto est déjà présente, on n'en charge pas une autre
    if($(`${idModal} #${options.geo_container_id}`+' .ol-viewport').length == 0) {
        // On empêche le clic sur la géolocalisation jusqu'à ce que la carto soit chargée
        $(idModal+' #geolocme').attr('disabled', true);
        $(idModal+' #geolocme_nominatim').attr('disabled', true);

        viewer = initViewer(
            `${options.geo_container_id}`,
            options.key_ign,
            [options.default_lon,options.default_lat],
            '<b>Centre par défaut</b>',
            options.autoconf_path
        );

        viewer.listen('mapLoaded', afterInitMap);
        viewer.listen('azimuthChanged', onRotation);

        function afterInitMap () {
            // Ajout du bouton FullScreen sur la carte
            var fsControl = new ol.control.FullScreen({});
            viewer.getLibMap().addControl(fsControl);

            // Ajout des couches utilisateur
            viewer = addUserLayers(viewer,options.couches_cartographiques);

            // On enlève le marker par défaut, les outils de mesures, et le reset d'orientation
            $('.ol-overlay-container').css('display', 'none');
            $('div[id^=GPtoolbox-measure-main-]').css('display', 'none');
            $('.ol-rotate').css('display', 'none');

            $(idModal+' #geolocme').removeAttr('disabled');
            $(idModal+' #geolocme_nominatim').removeAttr('disabled');

            if (idModal.includes('edit')) {
                geocodeAndShowMap()
            }
        };

        function onRotation () {
            viewer.getAzimuth() === 0 ? $('.ol-rotate').css('display', 'none') : $('.ol-rotate').css('display', 'block');
        };

        viewer.getLibMap().on('singleclick', function(evt) {
            lonlat = updateCoordinates(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
            putMarkerAt(viewer.getLibMap(), lonlat);
        });
    }

    function geocodeAndShowMap() {
        var adresse = "";
        var numero = $(idModal+" input[name='numero']").val().trim();
        var voie = $(idModal+" input[name='voie_ac']").val().trim();
        var codepostal = $(idModal+" input[name='code_postal']").val();
        var commune = $(idModal+" input[name='commune_ac']").val().replace(/\(.*\)/g, '');

        if (!commune) {
            $("span.result").text("Pas de commune renseignée");
            return false;
        }

        if (!voie) {
            $("span.result").text("Pas de voie renseignée");
            return false;
        }

        if (numero) {
            adresse += numero + ", ";
        }

        // On regarde si la voie contient une commune entre parenthèses
        var regExp = /\(([^)]+)\)/;
        var matches = regExp.exec(voie);

        if (matches) {
            // matches[1] contient la valeur entre parenthèses
            commune = matches[1];

            // On retire la commune de la voie
            voie = voie.split(regExp)[0].trim();
        }

        adresse += voie + ", " + codepostal +  ", " + commune;

        $("span.result").text("Géolocalisation en cours...");
        geocodeWithJsAutoconf(
            options.geo_container_id,
            adresse,
            'StreetAddress',
            'EPSG:4326',
            viewer
        );

        return false;
    }
}