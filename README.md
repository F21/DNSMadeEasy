### PHP Library for DNSMadeEasy's API V2.0
----------------------------------------------------------------------------

This is a simple PHP library to work with DNSMadeEasy's v2.0 API. The operations are pretty low-level, although if required, I
hope the community can work together to build a version that abstracts away most of the low-level operations.

All operations for the v2.0 API is supported and implemented.

At the moment, my needs are pretty simple, so the low-level operations suits me just fine :smile:

I have taken inspiration from the [v1.2 library](https://github.com/a1extran/DnsMadeEasy) and most operations should pretty much
be drop-in, although some operations will need you to update your code a bit.

I have also done some testing and experimenting as DME's API documents are sorely lacking in certain areas and seems to be missing
some operations, so most of those operations have been implemented.

### Getting Started
----------------------------------------------------------------------------

#### Autoloading
Simply include `Autoloader.php` and initialise it:
```php
require_once 'path/to/DNSMadeEasy/Autoloader.php';
DNSMadeEasy\Autoloader::init();
```

#### Using the library
The operations are all contained in the `resource` classes. Look for them inside the `resource` folder.
If a body is available, the library tries to decode the JSON object, so you can access the object directly under the `body` property.

Here's a simple example:

```php
$client = new DNSMadeEasy\Client('myapikey', 'mysecretkey', true); //The last parameter says to use the sandbox
$result = $client->domains()->add('testdomain.com');

if($result->success){
  //yay!
  var_dump($result->statusCode);
  var_dump($result->body);
}else{
  //:(
  var_dump($result->errors);
}

$result2 = $client->domains()->update($result->body->id, array('gtdEnabled' => true)); //Enable global traffic direct for that domain

if($result2->success){
  //yay!
}else{
  //:(
}
```

#### Debugging
The library contains a nice debugger so that you can see the requests sent to the server and the corresponding response.

To enable:
```php
$client->debug(true);
```

The client will then produce some HTML output showing the request and the response. This feature is best used when testing
in a browser.

### Testing
----------------------------------------------------------------------------

<em>Note: I started writing tests for the resources class (classes that represents DME operations like domains, records etc), however,
I had lots of problems trying to get them to work against DME's sandbox. Mainly because things like adding and deleting domains would
take a long time for a unit test. Deleting domains would also not delete immediately, and I have deleted domains stuck in my 
account for more than a few days. I have stopped working on those tests for now, but contributions and pull request are certainly
much appreciated! I hope that someone can build a `fake_DME` like [fake_braintree](https://github.com/thoughtbot/fake_braintree), so
that it is easier to test the resources and also, so that we can run tests on Travis.</em>

To run the tests, you need to set some environment variables. On Linux, you can set them this way:

```shell
export APIKEY=my_api_key
export SECRETKEY=my_secret_key
```

Then, simply go into the `tests` folder and run `phpunit`:

```
cd tests
phpunit
```
