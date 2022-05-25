# General
There are currently 2 lumen applications running in the same code base:
* app
* conveyor_tcp

## DI container
Binding interfaces/contracts to implementation is done via:
`App\Providers\DIServiceProvider::register()`

# Applications

## conveyor_tcp

* provides TCP server on specified port using a native laravel `command` (https://laravel.com/docs/8.x/artisan#generating-commands)
```
php artisan conveyor-tcp:run $CONVEYOR_TCP_PORT
```
* the command source is in `App\Console\Commands\RunConveyorServer`
  * the class uses:
    * `App\Services\ConveyorBeltService` - to parse input message
    * `App\Services\BoxWeightService` - to store measured weight data via the **BoxWeight** model

## app

* **commands**
  * **AddRandomBoxWeights** command
    * `App\Console\Commands\AddRandomBoxWeights`
    * can be used for testing/development
    * adds 10 random BoxWeight models to DB
  * **SyncExpectedWeights** command
    * `App\Console\Commands\SyncExpectedWeights`
    * expects arguments:
      * `num` - number of expected weights to store in 1 run
    * calls remote api to get expected weights and stores them into `box_weight` table
  * **SendRealWeightToMagento** command
    * It calls remote api to put real weight, box UID and picker ID to Magento
* **services**
  * **BoxWeightService** class
    * `App\Services\BoxWeightService`
    * used for basic operations regarding box weights
    * class responsibilities:
      * reading expected box weight from DB
      * updating/adding real box weight in DB
  * **ConveyorBeltService** class
    * `App\Services\ConveyorBeltService`
    * used for working with incoming and outgoing TCP messages from and to conveyor belt
    * class responsibilities:
      * parsing and validation of RAV TCP message (incoming message from conveyor belt)
      * creation of ACK TCP message (outgoing response to conveyor belt)
  * **WeightToleranceService** class
    * `App\Services\WeightToleranceService`
    * used for working with weight tolerance
    * read value and type of the weight tolerance from .env file
    * configuration of the weight tolerance is stored in .env file
    * `APP_WEIGHT_TOLERANCE_TYPE` is `absolute` or `relative` value
    * `APP_WEIGHT_TOLERANCE_VALUE` is integer value of tolerance
  * **BoxTypeService** class
    * `App\Services\BoxTypeService`
    * used to retrieve box type from box UID
  * **BoxWeightToleranceService** class
    * `App\Services\BoxWeightToleranceService`
    * used to retrieve value of box weight tolerance for specific box UID and input weight as % 
    * the output value can then be added to weight tolerance
    * uses `app.box.weight_tolerance`
      * array of `box_type => box_weight_tolerance_in_grams`
* **API**
  * **Endpoint:** `Api/V1/picker`
  * **Method:** `PUT`
  * **Authorization:** `Bearer token`
  * **Content-Type:** `application/json`
  * **Payload:**
    * order_id: required, (int) value of Order ID
    * box_uid: required (int) value of Box UID
    * picker_id: required (int) Picker identification
  * **Response HTTP Status:**
    * 200: `Success` - all data saved
    * 401: `Unauthorised` - bad or missing authorization token
    * 422: `Unprocessable Entity` - bad or missing payload data
    * 500: `Internal server error` - failed to persist data to DB or different server error

    * 401: `Unauthorised` - bad or mising authorization token
    * 422: `Unprocessable Entity` - bad or mising payload data
    * 500: `Internal server error` - failed to persist data to DB or different server error
  * **ExpectedWeightService** class
    * `App\Services\ExpectedWeightService`
    * used to fetch new expected weights from remote api
    * class responsibilities:
      * fetching and returning of parsed expected weights

# Cron
* **SendRealWeightToMagento**
  * Synchronize real weight to Magento
  * It runs every three minutes

# Logs 
(follows native lumen logging https://laravel.com/docs/8.x/logging)
location: `storage/logs/lumen*.log`

# Tests
## PHPUnit

(follows native lumen testing https://lumen.laravel.com/docs/8.x/testing)

* configuration: `phpunit.xml`
* location `tests/*`
* running tests: enter app container and run
```
./vendor/bin/phpunit && php artisan migrate
```
**Please note**: some tests work with DB (those using `DatabaseMigrations` php trait, therefore DB is reset and again created from migrations
to ensure correct data.
