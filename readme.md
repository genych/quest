__what:__

minimalistic but not bare bones php setup + survey demo app 

__how:__

`docker-compose up -d` — spawn a bunch of containers (wait for database to be pre-filled)

`docker-compose exec php composer test` — run unit tests and static analyzer

`https://localhost` — it's alive! (http is ok too)

GET http://localhost/stats — answer statistics

POST http://localhost/survey — submit survey

GET http://localhost/survey/1 — get survey info


__gotchas:__

browser will warn you about self signed certificate. it's ok.
also, it will expire one day. renew manually: https://letsencrypt.org/docs/certificates-for-localhost/

__todo/changelog:__

* ~~logic~~
* ~~fixtures~~
* ~~tests~~
* docs/openAPI
* CI
* more meaningfull and ordered tests
* get rid of mixed remnants
* profile
