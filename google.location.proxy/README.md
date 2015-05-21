# Proxy logger for the Firefox/Google location resolver

## What is this?

When a website in Firefox requests location information from the user, Firefox
can use various sources for resolving the user's geographical location.

One of these sources is the Google geolocation resolver. This tool expects a
list of MAC addresses and looks them up in a database of known devices – which,
as we know, have been [collected during Street View rides](http://www.theregister.co.uk/2010/04/22/google_streetview_logs_wlans/).

This means that Firefox makes a WiFi scan of your environment and sends the MAC
addresses of the found devices to Google and get a location object in return.

The **proxy.php** script will log the information transmitted between Firefox and
Google to a file of your choice.

## How to use this

Basically, this script works as a proxy between Firefox and Google. This means,
it will run on a dedicated webserver and intercept the communication.

Of course, certain conditions have to be met:

- Your device doesn't already provide the geolocation through a different method (e.g. GPS/Glonass).
- Your device has a working WiFi connection.
- Firefox is allowed to access the WiFi hardware of your device for making a scan.

Set this script up as follows:

1. Store the script on a PHP-enabled webserver of your choice.
2. Change the `$logFile` parameter to a file path of your choice.
3. Enter `about:config` in the Firefox location bar.
4. Search for `geo.wifi.url` in the config settings.
5. Double-click the URL in the *Value* column of the `geo.wifi.url` setting.
6. Replace the URL with the location of your script, for example `http://localhost/proxy.php`. Make sure to attach the `?key=%GOOGLE_API_KEY%` parameter.
7. Open a website which retrieves your geographical location.
8. After the website has received your location, look into the log file to see the transmitted data.

## Privacy considerations

So, what does that mean from a privacy point of view?

Firefox transmits a lot of personal data to Google: Not only the MAC adresses of
nearby WiFi access points, but also information such as your browser string and
a personal identifier (passed through the `key` parameter). You know what that
means, especially when this information is merged with other Google activities,
such as using their search, or even only visiting a website that has the `+1`
button embedded.

Google has set up a [policy](http://www.google.com/intl/en/privacy/lsf.html) that
governs the usage of this data. But, of course this is a typical Google privacy
statement, i.e. it is very broad and allows Google to do basically anything they
want with your information. (Disclaimer: I am not a lawyer, so if you feel the need for
a thorough analysis of Google's policies, you may want to consult one.)

## Background

The data transmitted between Firefox and Google looks like this (NB: `x`'s added for privacy reasons):

**Request**:

```
{
    "wifiAccessPoints": [
        {
            "macAddress": "c0-4a-00-xx-xx-xx",
            "signalStrength": 74
        },
        {
            "macAddress": "88-f7-c7-xx-xx-xx",
            "signalStrength": 60
        },
        {
            "macAddress": "00-24-fe-xx-xx-xx",
            "signalStrength": 32
        },
        {
            "macAddress": "58-23-8c-xx-xx-xx",
            "signalStrength": 29
        }
    ]
}
```

**Response**:
```
{
 "location": {
  "lat": 50.xxxxxxxxx,
  "lng": 6.xxxxxxxx
 },
 "accuracy": 51.0
}
```

## License

```
The MIT license

Copyright (c) 2015 Alex Günsche

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
```
