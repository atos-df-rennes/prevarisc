document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'affichage de l'icône de visibilité
    $('span > i').on('click', function() {
        if ($(this).hasClass('icon-eye-open')) {
            this.setAttribute('title', 'Couche non visible')
            $(this).closest('div').next().val(1)
        } else {
            this.setAttribute('title', 'Couche visible')
            $(this).closest('div').next().val(0)
        }

        $(this).toggleClass('icon-eye-close icon-eye-open')
    })
})

function initViewer(divId, ignKeys, center, description, autoconfPath) {
    viewer = Gp.Map.load(
        divId, // identifiant du conteneur HTML
        {
            apiKey : ignKeys,
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

function addUserLayers(viewer, ignKey, layers) {
    const wmsLayers = layers.filter(layer => (layer.TYPE_COUCHECARTO === 'WMS'))
    if (wmsLayers && wmsLayers.length > 0) {
        addWmsLayers(viewer, wmsLayers)
    }

    const wmtsLayers = layers.filter(layer => (layer.TYPE_COUCHECARTO === 'WMTS'))
    if (wmtsLayers && wmtsLayers.length > 0) {
        addWmtsLayers(viewer, wmtsLayers, ignKey)
    }

    return viewer;
}

function addWmsLayers(viewer, wmsLayers) {
    // Ajout des couches WMS
    for (let i = 0; i < wmsLayers.length; i++) {
        const source = new ol.source.TileWMS({
            url: wmsLayers[i].URL_COUCHECARTO,
            params: {
                'LAYERS': wmsLayers[i].LAYERS_COUCHECARTO,
                'FORMAT': wmsLayers[i].FORMAT_COUCHECARTO,
                'TILED': true
            }
        })

        const layer = new ol.layer.Tile({
            source: source,
            visible: wmsLayers[i].TRANSPARENT_COUCHECARTO === 1 ? false : true
        })

        viewer.getLibMap().addLayer(layer);

        // On renomme les couches utilisateurs
        $('.GPlayerName').eq(-(viewer.getLibMap().getLayers().getLength())).text(wmsLayers[i].NOM_COUCHECARTO)
        .attr('title', wmsLayers[i].NOM_COUCHECARTO)
    }
}

function addWmtsLayers(viewer, wmtsLayers, ignKey) {
    const wmtsCapabilities = getCapabilities(ignKey, 'wmts')

    // Projection EPSG:3857
    const resolutions = [
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
    
    // Ajout des couches WMTS avec les données utilisateurs renseignées en base
    for (let i = 0; i < wmtsLayers.length; i++) {
        // Données issues du getCapabilities correspondant à la couche renseignée par l'utilisateur
        // Permet d'avoir des informations complémentaires non renseignées par l'utilisateur pour l'ajout de la couche
        const wmtsLayer = wmtsCapabilities.find(wmtsCapability => wmtsCapability.internalName === wmtsLayers[i].LAYERS_COUCHECARTO)

        const source = new ol.source.WMTS({
            url: wmtsLayers[i].URL_COUCHECARTO,
            layer: wmtsLayers[i].LAYERS_COUCHECARTO,
            matrixSet: wmtsLayer.matrixSet,
            format: wmtsLayers[i].FORMAT_COUCHECARTO,
            tileGrid: new ol.tilegrid.WMTS({
                origin: wmtsLayer.origin,
                resolutions: resolutions,
                matrixIds: wmtsLayer.matrixIds,
            }),
            style: wmtsLayer.style
        })

        const layer = new ol.layer.Tile({
            source: source,
            visible: wmtsLayers[i].TRANSPARENT_COUCHECARTO === 1 ? false : true
        })

        viewer.getLibMap().addLayer(layer);

        // On renomme les couches utilisateurs
        $('.GPlayerName').eq(-(viewer.getLibMap().getLayers().getLength())).text(wmtsLayers[i].NOM_COUCHECARTO)
        .attr('title', wmtsLayers[i].NOM_COUCHECARTO)
    }
}

function getCapabilities(ignKey, format) {
    let parser = null
    const baseFormat = format.split(' ')[0]
    let urlToCall = 'https://wxs.ign.fr/' + ignKey + '/geoportail/{formatUrl}?SERVICE=' + baseFormat + '&REQUEST=GetCapabilities{options}'

    switch (format) {
        case 'wmts':
            parser = new ol.format.WMTSCapabilities()
            urlToCall = urlToCall.replace('{formatUrl}', 'wmts').replace('{options}', '')
            break
        case 'wms raster':
            parser = new ol.format.WMSCapabilities()
            urlToCall = urlToCall.replace('{formatUrl}', 'r/wms').replace('{options}', '&VERSION=1.3.0')
            break
        case 'wms vecteur':
            parser = new ol.format.WMSCapabilities()
            urlToCall = urlToCall.replace('{formatUrl}', 'v/wms').replace('{options}', '&VERSION=1.3.0')
            break
        default:
            console.error('Format non supporté: ' + format + '\nLes formats supportés sont: WMTS / WMS Raster / WMS Vecteur')
            return
    }

    let layersToReturn = []

    $.ajax({
        url: urlToCall,
        type: 'get',
        async: false,
        success: function (result) {
            const parsedResult = parser.read(result)

            let contents = ''
            let layerUrl = ''
            let layerOrigins = ''
            let contentMatrixSetIds =  []

            if (format === 'wmts') {
                contents = parsedResult.Contents
                layerUrl = parsedResult.OperationsMetadata.GetCapabilities.DCP.HTTP.Get[0].href.slice(0, -1)

                const contentMatrixSet = contents.TileMatrixSet
                const matrixSetToUse = contentMatrixSet.find(matrixSet => matrixSet.Identifier === 'PM')
                layerOrigins = matrixSetToUse.TileMatrix[0].TopLeftCorner

                for (let i = 0; i < matrixSetToUse.TileMatrix.length; i++) {
                    contentMatrixSetIds.push(i)
                }

                layerOrigins.forEach(function (part, index) {
                    this[index] = Math.trunc(part)
                }, layerOrigins)
            } else {
                contents = parsedResult.Capability.Layer
                layerUrl = parsedResult.Capability.Request.GetCapabilities.DCPType[0].HTTP.Get.OnlineResource.slice(0, -1)
            }

            const contentLayers = contents.Layer
            if (contentLayers === undefined) {
                return
            }

            let layers = []
            let layerFormat = null
            contentLayers.forEach(function (layer) {
                if (layer.Style === undefined && format !== 'wms vecteur') {
                    return
                } else if (layer.Style === undefined && format === 'wms vecteur') {
                    layerFormat = 'image/png'
                }

                let obj = {
                    // NOM_COUCHECARTO
                    name: layer.Title,
                    // TYPE_COUCHECARTO
                    type: baseFormat.toUpperCase(),
                    // URL_COUCHECARTO
                    url: layerUrl
                }

                if (format === 'wmts') {
                    // LAYERS_COUCHECARTO
                    obj.internalName = layer.Identifier
                    // FORMAT_COUCHECARTO
                    obj.format = layer.Format[0]
                    obj.style = layer.Style[0].Identifier
                    obj.matrixSet = layer.TileMatrixSetLink[0].TileMatrixSet
                    obj.origin = layerOrigins
                    obj.matrixIds = contentMatrixSetIds
                } else {
                    // LAYERS_COUCHECARTO
                    obj.internalName = layer.Name
                    // FORMAT_COUCHECARTO
                    obj.format = layerFormat !== null ? layerFormat : layer.Style[0].LegendURL[0].Format
                }

                layers.push(obj)
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
                projection: projection
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