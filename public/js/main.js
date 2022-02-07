document.addEventListener("DOMContentLoaded", function() {
    bindTitlePopup();
    bindContainerSize();
    bindEtsMarquee($(document));
    bindEtsPopup($(document));
}, false);

function bindTitlePopup() {
    // Titres
    $('a[title]').tipsy({live: true});
    $('abbr[title]').tipsy({live: true});
}

function bindContainerSize() {
    if ($('.ios_menu_style').is(':visible') > 0) {
        $('.main-container-fluid').css("width","80%");
        $('.main-container-fluid').css("display","table-cell");
    }
    else {
        $('.main-container-fluid').css("width","100%");
        $('.main-container-fluid').css("display","block");
    }

    $('.menu-trigger').click(function() {
        if ($('.ios_menu_style').is(':visible') > 0) {
            $('.ios_menu_style').hide();
            $('.main-container-fluid').css("width","100%");
            $('.main-container-fluid').css("display","block");
        }
        else {
            $('.ios_menu_style').show();
            $('.main-container-fluid').css("width","80%");
            $('.main-container-fluid').css("display","table-cell");
        }
    });
}

function bindEtsMarquee($elem) {
    // Marquee sur les listes de recherche
    $elem.find('ul.recherche_liste li.etablissement').each(function() {
        var li_width = $(this).width();
        var left_width = $(this).find('.pull-left').width();
        var right_width = $(this).find('.pull-right').width();
        if( (left_width + right_width) > li_width) {
            var free_width = li_width - right_width - 20;
            $(this).find('.pull-left').css('width', free_width + 'px').css('overflow', 'hidden').marquee({
                duplicated: true,
                duration: 7500,
                pauseOnHover: true
            });
        }
    });
}

function bindEtsPopup($elem) {

    // Bulle ETS
    $elem.find('a[href^="/etablissement/index/id/"]').hoverIntent({
        over: function () {
            var id = $(this).attr('href').replace("/etablissement/index/id/", "");
            var e = $(this);
            e.popover({html: true, content: "<p class='text-center'><img src='/images/load.gif'></p>"}).popover('show');
            $.getJSON("/api/1.0/etablissement?id=" + id, function(data) {
                var ets_id = data.response.general.ID_ETABLISSEMENT;
                var ets_libelle = data.response.informations.LIBELLE_ETABLISSEMENTINFORMATIONS;
                var ets_genre = data.response.informations.LIBELLE_GENRE;
                var ets_type = data.response.informations.LIBELLE_TYPE_PRINCIPAL;
                var ets_statut = data.response.informations.LIBELLE_STATUT;
                var ets_cat = data.response.informations.ID_GENRE == 3 ? data.response.parents[0].LIBELLE_CATEGORIE : data.response.informations.LIBELLE_CATEGORIE;
                var ets_adresse = data.response.adresses[0];
                data.response.parents.forEach(function(element, index, array) {
                    array[index] = element.LIBELLE_ETABLISSEMENTINFORMATIONS;
                });
                var ets_parents = data.response.parents.join(' - ');
                data.response.adresses.forEach(function(element, index, array) {
                    array[index] = element.LIBELLE_COMMUNE;
                });

                if(data.response.informations.ID_GENRE == 1) {
                    var ets_adresses = "";
                    data.response.etablissement_lies.forEach(function(element, index, array) {
                        if (element.LIBELLE_COMMUNE_ADRESSE_DEFAULT != null && ets_adresses == "") {
                            ets_adresses = element.LIBELLE_COMMUNE_ADRESSE_DEFAULT;
                        }
                    });
                }
                else {
                    var ets_adresses = data.response.adresses.join(' - ');
                }

                html = "";
                if(ets_parents != '') html += "<span>" + ets_parents + "</span><br>";
                html += "<span class='lead'><strong>";
                if(ets_type != null) html+= "<img src='/images/types/b/icone-type-" + ets_type + ".png'>&nbsp;";
                html += ets_libelle + "</strong></span>";
                html += "&nbsp;<span><small>" + ets_adresses + "</small></span>";
                html += "<br /><span><small>#" + data.response.general.NUMEROID_ETABLISSEMENT + "</small></span>";

                if(data.response.presence_avis_differe === true) {
                    html += "<br><br><p class='avis' style='background-color: #3a87ad; font-size: .7em; float: none'>Avis différé</p>";
                }
                else if(data.response.avis == 1) {
                    html += "<br><br><p class='avis F' style='font-size: .7em; float: none'>Favorable" + (data.response.informations.ID_GENRE == 3 ? '' : ' à l\'exploitation') + "</p>";
                }
                else if(data.response.avis == 2) {
                    html += "<br><br><p class='avis D' style='font-size: .7em; float: none'>Défavorable" + (data.response.informations.ID_GENRE == 3 ? '' : ' à l\'exploitation') + "</p>";
                }
                else if(data.response.informations.ID_GENRE != 1) {
                    html += "<br><br><p class='avis' style='font-size: .7em; ; float: none'>Avis d'exploitation indisponible</p>";
                }

                if(ets_cat != null && ets_type != null) html += "<br><span>" + ets_cat + " - " + ets_type + "</span>";

                if(ets_adresse != null) html += "<br><span>" + (ets_adresse.NUMERO_ADRESSE == null ? '' : ets_adresse.NUMERO_ADRESSE) + " " + ets_adresse.LIBELLE_RUE + " " + ets_adresse.CODEPOSTAL_COMMUNE + " " + ets_adresse.LIBELLE_COMMUNE + "</span>";

                html += "<br><br>";
                html += "<a href='/etablissement/index/id/" + ets_id + "' class='btn btn-small btn-primary btn-block'>Voir la fiche</a>";

                e.popover('destroy');
                e.popover({html: true, content: html}).popover('show');
            });
        },
        out: function() {
            var e = $(this);
            if($('.popover:hover').length === 0 && $('a[href^="etablissement/index/id/"]:hover').length === 0) {
                e.popover('destroy');
            }
            else {
                $('.popover').mouseleave(function() {
                    e.popover('destroy');
                });
            }
        },
        interval: 1000,
        timeout: 500
    });
};

function loadBloc($bloc) {
    $bloc.find('.panel-body').show();
    $bloc.removeClass('empty').addClass('loading');
    $.ajax({
        type: 'post',
        url: "/index/bloc",
        data: {id: $bloc.attr('id')},
        success: function(data) {
            $bloc.find(".panel-body").html(data);
            $container.packery('fit', $bloc.get(0));
            bindEtsMarquee($bloc);
            bindEtsPopup($bloc);
            $bloc.removeClass('loading').addClass('loaded');
        }
    });
};

function initViewer(divId, ignKey, center, description, autoconfPath) {
    viewer = Gp.Map.load(
        divId, // identifiant du conteneur HTML
        {
            apiKey : ignKey,
            configUrl : autoconfPath,
            // chargement de la cartographie en 2D
            viewMode : "2d",
            // niveau de zoom de la carte (de 1 à 21)
            zoom : 16,
            // centrage de la carte
            center : {
                x : center[0],
                y : center[1],
                projection : "EPSG:4326"
            },
            // Outils additionnels à proposer sur la carte
            controlsOptions : {
                "layerSwitcher" : {},
                "search" : {},
                "orientation" : {},
                "graphicscale" : {},
                "graticule" : {},
                "length" : {},
                "area" : {},
                "azimuth" : {}
            },
            markersOptions : [{
                content : description
            }]
        }
    );

    return viewer;
}

// FIXME Ajouter les couches au format WFS & WMTS
function addUserLayers(viewer, ignKey, layers) {
    const wmsLayers = layers.filter(layer => (layer.TYPE_COUCHECARTO === 'WMS'))
    addWmsLayers(viewer, ignKey, wmsLayers)

    const wfsLayers = layers.filter(layer => (layer.TYPE_COUCHECARTO === 'WFS'))
    // TODO addWfsLayers

    const wmtsLayers = layers.filter(layer => (layer.TYPE_COUCHECARTO === 'WMTS'))
    // TODO addWmtsLayers
    addWmtsLayers(viewer, ignKey, wmtsLayers)

    return viewer;
}

function addWmsLayers(viewer, ignKey, wmsLayers) {
    // Ajout des couches WMS
    if (wmsLayers && wmsLayers.length > 0) {
        for (let i = 0; i < wmsLayers.length; i++) {
            const source = new ol.source.TileWMS({
                url: wmsLayers[i].URL_COUCHECARTO.replace('\{key\}', ignKey),
                params: {
                    'LAYERS': wmsLayers[i].LAYERS_COUCHECARTO,
                    'FORMAT': wmsLayers[i].FORMAT_COUCHECARTO,
                    'TILED': true
                }
            })

            const layer = new ol.layer.Tile({
                source: source,
                visible: wmsLayers[i].TRANSPARENT_COUCHECARTO == 0 ? false : true
            })

            viewer.getLibMap().addLayer(layer);

            // On renomme les couches utilisateurs
            $('.GPlayerName').eq(-(viewer.getLibMap().getLayers().getLength())).text(wmsLayers[i].NOM_COUCHECARTO)
            .attr('title', wmsLayers[i].NOM_COUCHECARTO)
        }
    }
}

function addWmtsLayers(viewer, ignKey, wmtsLayers) {
    const wmtsCapabilities = getCapabilities(ignKey, 'wmts')

    // Projection EPSG:3857
    var resolutions = [
            156543.03392804103,
            78271.5169640205,
            39135.75848201024,
            19567.879241005125,
            9783.939620502562,
            4891.969810251281,
            2445.9849051256406,
            1222.9924525628203,
            611.4962262814101,
            305.74811314070485,
            152.87405657035254,
            76.43702828517625,
            38.218514142588134,
            19.109257071294063,
            9.554628535647034,
            4.777314267823517,
            2.3886571339117584,
            1.1943285669558792,
            0.5971642834779396,
            0.29858214173896974,
            0.14929107086948493,
            0.07464553543474241
    ];

    // // Ajout des couches WMTS
    if (wmtsLayers && wmtsLayers.length > 0) {
        for (let i = 0; i < wmtsLayers.length; i++) {
            const source = new ol.source.WMTS({
                url: wmtsLayers[i].URL_COUCHECARTO.replace('\{key\}', ignKey),
                layer: wmtsLayers[i].LAYERS_COUCHECARTO,
                matrixSet: wmtsCapabilities[wmtsLayers[i].LAYERS_COUCHECARTO].matrixSet,
                format: wmtsLayers[i].FORMAT_COUCHECARTO,
                tileGrid: new ol.tilegrid.WMTS({
                    origin: wmtsCapabilities[wmtsLayers[i].LAYERS_COUCHECARTO].origin,
                    resolutions: resolutions,
                    matrixIds: wmtsCapabilities[wmtsLayers[i].LAYERS_COUCHECARTO].matrixIds,
                }),
                style: wmtsCapabilities[wmtsLayers[i].LAYERS_COUCHECARTO].style
            })

            const layer = new ol.layer.Tile({
                source: source,
                visible: wmtsLayers[i].TRANSPARENT_COUCHECARTO == 0 ? false : true
            })

            viewer.getLibMap().addLayer(layer);

            // On renomme les couches utilisateurs
            $('.GPlayerName').eq(-(viewer.getLibMap().getLayers().getLength())).text(wmtsLayers[i].NOM_COUCHECARTO)
            .attr('title', wmtsLayers[i].NOM_COUCHECARTO)
        }
    }
}

function getCapabilities(ignKey, format) {
    var layersToReturn = null

    $.ajax({
        url: 'https://wxs.ign.fr/' + ignKey + '/geoportail/' + format + '?SERVICE=' + format + '&REQUEST=GetCapabilities',
        type: 'get',
        async: false,
        success: function (result) {
            var result = $(result)
            var layers = []

            result.find('Layer').each(function (index, layer) {
                var layer = $(layer)
                var layerFormat = layer.find('Format').text()
                
                if (layerFormat !== 'image/png' && layerFormat !== 'image/jpeg') {
                    return
                }
                
                var layerName = layer.find('ows\\:Identifier:first').text()
                var layerStyle = layer.find('Style:first').find('ows\\:Identifier').text()
                var layerMatrixSet = layer.find('TileMatrixSet').text()
                var layerOrigins = null
                var layerMatrixIds = []

                result.find('TileMatrixSet').each(function (index, tileMatrixSet) {
                    var tileMatrixSet = $(tileMatrixSet)
                    var projectionIdentifier = tileMatrixSet.find('ows\\:Identifier:first').text()
                    var projection = tileMatrixSet.find('ows\\:SupportedCRS').text()
    
                    // FIXME Passer la couche en paramètre ?
                    if (projection === 'EPSG:3857' && projectionIdentifier === 'PM') {
                        layerOrigins = tileMatrixSet.find('TileMatrix:first').find('TopLeftCorner').text()
                        layerOrigins = layerOrigins.split(' ')

                        layerOrigins.forEach(function (part, index) {
                            this[index] = Math.trunc(part)
                        }, layerOrigins)

                        var nbLayerMatrixIds = tileMatrixSet.find('TileMatrix').length
                        for (var i = 0; i < nbLayerMatrixIds; i++) {
                            layerMatrixIds.push(i)
                        }
                    }
                })

                layers[layerName] = {
                    style: layerStyle,
                    matrixSet: layerMatrixSet,
                    origin: layerOrigins,
                    matrixIds: layerMatrixIds
                }
            })

            layersToReturn = layers
        }
    })

    return layersToReturn
}

function putMarkerAt(viewer, center, nbCouches) {
    // Si on a déjà un marker, on le retire
    if (viewer.getLayers().getLength() != nbCouches) {
        var toRemove = viewer.getLayers().item(viewer.getLayers().getLength()-1);
        viewer.removeLayer(toRemove);
    }

    var coordinates = ol.proj.fromLonLat([center[0],center[1]]);
    var point = new ol.geom.Point(coordinates);
    var marker = new ol.Feature(point);

    // On crée le nouveau marker aux coordonnées indiquées
    var vectorSource = new ol.source.Vector({
        features: [marker]
    });
    var styleMarker = new ol.style.Style({
        image: new ol.style.Icon({
            src: "/images/red-dot.png"
        })
    });
    var vectorLayer = new ol.layer.Vector({
        source: vectorSource,
        style: styleMarker
    });
    viewer.addLayer(vectorLayer);

    // On renomme la couche
    $('.GPlayerName').eq(-(viewer.getLayers().getLength())).text('Position du marqueur');
}

function updateCoordinates(center, sourceProj, destProj) {
    var lonlat = new ol.proj.transform(center, sourceProj, destProj);
    $("input[name='lon']").val(lonlat[0]);
    $("input[name='lat']").val(lonlat[1]);

    return lonlat;
}

function geocodeWithJsAutoconf(apiKey, adresse, filterOptionsType, projection, viewer, nbCouches) {
    Gp.Services.geocode({
        apiKey: apiKey,
        location: adresse,
        filterOptions: [{
            type: filterOptionsType
        }],
        srs: projection,
        onSuccess: function(t) {
            var newCenter = {
                x: t.locations[0].position.y,
                y: t.locations[0].position.x,
                projection: "EPSG:4326"
            };
            viewer.setCenter(newCenter);
            $("span.result").text("Géolocalisée IGN");
            $('#geoportail-container').css('visibility', 'visible');
            // Changement des coordonnées et du marker 
            lonlat = updateCoordinates([viewer.getCenter().x, viewer.getCenter().y], 'EPSG:3857', 'EPSG:4326');
            putMarkerAt(viewer.getLibMap(), lonlat, nbCouches);
        },
        onFailure: function() {
            console.log('Erreur du service de géocodage ! Veuillez réessayer');
        }
    });
}