# ZapMe (V2) PHP SDK

### What?

This is the ZapMe API PHP SDK (for the new version of ZapMe: 2.0). This was created specifically to the new API version 2.0 of the ZapMe application.


### How to Use?

The SDK is designed to be used by developers who want to integrate the ZapMe API into PHP systems. With the SDK you will be able to:

- Get User Data
- Send Messages
- Send Messages (with attachment)
- Show Message
- Get Messages
- Create Contacts
- Get Contacts
- Show Contact
- Destroy Contact

---

## Requirements:

- Web Server (Apache, NGINX, LiteSpeed)
- PHP >= 7.4
- ``cURL`` extension
- ``json`` extension

---

## Installation:

You have two ways to use the SDK, the first and easiest is via composer:

```bash
composer require zapmeteam/php-sdk
```

**The second way is by downloading the latest release here from the GitHub repository.** We encourage you to always use the package via Composer.

---

## Examples of Usage:

1. Preparing...

```php
<?php

require 'vendor/autoload.php';

use ZapMeSdk\Base as ZapMeSdk;

$zapMeSdk = (new ZapMeSdk())
            ->withApi('YOUR_API_HERE')
            ->withSecret('YOUR_SECRET_HERE');

# or ...

$zapMeSdk = (new ZapMeSdk())
            ->withCredentials([
                'api'    => 'YOUR_API_HERE',
                'secret' => 'YOUR_SECRET_HERE'            
            ]);
```

If for some reason you need to point the SDK to a different you can use the `toUrl` method:

```php

require 'vendor/autoload.php';

use ZapMeSdk\Base as ZapMeSdk;

$zapMeSdk = (new ZapMeSdk())
            ->toUrl('URL_HERE')
            ->withApi('YOUR_API_HERE')
            ->withSecret('YOUR_SECRET_HERE');

```

**Tip:** You can instantiate the class in a constructor, using an object, and then use the object for the entire class:

```php

class Invoice
{
    private ZapMeSdk $zapMeSdk;
    
    public function __construct()
    {
        $this->zapMeSdk = (new ZapMeSdk())
            ->withApi('YOUR_API_HERE')
            ->withSecret('YOUR_SECRET_HERE');
    }
    
    // ...
    
    public function createUserInvoice()
    {
        $this->zapMeSdk->sendMessage('5511985850505', 'Hello! Your invoice has been created.');
    }
}

```

---

# Available Methods:

### Get User Account Data

```php
$result = $zapMeSdk->accountStatus();

{
    "status": true,
    "result": "success",
    "date": "2022-08-01 01:57:50",
    "data": {
        "service": {
            "plan": "Plano Mensal",
            "duedate": "2022-08-30",
            "status": "active"
        },
        "auth": {
            "status": true,
            "authenticated_at": "2022-07-31 22:38:47"
        }
    }
}
```

### Send Message

```php
$result = $zapMeSdk->sendMessage('5511985850505', 'Hey!');

{
    "status": true,
    "result": "success",
    "date": "2022-08-01 01:57:50",
    "data": "message_sent"
}
```

### Send Message (With Attachment)

```php
$result = $zapMeSdk->sendMessage('5511985850505', 'Hey! Here is the image.', [
    'file_content'   => '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAE8Aq8DASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKz9Q13S9KkSPUNQt7Z3GVWWQLkfjWhXzf+0if+Ki0b/r2b/0KgD3P/hM/DX/QdsP+/wCtH/CZ+Gv+g7Yf9/1r430PwvrXiPzTpGnzXfk48zylztz0rVPwx8agE/8ACO33H/TOgD7FtNUsL9Q1pe284P8AzzkDfyq0DmvhmSDW/DV8FlW8026X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GaVqEF/Z6UY7mBw8bee5wR7ZruaKAEUYGKWiigArlvEfw98N+LL2O81mwNxPGnlq3msuFznHBFdTRQB57/wpLwF/0Bm/8CJP8aP+FJeAv+gM3/gRJ/jXoVFAHnv/AApLwF/0Bm/8CJP8a1NB+GnhbwxqQ1HSNONvdBCm/wA52+U9eCa66igAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigD/9k=',
    'file_extension' => 'jpg'
]);

{
    "status": true,
    "result": "success",
    "date": "2022-08-01 01:57:50",
    "data": "message_sent"
}
```

**Note:** The `file_content` must be a valid base64 encoded string.

### Show Message

```php
$result = $zapMeSdk->getMessage(50);

{
    "status": true,
    "result": "success",
    "date": "2022-07-31 23:18:29",
    "data": {
        "id": 50,
        "hash": "1c250c8d7338331921aa",
        "message": "Impedit quasi tempore rerum numquam harum atque iste. Et possimus commodi earum facere qui ratione quo. Sit iure aut cum ut explicabo qui perferendis. Sapiente aut soluta et aut aut. Incidunt consectetur nostrum ipsam velit distinctio sed placeat. Distinctio a reprehenderit et quia aliquam.",
        "phone": "9564490379555",
        "type": "api",
        "status": "missing_number",
        "created_at": "2022-07-31 23:12:16",
        "updated_at": "2022-07-31 23:12:16"
    }
}
```

### Get Messages

```php
$result = $zapMeSdk->getMessages();

{
    "status": true,
    "result": "success",
    "date": "2022-08-01 03:00:04",
    "data": [
        {
            "id": 1,
            "hash": "e763e2cc00485335b619",
            "message": "Inventore consequuntur incidunt occaecati dolorem. Nobis numquam voluptas necessitatibus ut deleniti. In aliquid quod iusto ipsam molestiae possimus maxime. Excepturi eveniet qui distinctio nihil quibusdam voluptate.",
            "phone": "9621891589608",
            "type": "api",
            "status": "missing_number",
            "created_at": "2022-08-01 02:12:16",
            "updated_at": "2022-08-01 02:12:16"
        },
        {
            "id": 2,
            "hash": "32a3b457ea993e4a0e23",
            "message": "Ex iure deserunt voluptas totam minus sit cum laboriosam. Sint aut hic delectus reiciendis aut reiciendis autem. Amet illum facilis earum molestiae. Odio soluta accusamus numquam ratione ipsam.",
            "phone": "7930790068646",
            "type": "api",
            "status": "missing_number",
            "created_at": "2022-08-01 02:12:16",
            "updated_at": "2022-08-01 02:12:16"
        },
    ]
}
```

**Note:** The maximum amount obtained will be 50. If you request a page that doesn't exist or doesn't have messages, the `data` will be returned as empty.

### Get Messages (Paginate)

```php
$result = $zapMeSdk->getMessages(true, 1, 2);

{
    "status": true,
    "result": "success",
    "date": "2022-08-23 19:44:23",
    "data": [
        {
            "id": 93025,
            "hash": "43dfa09aa74f52306bf5",
            "message": "Teste",
            "phone": "5511985850505",
            "type": "manual",
            "status": "message_sent",
            "created_at": "2022-08-09 12:17:39",
            "updated_at": "2022-08-09 12:17:39"
        },
        {
            "id": 93143,
            "hash": "14442631a35032d308a0",
            "message": "Olá!",
            "phone": "5511985850505",
            "type": "manual",
            "status": "message_sent",
            "created_at": "2022-08-09 12:18:56",
            "updated_at": "2022-08-09 12:18:56"
        }
    ]
}
```

### Create Contact

```php

$result = $zapMeSdk->createContact('John', '11985850505');

{
    "status": true,
    "result": "created",
    "date": "2022-07-10 19:17:05",
    "data": {
        "id": 5031,
        "name": "Jhon",
        "phone": "(11) 98585-0505",
        "group": null,
        "status": "active",
        "created_at": "2022-07-10 19:17:05",
        "updated_at": "2022-07-10 19:17:05"
    }
}

```

### Get Contacts

```php

$result = $zapMeSdk->getContacts();

{
    "status": true,
    "result": "success",
    "date": "2022-07-10 19:13:14",
    "data": [
        {
            "id": 32,
            "user_id": 1,
            "group_id": null,
            "name": "John",
            "phone": "(11) 98585-0505",
            "status": "active",
            "created_at": "2022-07-09 15:40:58",
            "updated_at": "2022-07-09 15:40:58"
        },
        {
            "id": 33,
            "user_id": 1,
            "group_id": null,
            "name": "Doe",
            "phone": "(11) 98686-0505",
            "status": "disable",
            "created_at": "2022-07-09 15:40:58",
            "updated_at": "2022-07-09 15:40:58"
        }
    ]
}

```

**Note:** The maximum amount obtained will be 50. Like `getMessages` you also can paginate the `getContacts`. If you request a page that doesn't exist or doesn't have contacts, the `data` will be returned as empty.

### Show Contact

```php

$result = $zapMeSdk->getContact(3520);

{
    "status": true,
    "result": "success",
    "date": "2022-07-10 19:34:39",
    "data": {
        "id": 3520,
        "name": "Jhon",
        "phone": "(11) 98585-0505",
        "group": {
            "id": 3,
            "name": "Teste",
            "color": "#5f76e8",
            "created_at": "2022-07-10 19:18:45",
            "updated_at": "2022-07-10 19:18:45"
        },
        "status": "active",
        "created_at": "2022-07-10 19:21:19",
        "updated_at": "2022-07-10 19:21:19"
    }
}

```

### Destroy Contact

```php

$result = $zapMeSdk->destroyContact(3520);

{
    "status": true,
    "result": "success",
    "date": "2022-07-10 20:04:50"
}

```

---

# Testing:

If you are a PHP developer and want to test the SDK or create new functionality by providing a PR, you can clone the repository, work on new code and run PHPUnit tests:

1. Clone the repository:

```bash
git clone git@github.com:zapmeteam/php-sdk.git
```

2. Install composer dependencies:

```bash
composer install
```

3. Preparing the `.env` used specifically to the PHPUnit tests:

```bash
cp .env.example .env
```

**After run this command** edit the `.env` if the API credentials of your account:

```
ZAPME_TEST_URL=http://api.zapme.com.br
ZAPME_TEST_API=YOUR_API_HERE
ZAPME_TEST_SECRET=YOUR_SECRET_HERE
```

4. Run the PHPUnit tests:

```bash
./vendor/bin/phpunit
```

---

# Issues:

Report any issue to the ZapMe support or feel free to suggest the correction by PR. Make sure to create the tests to validate your PR, if necessary.