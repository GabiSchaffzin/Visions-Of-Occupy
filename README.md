Visions of Occupy
=======================

This project seeks to pair two pillars of the Occupy movement: the beliefs we have which inspire us to occupy and the occupations themselves. 

Using data collected this winter by the Occupy Research General Survey (administered by OccupyResearch), we take the answer to question 42—"In just a few words, what are you trying to achieve with your participation in the Occupy movement"—and pair it with a Flickr photo tagged with the camp name that the same respondent mentions. This means that while the photo displayed and quote may be completely unrelated \(both in source and in specific content\), viewers are presented with locational context and imagery. 

For more information on the survey and its results, please see [Occupy Research](http://occupyresearch.net/).

I've been extremely pleased with the reaction to the app to this point. I'm opening the code in the hopes that awesome things will come of it. The following instructions dictate how to install this app as I have it running on my server (i.e. pulling data from the database, querying Flickr, posting to Tumblr). I encourage you to rework it into something slightly or even completely different.

Package Contents
================
  
Data
----

In order to speed up the query of the survey data, I had to translate the [publicly available](http://www.occupyresearch.net/2012/03/23/preliminary-findings-occupy-research-demographic-and-political-participation-survey/) XLS and JSON files into a MySQL database on my server. While open-sourcing this app, I decided to simply export the data as a SQL query you can load into your MySQL database. This also means you'll need to update the database information in the config.php file.

PHP
---

The following PHP files are included with the package:

* **config.php**: Main configuration file. See below.
* **compile_visuals.php**: The file to call which will compile the DB query, Flickr search, and Tumblr post. On my server, I set up a cron job to run this every few hours \(at first it was three, then after a few months I switched it to 12\).
* **class.flickr.php**: The class which handles the Flickr search. This file was adapted from [PEAR::Flickr_API](http://code.iamcal.com/php/flickr/readme.htm) class written by Cal Henderson and requires PEAR installation \(see below\). There are some hiccups along the way here that I wasn't able to figure out. Sometimes, no results would be returned for no apparent reason \(sometimes it was because there were actually no results\). I didn't want to kill the server by looping until a result returned, so I limited it to five tries. Since the app runs on a cron job, I figured I had a good chance of retrieving data the next time around.
* **tumblr_xauth.php**: The class which handles the Tumblr post, adapted from Jacob Budin's [TumblrOAuth](https://github.com/jacobbudin/tumblroauth). *This file sends a Tumblr user's username and password without encryption, so please use it at your own risk.*
* **tumblroauth/**: \(directory\) The files used by TumblrOAuth.

Other
-----

The only other file in the package is log.txt, simply a placeholder for the log traces from the app. That way, I could check in on the progress as it ran via cron.

Installation  
============
  
Data
----

1. Create a new database for the ORGS result data.
2. Run the included **OccupyCOGSdata.sql** file as a SQL command.
3. Take note of the database hostname, name, username, and password. Copy those into **config.php**.

Flickr
------

1. Sign up for a Flickr API Key [here](http://www.flickr.com/services/apps/create/apply/).
2. Copy your API Key and API Secret Key from your Flickr Apps page \(http://www.flickr.com/services/apps/by/FLICKR\_USER\_NAME\) into **config.php**.
3. Install PEAR. To do so, I used the instructions [here](http://code.iamcal.com/php/flickr/readme.htm) under the **INSTALLATION** header. 
4. You'll need to get the absolute path to PEAR on your server. On mine, this was rather different from the URL to it. I'd do some Googling for your hosting package + absolute paths. Once you have it, copy it into **config.php**.

Tumblr
------

1. Sign up for a Tumblr API Key [here](http://www.tumblr.com/oauth/apps).
2. Once registered, click on "Request xAuth" under your app. You'll need to wait for approval before you can make calls using the hard-coded username and password. It did not take very much time to hear back from the Tumblr dev team.
3. Copy your API Key, API Secret, Tumblr blog name \(the format is NAME.tumblr.com\), Tumblr username, and Tumblr password into **config.php**.

That should cover it. Give it a try and let me know how it goes.

MIT License
===========

Copyright (c) 2012 Gabi Schaffzin

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.