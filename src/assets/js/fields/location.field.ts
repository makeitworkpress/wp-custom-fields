/**
 * Our location field
 * @param {HTMLElement} framework The parent framework element
 */
declare namespace google {
    namespace maps {

        class LatLng {
            constructor(lat: number, lng: number);
        }        

        class Map {
            constructor(canvas: HTMLElement, options: MapOptions);
            fitBounds(viewport: any)
            setCenter(location: any)
            setZoom(zoom: number)
        } 

        enum MapTypeId {
            ROADMAP
        }       

        interface MapOptions {
            scrollwheel: boolean;
            center: LatLng;
            zoom: number;
            mapTypeId: MapTypeId;
        }        
        
        class Marker {
            constructor(options: MarkerOptions);
            setPosition(latLng: LatLng)
        }

        interface MarkerOptions {
            map: Map;
            draggable: boolean;
        } 
        
        namespace event {}
        namespace places {}

    }
}

export const LocationField = (framework: HTMLElement) => {
    
    framework.querySelectorAll('.wpcf-location').forEach((locationElement: Element) => {
        const searchInput = locationElement.querySelector('.wpcf-map-search') as HTMLInputElement,
            mapCanvas = locationElement.querySelector('.wpcf-map-canvas') as HTMLElement,
            latitude = locationElement.querySelector('.latitude') as HTMLInputElement,
            longitude = locationElement.querySelector('.longitude') as HTMLInputElement,
            city = locationElement.querySelector('.city') as HTMLInputElement,
            country = locationElement.querySelector('.country') as HTMLInputElement,
            zip = locationElement.querySelector('.postal_code') as HTMLInputElement,
            street = locationElement.querySelector('.street') as HTMLInputElement,
            state = locationElement.querySelector('.state') as HTMLInputElement,
            number = locationElement.querySelector('.number') as HTMLInputElement;
        
        let latLng = new google.maps.LatLng(52.2129918, 5.2793703),
            zoom = 7;
    
        // Map
        if (latitude.value && longitude.value) {
            latLng = new google.maps.LatLng(parseFloat(latitude.value), parseFloat(longitude.value));
            zoom = 15;
        }
    
        // Map Options
        const mapOptions: google.maps.MapOptions = {
            scrollwheel: false,
            center: latLng,
            zoom: zoom,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        const map = new google.maps.Map(mapCanvas, mapOptions);
        const markerOptions: google.maps.MarkerOptions = {
            map: map,
            draggable: false,
        };
        const marker = new google.maps.Marker(markerOptions);
        
        // @ts-ignore
        const autocomplete = new google.maps.places.Autocomplete(searchInput, {
            types: ['geocode']
        });
    
        if (latitude.value.length > 0 && longitude.value.length > 0) {
            marker.setPosition(latLng);
        }
    
        // Search
        autocomplete.bindTo('bounds', map);
    
        // @ts-ignore
        google.maps.event.addListener(autocomplete, 'place_changed', () => {
            const place = autocomplete.getPlace();
            const components = place.address_components;
    
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
    
            marker.setPosition(place.geometry.location);
            latitude.value = place.geometry.location.lat().toString();
            longitude.value = place.geometry.location.lng().toString();
    
            // Fill in our components
            if (components) {
                for (let i = 0; i < components.length; i++) {
                    const component = components[i];
                    const types = component.types;
    
                    if (types.includes('street_number')) {
                        number.value = component.long_name;
                    } else if (types.includes('route')) {
                        street.value = component.long_name;
                    } else if (types.includes('locality')) {
                        city.value = component.long_name;
                    } else if (types.includes('postal_code')) {
                        zip.value = component.long_name;
                    } else if (types.includes('administrative_area_level_1')) {
                        state.value = component.long_name;
                    } else if (types.includes('country')) {
                        country.value = component.long_name;
                    }
                }
            }
        });
    });
};