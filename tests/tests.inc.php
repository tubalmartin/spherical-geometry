<?php

h2('LatLng class');
h3('Constructor');
// Create LatLng objects
foreach ($places as $name => $coords) {
    $$name = new LatLng($coords['lat'], $coords['lng']);
    p($name .' => lat: '. $$name->getLat() .', lng: '. $$name->getLng());
}

h3('toString()');
p($sydney->toString());

h3('toUrlValue()');
p($sydney->toUrlValue());
h3('toUrlValue(3)');
p($sydney->toUrlValue(3));

h3('donostia equals(donostia)');
p($donostia->equals($donostia));
h3('donostia equals(buenosaires)');
p($donostia->equals($buenosaires));

h2('LatLngBounds class');
h3('Constructor (buenosaires, moscow)');
 
$bounds  = new LatLngBounds($buenosaires, $moscow);
$bounds2 = new LatLngBounds($sydney, $newyork); 
p($bounds->toString());


h3('getCenter()');
p($bounds->getCenter()->toString());

h3('getSouthWest()');
p($bounds->getSouthWest()->toString());

h3('getNorthEast()');
p($bounds->getNorthEast()->toString());

h3('contains(moscow)');
p($bounds->contains($moscow));
h3('contains(donostia)');
p($bounds->contains($donostia));
h3('contains(sydney)');
p($bounds->contains($sydney));

h3('equals((buenosaires, moscow))');
p($bounds->equals($bounds));
h3('equals((sydney, newyork))');
p($bounds->equals($bounds2));

h3('Sw only constructor (london)');
$bounds = new LatLngBounds($london); p($bounds->toString());

h3('empty constructor ( , )');
$bounds = new LatLngBounds(); p($bounds->toString());

h3('isEmpty()');
p($bounds->isEmpty());

h3('extend(london, donostia, newyork)');
p($bounds->extend($london)->toString());
p($bounds->extend($donostia)->toString());
p($bounds->extend($newyork)->toString());

h3('isEmpty()');
p($bounds->isEmpty());

h3('toSpan()');
p($bounds->toSpan()->toString());

h3('toUrlValue()');
p($bounds->toUrlValue());

h3('intersects(sydney, newyork)');
p($bounds->intersects($bounds2));

h3('union(sydney, newyork)');
p($bounds->union($bounds2)->toString());

h2("LatLngBounds class: maximum bounds in GMaps");
$bounds3 = new LatLngBounds(
    new LatLng(-85.051128779807, -180),
    new LatLng(85.051128779807, 180)
);

h3("Constructor (new LatLng(-85.051128779807, -180), new LatLng(85.051128779807, 180))");
p($bounds3->toString());

h3("getCenter()");
p($bounds3->getCenter()->toString());

h3("getSouthWest()");
p($bounds3->getSouthWest()->toString());

h3("getNorthEast()");
p($bounds3->getNorthEast()->toString());

h3('contains(moscow)');
p($bounds3->contains($moscow));

h3('contains(sydney)');
p($bounds3->contains($sydney));

h3('contains(buenosaires)');
p($bounds3->contains($buenosaires));

h2('Spherical geometry static class');
h3('computeArea(london, donostia, newyork)');
p(float_to_string(SphericalGeometry::computeArea(array($london, $donostia, $newyork))));

h3('computeSignedArea(london, donostia, newyork)');
p(float_to_string(SphericalGeometry::computeSignedArea(array($london, $donostia, $newyork))));

h3('computeDistanceBetween(london, newyork)');
p(float_to_string(SphericalGeometry::computeDistanceBetween($london, $newyork)));

h3('computeHeading(london, newyork)');
p(float_to_string(SphericalGeometry::computeHeading($london, $newyork)));

h3('computeLength(london, newyork, moscow, sydney)');
p(float_to_string(SphericalGeometry::computeLength(array($london, $newyork, $moscow, $sydney))));

h3('computeOffset(london, 5576353.232683, -71.669371)');
p(SphericalGeometry::computeOffset($london, 5576353.232683, -71.669371)->toString());

h3('interpolate(newyork, sydney, 0.7)');
p(SphericalGeometry::interpolate($newyork, $sydney, 0.7)->toString());
