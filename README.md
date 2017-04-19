# geoHash

This project is an example of using the geoHash library. It will get useful information of a bunch of distributed geopoints saved on an elasticSearch instance.

## Usage

You only need docker & docker-compose installed on your environment to get up the containers.


```
$ docker-compose up -d
$ docker exec fpm composer install
```

Navigate to your localhost (or docker-machine host) ip:80 to see the results. If have collition errors you can tweak the port forwarding in nginx service on docker-compose.yml
Feel free to play with provided data and included map. 



