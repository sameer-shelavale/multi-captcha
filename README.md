# multi-captcha


As the name says, one package to render them all! Multiple types of CAPTCHA in one package. Sometimes we just need to avoid spambots still want to keep the forms user-friendly and sometimes we need complete protection even if becomes a bit complex for users. MultiCaptcha helps you have uniform Captcha validation code over your projects.

This package currently supports following types of CAPTCHA

1. Gif - Gif animated captcha. Very hard on bots.
2. Image Code - User has to type some or all part of code displayed in the image.
3. HoneyPot - It simply adds an empty field asking users to leave it blank. Bots fill it up.
4. ASCII - It displays the CAPTCHA text as ASCII Art. User has to type some or all part of the code displayed.
5. Math - It asks simple mathematical question to user.
6. Recaptcha - Google Recaptcha

## Screenshots:


Captcha Type    |   Screenshot
-------------   |   -------------
image           |   ![Image Captcha Screenshot](/examples/image-captcha.gif)
gif             |   ![Gif Captcha Screenshot](/examples/gif-captcha.gif)
ascii           |   ![Ascii Captcha Screenshot](/examples/ascii-captcha.gif)
math            |   ![Math Captcha Screenshot](/examples/math-captcha.gif)
honeypot        |   ![Honeypot Captcha Screenshot](/examples/honeypot-captcha.gif)
recaptcha       |   ![Recaptcha Screenshot](/examples/recaptcha-captcha.gif)

## Installation:

### Using Composer

#### Command Line
You can install MultiCaptcha using Composer by doing

```
composer require sameer-shelavale/multi-captcha
```

#### composer.json
Alternatively, you can add it directly in your composer.json file in require block

```
{
    "require": {
        "sameer-shelavale/multi-captcha": "1.0.*"
    }
}
```

and then run

```
composer update
```

### PHP Include
Or you can download the zip/rar archive and extract it and copy the /src folder to appropriate location in your project folder.
And then include the captcha.php

```
include_once( 'PATH-TO-MULTI-CAPTCHA-FOLDER/src/Captcha.php' );
```

## Usage:

### Initialize:
The minimal code required to initialize the captcha looks like:
```php
$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "your-secret-key",
] );
```

The above code will initialize the captcha object to output "image" captcha which is default.
Note: Its important to set your own secret key, as its used to encrypt and decrypt the captcha fields.

And more customized code looks like:

```php
$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "your-secret-key",
    'life' => 2, //number of hours the generated captcha will be valid
    'customFieldName' => 'my_captcha', //this becomes the name of captcha answer field
    'options' =>  [
        'image' => [
            'maxCodeLength' => 8,
            'font'=>'../src/types/image/comic.ttf',
            'width'=>180
        ]
    ]
] );
```

Now, that we have basic knowledge of how to initialize it, lets look at the supported parameters in details,

### Supported Parameters
* *secret* - Your secret code for encryption and decryption of the form. It is recommended that you use different codes for each different web form.
* *life* - total number of hours the generated captcha will be valid. If you set it to 2, then after 2 hours the validate() function will return false even if you enter the correct code. Basically it means the user is expected to submit the form within these many hours after opening the form.
* *customName* - a custom name for the captcha answer field. If not provided it will use encrypted random name for the field.
* *options* - field contains the array with type of captcha/s that can be rendered as keys and their configurations as value array. If we pass more than one captcha type with it's configuration, it will randomly display one type of captcha from the supplied types. We will see configuration details of each type in details in next section.

### Options
Right now we can render 6 types of captcha namely *image*, *gif*, *ascii*, *math*, *honeypot* and *recaptcha*.
Now lets look in details at the supported configuration parameters for each of them.

Captcha Type | Configuration Param | Decription
------------ | ------------------- | ----------




