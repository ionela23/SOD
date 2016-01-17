<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use EasyRdf_Graph;
use EasyRdf_Namespace;

class RdfController extends Controller
{
    function test(){
        EasyRdf_Namespace::set('postcode', 'http://data.ordnancesurvey.co.uk/ontology/postcode/');
        EasyRdf_Namespace::set('sr', 'http://data.ordnancesurvey.co.uk/ontology/spatialrelations/');
        EasyRdf_Namespace::set('eg', 'http://statistics.data.gov.uk/def/electoral-geography/');
        EasyRdf_Namespace::set('ag', 'http://statistics.data.gov.uk/def/administrative-geography/');
        EasyRdf_Namespace::set('osag', 'http://data.ordnancesurvey.co.uk/ontology/admingeo/');

        $postcode = "W1A 1AA";
        $postcode = str_replace(' ', '', strtoupper($postcode));
        $docuri = "http://www.uk-postcodes.com/postcode/$postcode.rdf";
        $graph = EasyRdf_Graph::newAndLoad($docuri, 'rdfxml');
        dd($graph);
    }
}
