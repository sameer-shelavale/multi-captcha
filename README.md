multi-captcha
=============

Different types of CAPTCHA in one package. Sometimes we just need to avoid spambots and want to keep the forms user-friendly and sometimes we need complete protection even if becomes a bit complex for users.

This package currently supports following types of CAPTCHA

1. Gif - Gif animated captcha. Very hard on bots.
2. Image Code - User has to type some or all part of code displayed in the image.
3. HoneyPot - It simply adds an empty field asking users to leave it blank. Bots fill it up.
4. ASCII - It displays the CAPTCHA text as ASCII Art. User has to type some or all part of the code displayed.
5. Math - It asks simple mathematical question to user.
6. Recaptcha - Google Recaptcha

Screenshots:

Captcha Type    |   Screenshot
-------------   |   -------------
image           |   ![Image Captcha Screenshot](/examples/image-captcha.gif)
gif             |   ![Gif Captcha Screenshot](/examples/gif-captcha.gif)
ascii           |   ![Ascii Captcha Screenshot](/examples/ascii-captcha.gif)
math            |   ![Math Captcha Screenshot](/examples/math-captcha.gif)
honeypot        |   ![Honeypot Captcha Screenshot](/examples/honeypot-captcha.gif)
recaptcha       |   ![Recaptcha Screenshot](/examples/recaptcha-captcha.gif)
