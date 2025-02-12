# Smart Emailing API v3
API wrapper for [Smart emailing](http://smartemailing.cz) API. Currenlty in development.

![img](https://img.shields.io/badge/PHPStan-8-blue)
![php](https://img.shields.io/badge/PHP-7.4%20to%208.2-B0B3D6)
![coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/pionl/acb2fd9298c1faa461c2a60b54b673ed/raw/coverage.json)
[![CI](https://github.com/pionl/smart-emailing-v3/actions/workflows/check.yml/badge.svg)](https://github.com/pionl/smart-emailing-v3/actions/workflows/check.yml)
[![Total Downloads](https://poser.pugx.org/pion/smart-emailing-v3/downloads?format=flat)](https://packagist.org/packages/pion/smart-emailing-v3)
[![Latest Stable Version](https://poser.pugx.org/pion/smart-emailing-v3/v/stable?format=flat)](https://packagist.org/packages/pion/smart-emailing-v3)
[![Latest Unstable Version](https://poser.pugx.org/pion/smart-emailing-v3/v/unstable?format=flat)](https://packagist.org/packages/pion/smart-emailing-v3)


* [Installation](#installation)
* [Usage](#usage)
* [Supports](#supports)
* [Advanced docs](#advanced-docs)
* [Contribution or overriding](#contribution-or-overriding)
* [Upgrading](#upgrading)
* [Copyright and License](#copyright-and-license)
* [Smart emailing API](https://app.smartemailing.cz/docs/api/v3/index.html)

## Installation

**Requirements**

This package requires PHP 7.4 and higher.

**Install via composer**

```
composer require pion/smart-emailing-v3
```

## Usage

Create an Api instance with your username and apiKey.

```php
use SmartEmailing\v3\Api;

...
$api = new Api('username', 'api-key');

```

then use the `$api` with desired method/component.

```php
// Creates a new instance
$api->importRequest()->addContact(new Contact('test@test.cz'))->send();
```

or

```php
// Creates a new instance
$import = $api->importRequest();
$contact = new Contact('test@test.cz');
$contact->setName('Martin')->setNameDay('2017-12-11 11:11:11');
$import->addContact($contact);

// Create new contact that will be inserted in the contact list
$contact2 = $import->newContact('test2@test.cz');
$contact2->setName('Test');

// Create new contact that will be inserted in the contact list
$import->newContact('test3@test.cz')->setName('Test');
$import->send();
```
### Error handling

When sending any request you can catch the error exception `RequestException`.

```php
use SmartEmailing\v3\Exceptions\RequestException;

try {
    $api->ping();
} catch (RequestException $exception) {
    $exception->response(); // to get the real response, will hold status and message (also data if provided)
    $exception->request(); // Can be null if the request was 200/201 but API returned error status text
}
```

## Supports

* [x] [Import contacts](https://app.smartemailing.cz/docs/api/v3/index.html#api-Import-Import_contacts)
* [x] [Import orders](https://app.smartemailing.cz/docs/api/v3/index.html#api-Import-Import_orders)
* [x] [Ping](https://app.smartemailing.cz/docs/api/v3/index.html#api-Tests-Aliveness_test)
* [x] [Credentials](https://app.smartemailing.cz/docs/api/v3/index.html#api-Tests-Login_test_with_GET)
* [x] [Contactlist](https://app.smartemailing.cz/docs/api/v3/index.html#api-Contactlists-Get_Contactlists)
* [ ] [Customfields](https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfields)
  * [x] [Customfields - create](https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfields)
  * [x] [Customfields - search / list](https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfields)
  * [ ] [Customfields - rest](https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfields)
  * [ ] [Customfields - options](https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfield_Options)
* [x] [Contacts](https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts)
* [ ] [Contacts in list](https://app.smartemailing.cz/docs/api/v3/index.html#api-Contacts_in_lists)
* [ ] [Custom emails](https://app.smartemailing.cz/docs/api/v3/index.html#api-Custom_emails)
* [x] [Emails](https://app.smartemailing.cz/docs/api/v3/index.html#api-Emails)
* [x] [Newsletter](https://app.smartemailing.cz/docs/api/v3/index.html#api-Newsletter)
* [ ] [Webhooks](https://app.smartemailing.cz/docs/api/v3/index.html#api-Webhooks)
* [x] (DEPRECATED) [E-shops](https://app.smartemailing.cz/docs/api/v3/index.html#api-E_shops)

## Advanced docs

## Import contacts

The import holds 2 main data points:
1. Settings `$import->settings()->setUpdate(true)`
2. Contacts `$import->newContact() : Contact`, `$import->contacts() : array` and `$import->addContact($contact) : self`

Example of usage is above.

### [Contact](./src/Models/Contact.php)

The import holds 3 main data points:
1. All data accessible via public properties. Fluent set method has basic validation and date convert logic
2. CustomFields `$contact->customFields()` for adding new fields
3. ContactLists `$contact->contactLists()` for adding new contact list

See source code for all methods/properties that you can use

#### [CustomFields](./src/Models/Holder/CustomFields.php) and [ContactLists](./src/Models/Holder/ContactLists.php)

Uses a data holder with `create`/`add`/`get`/`isEmpty`/`toArray`/`jsonSerialize` methods.

```php
$field = $contact->customFields()->create(12, 'test')
$list = $contact->contactLists()->create(12, 'confirmed')
```

## Import orders

The import holds 2 main data points:
1. Settings `$import->settings()->setSkipInvalidOrders(true)`
2. Orders `$import->newOrder() : Order`, `$import->orders() : array` and `$import->addOrder($order) : self`

Example of usage is above.

## CustomFields

The customFields uses a wrapper for each request related to custom-fields. To create a new instance call `$api->customFields()`.
On this object you can create any request that is currently implemented. See below.

### Create

Quick way that will create request with required customField

```php
use SmartEmailing\v3\Models\CustomFieldDefinition;

...
// Create the new customField and send the request now.
$customField = new CustomFieldDefinition('test', CustomFieldDefinition::TEXT);
$data = $api->customFields()->create($customField);

 // Get the customField in data
$customFieldId = $data->id;
```

or

```php
$request = $api->customFields()->createRequest(); // You can pass the customField object

// Setup customField
$customField = new CustomField();
$request->setCustomField($customField);

// Setup data
$customField->setType(CustomField::RADIO)->setName('test');

// Send the request
$response = $request->send();
$data = $response->data();
$customFieldId = $data->id;
```

### Search / List
[API DOCS](https://app.smartemailing.cz/docs/api/v3/index.html#api-Customfields-Get_Customfields)

Enables searching threw the custom fields with a filter/sort support. Results are limited by 100 per page. The response
returns meta data (MetaDataInterface) and an array of `Models\CustomFieldDefinition` by calling `$response->data()`.

#### Response

* data() returns an array `Models\CustomFieldDefinition`
* meta() returns a `stdClass` with properties (defined in `MetaDataInterface`)

#### Get a list without advanced search
Creates a search request and setups only `$page` or `$limit`. The full response from api with `customfield_options_url` or

```php
$data = $api->customFields()->list();

/** @var \SmartEmailing\v3\Models\CustomFieldDefinition $customField */
foreach ($data as $customField) {
    echo $customField->id;
    echo $customField->name;
    echo $customField->type;
}
```

#### Advanced search - filter/sort/etc

```php
$request = $api->customFields()->searchRequest(1);

// Search by name
$request->filter()->byName('test');
$request->sortBy('name');

// Send the request
$response = $request->send();
$data = $response->data();
```
##### Request methods

* Getters are via public property
    * page
    * limit
    * select
    * expand
    * sort
* Fluent Setters (with a validation) - more below.
* `filter()` returns a Filters setup - more below

###### expandBy(string : $expand)
Using this parameter, "customfield_options_url" property will be replaced by "customfield_options" contianing
expanded data. See examples below For more information see "/customfield-options" endpoint.

Allowed values: "customfield_options"

###### select(string : $select)
Comma separated list of properties to select. eg. "?select=id,name" If not provided, all fields are selected.

Allowed values: "id", "name", "type"

###### sortBy(string : $sort)
Comma separated list of sorting keys from left side. Prepend "-" to any key for desc direction, eg.
"?sort=type,-name"

Allowed values: "id", "name", "type"

###### setPage(int : $page)
Sets the current page

###### limit(int : $limit)
Sets the limit of result in single query

###### filter()
Allows filtering custom fields with multiple filter conditions.

* Getters are via public property
    * name
    * type
    * id
* Fluent Setters (with a validation)
    * byName($value)
    * byType($value)
    * byId($value)

### Get by name
Runs a search query with name filter and checks if the given name is found in customFields. Returns `false` or the `CustomFields\CustomField`.
Uses send logic (throws RequestException).

```php
// Can throw RequestException - uses send.
if ($customField = $api->customFields()->getByName('name')) {
    return $customField->id;
} else {
    throw new Exception('Not found!', 404);
}
```
## Send / Transactional emails
The implementation of API call ``send/transactional-emails-bulk``: https://app.smartemailing.cz/docs/api/v3/index.html#api-Custom_campaigns-Send_transactional_emails
### Full transactional email example
```php
$transactionEmail = $api->transactionalEmailsRequest();

$credentials = new SenderCredentials();
$credentials->setFrom('from@example.com');
$credentials->setReplyTo('to@example.com');
$credentials->setSenderName('Jean-Luc Picard');

$recipient = new Recipient();
$recipient->setEmailAddress('kirk@example.com');

$replace1 = new Replace();
$replace1->setKey('key1');
$replace1->setContent('content1');

$replace2 = new Replace();
$replace2->setKey('key2');
$replace2->setContent('content2');

$templateVariable = new TemplateVariable();
$templateVariable->setCustomData([
    'foo' => 'bar',
    'products' => [
        ['name' => 'prod1', 'desc' => 'desc1'],
        ['name' => 'prod1', 'desc' => 'desc2']
    ]
]);

$attachment1 = new Attachment();
$attachment1->setContentType('image/png');
$attachment1->setFileName('picture.png');
$attachment1->setDataBase64('data1');

$attachment2 = new Attachment();
$attachment2->setContentType('image/gif');
$attachment2->setFileName('sun.gif');
$attachment2->setDataBase64('data2');

$task = new Task();
$task->setRecipient($recipient);
$task->addReplace($replace1);
$task->addReplace($replace2);
$task->setTemplateVariables($templateVariable);
$task->addAttachment($attachment1);
$task->addAttachment($attachment2);

$messageContents = new MessageContents();
$messageContents->setTextBody('text_body');
$messageContents->setHtmlBody('html_body');
$messageContents->setSubject('subject');

$transactionEmail->setTag('tag_tag');
$transactionEmail->setEmailId(5);
$transactionEmail->setSenderCredentials($credentials);
$transactionEmail->addTask($task);
$transactionEmail->setMessageContents($messageContents);

$transactionEmail->send();
```

## Send / Bulk custom emails
The implementation of API call ``send/custom-emails-bulk``: https://app.smartemailing.cz/docs/api/v3/index.html#api-Custom_campaigns-Send_bulk_custom_emails
### Full custom email example
```php
$transactionEmail = $api->customEmailsBulkRequest();

$credentials = new SenderCredentials();
$credentials->setFrom('from@example.com');
$credentials->setReplyTo('to@example.com');
$credentials->setSenderName('Jean-Luc Picard');

$recipient = new Recipient();
$recipient->setEmailAddress('kirk@example.com');

$replace1 = new Replace();
$replace1->setKey('key1');
$replace1->setContent('content1');

$replace2 = new Replace();
$replace2->setKey('key2');
$replace2->setContent('content2');

$templateVariable = new TemplateVariable();
$templateVariable->setCustomData([
    'foo' => 'bar',
    'products' => [
        ['name' => 'prod1', 'desc' => 'desc1'],
        ['name' => 'prod1', 'desc' => 'desc2']
    ]
]);

$task = new Task();
$task->setRecipient($recipient);
$task->addReplace($replace1);
$task->addReplace($replace2);
$task->setTemplateVariables($templateVariable);

$transactionEmail->setTag('tag_tag');
$transactionEmail->setEmailId(5);
$transactionEmail->setSenderCredentials($credentials);
$transactionEmail->addTask($task);

$transactionEmail->send();
```
## Send / Bulk custom sms
The implementation of API call ``send/custom-sms-bulk``: https://app.smartemailing.cz/docs/api/v3/index.html#api-Custom_campaigns-Send_bulk_custom_SMS

### Full send sms example

```php
$bulkCustomSms = $api->customSmsBulkRequest();

$recipient = new Recipient();
$recipient->setEmailAddress('kirk@example.com');
$recipient->setCellphone('+420777888777');

$replace1 = new Replace();
$replace1->setKey('key1');
$replace1->setContent('content1');

$replace2 = new Replace();
$replace2->setKey('key2');
$replace2->setContent('content2');

$task = new Task();
$task->setRecipient($recipient);
$task->addReplace($replace1);
$task->addReplace($replace2);

$bulkCustomSms->setTag('tag_tag');
$bulkCustomSms->setSmsId(5);
$bulkCustomSms->addTask($task);

$bulkCustomSms->send();
```

## Upgrading
See [UPGRADE.md](UPGRADE.md) for how to upgrade to newer versions.


## Contribution or overriding
See [CONTRIBUTING.md](CONTRIBUTING.md) for how to contribute changes. All contributions are welcome.

## Copyright and License

[smart-emailing-v3](https://github.com/pionl/smart-emailing-v3)
was written by [Martin Kluska](http://kluska.cz) and is released under the
[MIT License](LICENSE.md).

Copyright (c) 2016 - 2022 Martin Kluska and contributors
