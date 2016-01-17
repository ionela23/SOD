<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Google_Service_Plus;
use Google_Service_Plus_Person;
use Google_Service_YouTube;
use Google_Service_YouTube_Channel;
use Illuminate\Support\Facades\Auth;

class GraphController extends Controller
{
    function makeGraph()
    {
        $client = (new GoogleAuthController())->getGoogleClientS();
        $plus = new Google_Service_Plus($client);
        $me = $plus->people->get('me'); // dd($me);
//        dd($me->toSimpleObject());
        $list = $plus->people->listPeople('me', 'visible');
        $nextPageToken = (isset($list->nextPageToken)) ? $list->nextPageToken : null;

        $people = $list->getItems();
        $stop = false; //dd($people);

        $filepath = "graphs/".Auth::user()->id.'.xml';
        try{
            $fh = fopen($filepath,'w');
        }catch(\Exception $e){
            return null;
        }


        fwrite($fh, "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n");


        fwrite($fh, "<rdf:RDF\n");
        fwrite($fh, "\txmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n");
        fwrite($fh, "\txmlns:foaf=\"http://xmlns.com/foaf/0.1/\"\n");
        fwrite($fh, "\txmlns:rel=\"http://www.perceive.net/schemas/relationship/\"\n");
        fwrite($fh, "\txmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\"\n");
        fwrite($fh, "\txmlns:owl=\"http://www.w3.org/2002/07/owl#\"\n");
        fwrite($fh, "\txmlns:dbo=\"http://dbpedia.org/ontology/\"\n");
        fwrite($fh, "\txmlns:dbp=\"http://dbpedia.org/property/\"\n");
        fwrite($fh, ">\n");

        fwrite($fh, "\t<foaf:PersonalProfileDocument rdf:about=\"\">\n");
        fwrite($fh, "\t\t<foaf:maker rdf:resource=\"#me\"/>\n");
        fwrite($fh, "\t\t<foaf:primaryTopic rdf:resource=\"#me\"/>\n");
        fwrite($fh, "\t</foaf:PersonalProfileDocument>\n\n");

        fwrite($fh, "\t<foaf:Person rdf:ID=\"me\">\n");

        if(isset($me->displayName)){
            // foaf:name
            fwrite($fh, "\t\t<foaf:name>" . $me->displayName. "</foaf:name>\n");
        }
        if(method_exists($me, 'getName') && isset($me->getName()['givenName'])){
            // foaf:givenname
            fwrite($fh, "\t\t<foaf:givenname>" . $me->getName()['givenName'] ."</foaf:givenname>\n");
        }
        if(method_exists($me, 'getName') && isset($me->getName()['familyName'])){
            // foaf:family_name
            fwrite($fh, "\t\t<foaf:family_name>" . $me->getName()['familyName'] . "</foaf:family_name>\n");
        }
        //website
        fwrite($fh, "\t\t<foaf:homepage rdf:resource=\"" . $me->url . "\"/>\n");
        if(isset($me->gender)){
            //gender
            fwrite($fh, "\t\t<foaf:gender rdf:resource=\"" . $me->gender . "\"/>\n");
        }
        if(isset($me->occupation)){
            //occupation
            fwrite($fh, "\t\t\t\t<dbo:occupation rdf:resource=\"" . $me->occupation . "\"/>\n");
        }

        if(method_exists($me, 'getOrganizations') && count($me->getOrganizations()) > 0){
            foreach($me->getOrganizations() as $org){
             if($org->type=='school'){
                 fwrite($fh, "\t\t<dbo:school>\n");
                 fwrite($fh, "\t\t\t<dbo:EducationalInstitution dbp:name=\"" . $org->name . "\"/>\n");
                 fwrite($fh, "\t\t</dbo:school>\n");
             }
            }
            }

//        $this->addYoutubeData($client);

        do {
            foreach ($people as $person) { // identified by url https://plus.google.com/userId
                $gperson = new Google_Service_Plus_Person();
                $gperson = $person;
                $gperson = $plus->people->get($gperson->id)->toSimpleObject();
//                dd($gperson);

                if(true /*$gperson->objectType == 'person'*/){
                    fwrite($fh, "\t\t<foaf:knows>\n");
                    fwrite($fh, "\t\t\t<foaf:Person rdf:about=\"#" . $gperson->id . "\">\n");

                    if(isset($gperson->displayName)){
                        // foaf:name
                        fwrite($fh, "\t\t\t\t<foaf:name>" . $gperson->displayName. "</foaf:name>\n");
                    }
                    if(method_exists($gperson, 'getName') && isset($gperson->getName()['givenName'])){
                        // foaf:givenname
                        fwrite($fh, "\t\t\t\t<foaf:givenname>" . $gperson->getName()['givenName'] ."</foaf:givenname>\n");
                    }
                    if(method_exists($gperson, 'getName') && isset($gperson->getName()['familyName'])){
                        // foaf:family_name
                        fwrite($fh, "\t\t\t\t<foaf:family_name>" . $gperson->getName()['familyName'] . "</foaf:family_name>\n");
                    }
                    //website
                    fwrite($fh, "\t\t\t\t<foaf:homepage rdf:resource=\"" . $gperson->url . "\"/>\n");
                    if(isset($gperson->gender)){
                        //gender
                        fwrite($fh, "\t\t\t\t<foaf:gender rdf:resource=\"" . $gperson->gender . "\"/>\n");
                    }
                    if(isset($gperson->occupation)){
                        //occupation
                        fwrite($fh, "\t\t\t\t<dbo:occupation rdf:resource=\"" . $gperson->occupation . "\"/>\n");
                    }

                    if(method_exists($gperson, 'getOrganizations') && count($gperson->getOrganizations()) >0){
                        foreach($gperson->getOrganizations() as $org){
                            if($org->type=='school'){
                                fwrite($fh, "\t\t<dbo:school>\n");
                                fwrite($fh, "\t\t\t<dbo:EducationalInstitution dbp:name=\"" . $org->name . "\"/>\n");
                                fwrite($fh, "\t\t</dbo:school>\n");
                            }
                        }
                    }

                    fwrite($fh, "\t\t\t</foaf:Person>\n");
                    fwrite($fh, "\t\t</foaf:knows>\n");
                }

            }

            if (isset($nextPageToken)) {
                $list = $plus->people->listPeople('me', 'visible', $nextPageToken);
                $nextPageToken = (isset($list->nextPageToken)) ? $list->nextPageToken : null;
                $people = $list->getItems();
            } else {
                $stop = true;
            }
        } while ($stop == false);


        fwrite($fh, "\t</foaf:Person>\n");
        fwrite($fh, "</rdf:RDF>\n");

        fclose($fh);
    }

    function addYoutubeData($client){
        $youtube = new Google_Service_YouTube($client);
        $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
            'mine' => 'true',
        ));
//        dd($channelsResponse);
        $subscriptions = $youtube->subscriptions->listSubscriptions('snippet', array(
            'mine' => 'true',
        ));

//        dd($subscriptions->getItems()[0]->toSimpleObject());
//        $channel = new Google_Service_YouTube_Channel();
////        $channel->
    }
}
