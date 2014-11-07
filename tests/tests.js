h2("google.maps.LatLng class");
h3("Constructor");
for (var name in places) {
    window[name] = new google.maps.LatLng(places[name].lat, places[name].lng);
    p(name +" => lat: "+ window[name].lat() +", lng: "+ window[name].lng());
}

h3("toString()");
p(sydney.toString());

h3("toUrlValue()");
p(sydney.toUrlValue());
h3("toUrlValue(3)");
p(sydney.toUrlValue(3));

h3("donostia equals(donostia)");
p(donostia.equals(donostia));
h3("donostia equals(buenosaires)");
p(donostia.equals(buenosaires));

h2("google.maps.LatLngBounds class");
h3("Constructor (buenosaires, moscow)");
bounds  = new google.maps.LatLngBounds(buenosaires, moscow);
bounds2 = new google.maps.LatLngBounds(sydney, newyork);
p(bounds);

h3("getCenter()");
p(bounds.getCenter());

h3("getSouthWest()");
p(bounds.getSouthWest());

h3("getNorthEast()");
p(bounds.getNorthEast());

h3("contains(moscow)");
p(bounds.contains(moscow));
h3("contains(donostia)");
p(bounds.contains(donostia));
h3("contains(sydney)");
p(bounds.contains(sydney));

h3("equals((buenosaires, moscow))");
p(bounds.equals(bounds));
h3("equals((sydney, newyork))");
p(bounds.equals(bounds2));

h3("Sw only constructor(london)");
p((bounds = new google.maps.LatLngBounds(london)));

h3("empty constructor( , )");
p((bounds = new google.maps.LatLngBounds()));

h3("isEmpty()");
p(bounds.isEmpty());

h3("extend(london, donostia, newyork)");
p(bounds.extend(london));
p(bounds.extend(donostia));
p(bounds.extend(newyork));

h3("isEmpty()");
p(bounds.isEmpty());

h3("toSpan()");
p(bounds.toSpan());

h3("toUrlValue()");
p(bounds.toUrlValue());

h3("intersects(sydney, newyork)");
p(bounds.intersects(bounds2));

h3("union(sydney, newyork)");
p(bounds.union(bounds2));

h2("LatLngBounds class: maximum bounds in GMaps");
bounds3 = new google.maps.LatLngBounds(
    new google.maps.LatLng(-85.051128779807, -180),
    new google.maps.LatLng(85.051128779807, 180)
);
h3("Constructor (new google.maps.LatLng(-85.051128779807, -180), new google.maps.LatLng(85.051128779807, 180))")
p(bounds3);

h3("getCenter()");
p(bounds3.getCenter());

h3("getSouthWest()");
p(bounds3.getSouthWest());

h3("getNorthEast()");
p(bounds3.getNorthEast());

h3("contains(moscow)");
p(bounds3.contains(moscow));

h3("contains(sydney)");
p(bounds3.contains(sydney));

h3("contains(buenosaires)");
p(bounds3.contains(buenosaires));

h2("google.maps.geometry.spherical library");
h3("computeArea(london, donostia, newyork)");
p(spherical.computeArea([london, donostia, newyork]));

h3("computeSignedArea(london, donostia, newyork)");
p(spherical.computeSignedArea([london, donostia, newyork]));

h3("computeDistanceBetween(london, newyork)");
p(spherical.computeDistanceBetween(london, newyork));

h3("computeHeading(london, newyork)");
p(spherical.computeHeading(london, newyork));

h3("computeLength(london, newyork, moscow, sydney)");
p(spherical.computeLength([london, newyork, moscow, sydney]));

h3("computeOffset(london, 5576353.232683, -71.669371)");
p(spherical.computeOffset(london, 5576353.232683, -71.669371));

h3("interpolate(newyork, sydney, 0.7)");
p(spherical.interpolate(newyork, sydney, 0.7));