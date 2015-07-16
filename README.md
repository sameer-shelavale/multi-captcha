# multi-captcha

As the name says, this is one package to render them all! Multiple types of CAPTCHA in one package. Sometimes we just need to avoid spambots still want to keep the forms user-friendly and sometimes we need complete protection even if becomes a bit complex for users. MultiCaptcha helps you have uniform Captcha validation code over your projects.

## Supported types of CAPTCHA

Captcha Type    |   Screenshot  |  Description  |  Protection  | Difficulty for humans
-------------   |   ----------  |  -----------  |  --------------  | --------------
image           |   ![Image Captcha Screenshot](/examples/image-captcha.gif) | Asks user to identify all or some characters from the jpg image. [Live demo](http://demo.techrevol.com/multicaptcha/examples/image-captcha.php) | High | Average
gif             |   ![Gif Captcha Screenshot](/examples/gif-captcha.gif) | Asks user to identify all or some characters from the animated image. [Live demo](http://demo.techrevol.com/multicaptcha/examples/gif-captcha.php) | High | Average
ascii           |   ![Ascii Captcha Screenshot](/examples/ascii-captcha.gif) | Asks user to identify all or some characters from code displayed in ASCII art. [Live demo](http://demo.techrevol.com/multicaptcha/examples/ascii-captcha.php) | Moderate | Average
math            |   ![Math Captcha Screenshot](/examples/math-captcha.gif) | Asks user to solve simple mathematical expression. [Live demo](http://demo.techrevol.com/multicaptcha/examples/math-captcha.php) | Below Average  | Easy
honeypot        |   ![Honeypot Captcha Screenshot](/examples/honeypot-captcha.gif) | Asks user to leave the captcha field blank. [Live demo](http://demo.techrevol.com/multicaptcha/examples/honeypot-captcha.php) | Low | Very Easy
recaptcha       |   ![Recaptcha Screenshot](/examples/recaptcha-captcha.jpg) *(image is resized)* | Google ReCaptcha. Asks user to identify all characters displayed in the jpg image. [Live demo](http://demo.techrevol.com/multicaptcha/examples/recaptcha-captcha.php) | High | Average
nocaptcha       |   ![NoCaptcha Screenshot](/examples/nocaptcha-captcha.jpg) *(image is resized)* | Google NoCaptcha(Recaptcha v2.0). [Live demo](http://demo.techrevol.com/multicaptcha/examples/nocaptcha-captcha.php) | Moderate | Average

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

#### Supported Parameters
param | description
----- | -----------
*secret* | Your secret code for encryption and decryption of the form. It is recommended that you use different codes for each different web form.
*life* | total number of hours the generated captcha will be valid. If you set it to 2, then after 2 hours the validate() function will return false even if you enter the correct code. Basically it means the user is expected to submit the form within these many hours after opening the form.
*customFieldName* | a custom name for the captcha answer field. If not provided it will use encrypted random name for the field. *Note: Recaptcha type does not honor this parameter.*
*options* | field contains the array with type of captcha/s that can be rendered as keys and their configurations as value array. If we pass more than one captcha type with it's configuration, it will randomly display one type of captcha from the supplied types. We will see configuration details of each type in details in next section.
*refreshUrl* | (Optional) url from which we will GET the new captcha using AJAX. If not provided refresh button will not be displayed. Also note that this feature is useful mainly with image, gif, ascii and math captcha.
*helpUrl* | (Optional) url which will provide help related to the captcha type. This url will open in new tab/window.

#### Options
Right now we can render 7 types of captcha namely *image*, *gif*, *ascii*, *math*, *honeypot*, *recaptcha* and *nocaptcha*.
Now lets look in details at the supported configuration parameters for each of them.

##### Image Captcha
| Configuration Param | Default Value | Required | Description |
| ------------------- | ------------- | -------- | ---------- |
| *minCodeLength*       |     4       | Optional | Maximum length of code to be displayed in the image |
| *maxCodeLength*       |     8       | Optional | Maximum length of code to be displayed in the image |
| *maxRequired*         |     5       | Optional | Maximum number of characters it can ask the user to identify |
| *minRequired*         |     3       | Optional | Minimum number of characters it can ask the user to identify |
| *noiseLevel*          |     25      | Optional | Number of background noisy characters to be added as noise |
| *width*               |     150     | Optional | Width of the captcha image in pixels |
| *height*              |     40      | Optional | Height of the captcha image in pixels |
| *font*                | comic.ttf   | Optional | Path to the font file which will be used for creating the characters |


##### Gif Captcha(GIF Animated captcha)
| Configuration Param | Default Value | Required | Description |
| ------------------- | ------------- | -------- | ---------- |
| *totalFrames*         |     60      | Optional | Total number of frames to be produced. Please note having too many frames may overload server during heavy traffic |
| *delay*               |     5       | Optional | delay between frames in Millisecond
| *minCodeLength*       |     4       | Optional | Maximum length of code to be displayed in the image |
| *maxCodeLength*       |     8       | Optional | Maximum length of code to be displayed in the image |
| *maxRequired*         |     5       | Optional | Maximum number of characters it can ask the user to identify |
| *minRequired*         |     3       | Optional | Minimum number of characters it can ask the user to identify |
| *noiseLevel*          |     25      | Optional | Number of background noisy characters to be added as noise |
| *width*               |     150     | Optional | Width of the captcha image in pixels |
| *height*              |     40      | Optional | Height of the captcha image in pixels |
| *font*                | comic.ttf   | Optional | Path to the font file which will be used for creating the characters |


##### ASCII Captcha
| Configuration Param | Default Value | Required | Description |
| ------------------- | ------------- | -------- | ---------- |
| *fonts*               | comic.ttf   | Optional | array containing font name(without extension) as key and the size to be rendered in pixels as value. The figlet fonts vary in size when rendered so in order to control the height and width of the captcha we need to pass the font-size. For large fonts you can pass smaller value of font size. |
| *fontPath*            |     null    | Optional | If you want your own Figlet fonts you can specify path to the folder containing them here. |
| *minCodeLength*       |     4       | Optional | Maximum length of code to be displayed in the image |
| *maxCodeLength*       |     8       | Optional | Maximum length of code to be displayed in the image |
| *maxRequired*         |     5       | Optional | Maximum number of characters it can ask the user to identify |
| *minRequired*         |     3       | Optional | Minimum number of characters it can ask the user to identify |

ASCII font names:
You can find the currently supported ASCII figlet fonts in src/types/ascii/fonts/ folder and use the name of the font without the .flf extension.

**Example of ASCII captcha configuration:**

```php
$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "your-secret-key",
    'options' =>  [
        'ascii' => [
            'maxCodeLength' => 8,
            'fonts'=>array(
                'banner' => 4, //render with font size 4px or it becomes too big
                'doom' => 8, //render with font size 8px
                'small' =>'8' //render with font size 8px, "small" font is at src/types/ascii/fonts/small.flf
            )
        ]
    ]
] );
```

Note, the fonts parameter, it has the font name without extension as key. and the size in pixels as the value.
Unless you provide a *fontPath* parameter, it will look in src/types/ascii/fonts/ folder for that font.



##### Math Captcha(Simple Mathematical expression)
| Configuration Param | Default Value | Required | Description |
| ------------------- | ------------- | -------- | ---------- |
| *level*               |     4   | Optional | Number of variables(digits) in the mathematical expression |
| *description*         |   null  | Required | Some text asking the user to solve the mathematical expression. |




##### Honeypot Captcha
| Configuration Param | Default Value | Required | Description |
| ------------------- | ------------- | -------- | ---------- |
| *description*         |   null  | Required | Some text asking the user to leave the captcha field blank(bots will try to fill it up and get caught) |




##### Recaptcha
| Configuration Param | Default Value | Required | Description |
| ------------------- | ------------- | -------- | ---------- |
| *publicKey*           |   null  | Required | You Recaptcha Public key given to you by Google |
| *privateKey*          |   null  | Required | You Recaptcha Public key given to you by Google |

Note: You can register and get your recaptcha keys at
http://www.google.com/recaptcha




##### Nocaptcha
| Configuration Param | Default Value | Required | Description |
| ------------------- | ------------- | -------- | ---------- |
| *siteKey*           |   null  | Required | You Recaptcha Site key given to you by Google |
| *secretKey*         |   null  | Required | You Recaptcha Secret key given to you by Google |
| *lang*              |   en    | Optional | language code as on https://developers.google.com/recaptcha/docs/language |
Note: You can register and get your recaptcha keys at
http://www.google.com/recaptcha



##### You can generate random type of captcha from multiple configured types.
For example You can do:

```php
$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "your-secret-key",
    'options' =>  [
        'math' => array(
            'description'=> "Answer following question if you are human",
            'level' => 4
        ),
        'image' => array(
            'maxCodeLength' => 8,
            'font'=>'../src/types/image/comic.ttf',
            'width'=>180
        ),
        'ascii' => array(
            'maxCodeLength' => 8,
            'fonts'=>array(
                'banner'=> 4,
                'doom'=> 8,
                'small'=>'8'
            )
        ),
        'gif' => array(
            'maxCodeLength' => 8,
            'font'=>'../src/types/image/comic.ttf',
            'width'=>180,
            'height'=>60,
            'totalFrames'=>50,
            'delay'=>20
        )
    ]
] );
```

And then it will generate a random type of captcha from the 4 types which are configured

### Render

You can use the following one liner to render the captcha

```php
echo $captcha->render() ;
```

That will do it.(note that $captcha is the name of object you initialized)

### Refresh

To display the refresh button, its necessary to provide the refreshUrl.
Then in script of that url you can do.

```php
echo $captcha->refresh() ;
exit; //this is important to ensure no html is trailing the captcha
```
Note: make sure no html is displayed before or after the `captcha->refresh();`
You can also render and refresh at same page, please refer to the gif, ascii, image and math captcha examples.

### Validate
You can validate your form data simply by doing

```php
if( $captcha->validate( $_POST ){
    //do further processing, validate individual form fields
}
```

Note: We need to pass all the submitted data to the validate function because the name of captcha field is encrypted & random.
If you specify the customFieldName parameter it will require that field and the challenge field for validation.
E.g. if customFieldName = my_captcha, then you need to pass an array

```php
if( $captcha->validate( [ 'my_captcha'=>$_POST['my_captcha'], 'my_captcha_challenge'=>$_POST['my_captcha_challenge'] ] ){
    //do further processing, validate individual form fields
}
```
or
```php
if( $captcha->validate( array_intersect_key($_POST, array_flip(['my_captcha', 'my_captcha_challenge']) ) ) ){
    //do further processing, validate individual form fields
}
```

## Features under progress
1. Multi-language support

## Planned Features
1. Custom background image for image and gif captcha


## License
AGPL3.0, Free for non-commercial use.
Email me at samiirds@gmail.com for other type of licensing.