<?php 
                
h2('LatLng class');
h3('Constructor');
// Create LatLng objects
foreach ($places as $name => $coords) {
    $$name = new LatLng($coords['lat'], $coords['lng']);
    p($name .' => lat: '. $$name->lat() .', lng: '. $$name->lng());
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

h2('Spherical geometry static class');
h3('computeArea(london, donostia, newyork)');
p(float_to_string(Spherical::computeArea(array($london, $donostia, $newyork))));

h3('computeSignedArea(london, donostia, newyork)');
p(float_to_string(Spherical::computeSignedArea(array($london, $donostia, $newyork))));

h3('computeDistanceBetween(london, newyork)');
p(float_to_string(Spherical::computeDistanceBetween($london, $newyork)));

h3('computeHeading(london, newyork)');
p(float_to_string(Spherical::computeHeading($london, $newyork)));

h3('computeLength(london, newyork, moscow, sydney)');
p(float_to_string(Spherical::computeLength(array($london, $newyork, $moscow, $sydney))));

h3('computeOffset(london, 5576353.232683, -71.669371)');
p(Spherical::computeOffset($london, 5576353.232683, -71.669371)->toString());

h3('interpolate(newyork, sydney, 0.7)');
p(Spherical::interpolate($newyork, $sydney, 0.7)->toString());
