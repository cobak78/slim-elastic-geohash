<?php

namespace Cobak78\Services;

use Cobak78\GeoHash\GeoHash;
use Elastica\Aggregation\GeoCentroid;
use Elastica\Aggregation\GeohashGrid;
use Elastica\Client;
use Elastica\Query;
use Elastica\Search;
use Elastica\Type\Mapping;
use Psr\Log\LoggerInterface;

class GeoService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Client
     */
    private $elastic;

    /**
     * @var GeoHash
     */
    private $geo;

    /**
     * GeoService constructor.
     * @param LoggerInterface $logger
     * @param Client $elastic
     * @param GeoHash $geo
     */
    public function __construct(LoggerInterface $logger, Client $elastic, GeoHash $geo)
    {
        $this->logger = $logger;
        $this->elastic = $elastic;
        $this->geo = $geo;
    }

    /**
     * @return array
     */
    public function doYerThingee()
    {
        // Sample log message
        $this->logger->info("Slim-Skeleton '/' route");

        /** @var \Elastica\Index $elasticaIndex */
        $elasticaIndex = $this->elastic->getIndex('twitter');

        // Create the index new
        $elasticaIndex->create(
            array(
                'number_of_shards' => 4,
                'number_of_replicas' => 1,
                'analysis' => array(
                    'analyzer' => array(
                        'default' => array(
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => array('lowercase', 'mySnowball')
                        ),
                        'default_search' => array(
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => array('standard', 'lowercase', 'mySnowball')
                        )
                    ),
                    'filter' => array(
                        'mySnowball' => array(
                            'type' => 'snowball',
                            'language' => 'German'
                        )
                    )
                )
            ),
            true
        );

        //Create a type
        $elasticaType = $elasticaIndex->getType('tweet');

// Define mapping
        $mapping = new Mapping();
        $mapping->setType($elasticaType);

// Set mapping
        $mapping->setProperties(array(
            'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'msg'     => array('type' => 'text', 'include_in_all' => TRUE),
            'location'=> array('type' => 'geo_point', 'include_in_all' => FALSE)
        ));

// Send mapping to type
        $mapping->send();

        $deals = [
            //      ['lat' => 39.3926176, 'lon' => 0.1693494],
            ['lat' => 41.3926176, 'lon' => 2.1693494],
            ['lat' => 41.3326176, 'lon' => 2.0693494],
            ['lat' => 41.5926176, 'lon' => 2.1693494],
            ['lat' => 41.4926176, 'lon' => 2.1693494],
            ['lat' => 41.5526176, 'lon' => 2.1953494],
            ['lat' => 41.5526176, 'lon' => 2.0953494],
            ['lat' => 41.5776176, 'lon' => 2.0953494],
            ['lat' => 41.4326176, 'lon' => 2.2693494],
            ['lat' => 41.4226176, 'lon' => 2.1493494],
            ['lat' => 41.4316176, 'lon' => 2.1093494],
            ['lat' => 41.4386176, 'lon' => 2.0993494],
            ['lat' => 41.4096176, 'lon' => 2.3693494],
            ['lat' => 41.3906176, 'lon' => 2.1593494],
            ['lat' => 41.3896176, 'lon' => 2.1493494],
        ];

        foreach ($deals as $key => $deal)
        {
            $tweet = array(
                'id'      => $key,
                'msg'     => $key . ' Ola k ase',
                'location'=> $deal['lat'] . ',' . $deal['lon']
            );
            // First parameter is the id of document.
            $tweetDocument = new \Elastica\Document($key, $tweet);

            // Add tweet to type
            $elasticaType->addDocument($tweetDocument);
        }

        // Refresh Index
        $elasticaType->getIndex()->refresh();

        //  [ top_left => [ lat => x, lon => y ], bottom_right => [ lat => x, lon => y ]
        $precision = $this->geo->getGeoHashPrecision(
            ["top_left" => ["lat" => 41.3326176, "lon" => 2.3693494], "bottom_right" => ["lat" => 41.4926176, "lon" => 2.0693494]], 4
//        ["top_left" => ["lat" => 41.3326176, "lon" => 2.3693494], "bottom_right" => ["lat" => 39.3926176, "lon" => 0.1693494]], 32
        );

        // Search

        $search = new Search($this->elastic);


        $query = new Query();

        $geoAgg = new GeohashGrid('geohash', 'location');

        $centroidAgg = new GeoCentroid('centroid', 'location');

        $geoAgg->addAggregation($centroidAgg);

        $geoAgg->setPrecision($precision);

        $query->addAggregation($geoAgg);


        $search->setQuery($query);

        $resultSet = $search->search();

        $aggregations = $resultSet->getAggregations();

        return [
            'deals' => $deals, 'groups' => $aggregations
        ];
    }
}
