# multi-captcha

As the name says, this is one package to render them all! Multiple types of CAPTCHA in one package. Sometimes we just need to avoid spambots still want to keep the forms user-friendly and sometimes we need complete protection even if becomes a bit complex for users. MultiCaptcha helps you have uniform Captcha validation code over your projects.

## Key Features
1. 7 types of captcha supported with multitude of configuration options for each of them.
2. Shows refresh button (you need to provide refresh url).
3. Each Captcha challenge can be submitted only once.
4. Customizable error messages
5. You can customize the look and feel using *themeOptions* Or you can write your own theme/s.

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

Note: Make sure the cache directory is writable.

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
| *font*                | src/fonts/segoesc.ttf   | Optional | Path to the font file which will be used for creating the characters |


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
| *font*                | src/fonts/comic.ttf   | Optional | Path to the font file which will be used for creating the characters |


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

### Themes/customization
MultiCaptcha ships with a default theme named DefaultTheme. This theme supports customization of its color/style through various parameters nested under *themeOptions*.
e.g. you can change the background color to blue
```
$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'image' => array(
            'maxCodeLength' => 8,
            'font'=>'../src/types/image/comic.ttf',
            'width'=>180,
            'themeOptions' => [
                'containerStyle' => 'border:1px solid #0f702d; border-radius: 5px; padding: 5px; display: table; margin: 2px; background-color: #29713f; font-family: Helvetica; font-size: 14px; max-width: 180px;position:relative;',
                'fieldStyle' => 'background-color:#52785e; border:2px solid #fff; color:#fff; font-size:120%; font-weight:bold; border-radius:3px;width:144px;',
                'labelStyle' => 'font-size:80%; line-height:100%; color: #fff;',
            ]
        ),
        'ascii' => array(
            'maxCodeLength' => 8,
            'fonts'=>array(
                'banner'=> 4,
                'doom'=> 8,
                'small'=>'8'
            ),
            'themeOptions' => [
                'containerStyle' => 'border:1px solid #1e2a37; border-radius: 5px; padding: 10px; display: table; margin: 2px; background-color: #374c63; font-family: Helvetica; font-size: 14px; max-width: 180px;position:relative;',
                'fieldStyle' => 'background-color:#4d5d6f; border:2px solid #fff; color:#fff; font-size:120%; font-weight:bold; border-radius:3px;width:144px;',
                'labelStyle' => 'font-size:80%; line-height:100%; color: #fff;'
            ]
        ),
    ],
    'refreshUrl'=>'random.php?captcha=refresh',
    'helpUrl'=>'http://github.com/sameer-shelavale/multi-captcha',

] );
```
Note: each captcha type can have its own *theme* and *themeOptions*

#### themeOptions for DefaultTheme
| Configuration Param | Description | Default Value |
| ------------------- | ----------- | ------------- |
| *fieldClass*        | class for the input field  |
| *fieldStyle*        | css styles  for the input field  | background-color:#f66a03; border:2px solid #fff; font-size:120%; font-weight:bold; border-radius:3px;width:144px;
| *containerStyle*    | css styles  for the main container which contains all captcha elements (without the *style=* part and without enclosing quotes) | border:3px solid #000; border-radius: 5px; padding: 10px; display: table; margin: 2px; background-color: #f69d03; font-family: Arial; font-size: 14px; max-width: 180px;position:relative
| *questionImageStyle*| css styles for  the image code(for *image* or *gif* types ) | border-radius:3px; margin-bottom:5px;
| *questionTextStyle* | css styles for the question/challenge text(for *math* type)| font-size:120%; font-weight:bold;background-color:#ccc; border-radius:3px; padding:4px;margin-bottom:2px;text-align:center;display:block;min-width:172px;
| *questionAsciiStyle*| css styles for ASCII text(for the challenge text of *ascii* type) | background-color:#ccc; border-radius:3px; padding:4px;margin-bottom:2px;text-align:center;display:block;min-width:172px;
| *questionContainerStyle* | css styles for the container of the question/challenge | none ;
| *labelStyle*        | css style for label | font-size:80%; line-height:100%;
| *helpBtnClass*      | css class for the help butto | btn-help |
| *helpBtnText*       | text to show on the help button | ?
| *refreshBtnClass*   | css class for the refresh button | btn-refresh
| *refreshBtnText*    | text to show up on the refresh button  | &#8634;
| *extraHtml*         | extra css styles(with the enclosing style tag) | <style type="text/css"> a.btn-refresh, a.btn-help{ background-color:#fff; text-decoration:none; color:#f66a03; padding:1px 2px; border-radius:2px; vertical-align:top; margin-left:2px; display:inline-block; width:12px; height:12px; text-align:center; line-height:100%; font-size:12px; } </style>

With the above themeOptions you will be able to change the look and feel of the default theme.
However if you need to change the placements of the elements you will have to write your own theme by extending the DefaultTheme
Please refer example/theming.php for working example of *themeOptions*.


#### Extending the DefaultTheme
1. The render() and refresh() functions are crucial for rendering the captcha, your theme must have it.
2. When you extend the DefaultTheme you can use that theme by setting the *theme*
e.g.
```
$captcha = new \MultiCaptcha\Captcha([
    'secret'=>    "form1-secret-key",
    'options' =>  [
        'image' => array(
            'maxCodeLength' => 8,
            'font'=>'../src/types/image/comic.ttf',
            'width'=>180,
            'theme' => 'CustomTheme1'
        ),
        'ascii' => array(
            'maxCodeLength' => 8,
            'fonts'=>array(
                'banner'=> 4,
                'doom'=> 8,
                'small'=>'8'
            ),
            'theme' => 'CustomTheme2'
        ),
    ],
] );
```
Note: you can have your own *themeOptions* when you make your own theme. Also remember to update javascipt refresh function, it needs updating whenever you change the structure/layout.

### Cache
Multicaptcha uses file cache to record the answered captcha and uses it to block brute-force attack using single captcha and multiple answers.
It stores the unique id of captcha and expiration time in the record, and that record is kept till the captcha expires.
It uses file cache to store these records. To avoid the cache becoming too big the records are dispersed over multiple files.
The number of files to be used for caching is specified by a variable *$cacheSize*
and the directory in which the files should be stored is specified by *$cacheDir*, you can pass both of these variables as params to the constructor.
Default cache size is 10, but you can increase it for busy websites.
Note: The cache directory MUST be writable if you are using the default file cache implementation.

## Features under progress
1. Multi-language support

## Planned Features
1. Custom background image for image and gif captcha


## License
AGPL3.0, Free for non-commercial use.
Email me at samiirds@gmail.com for other type of licensing.