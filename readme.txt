=== JobLookup Jobbox ===
Contributors: JobLookup
Tags: job, JobLookup, careers, employer, job board
Requires at least: 4.*
Tested up to: 6.7
Stable tag: 1.2.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

JobLookup Jobbox WordPress Plugin

== Description ==
Host the latest UK job listings on your site or blog with [JobLookup](https://joblookup.com/)'s Jobbox plugin. Choose from a range of sizes and styles that seamlessly integrate into your site, get set up in seconds, and start earning.

Register with [JobLookup's Publisher Program](https://joblookup.com/publisher), and earn every time your users click on job adverts in the jobbox.

Login to your [Joblookup Publisher Dashboard](https://joblookup.com/publisher/login) to keep track of your clicks and earnings. You can customise the size and look of your jobbox at any time on your WordPress dashboard. Learn more about [how our jobbox works](https://joblookup.com/publisher/info/jobbox), and what designs are currently available.

== Installation ==


1. Navigate to the Plugins menu and click Add New.


2. In the search field, type "JobLookup Jobbox" and click Search.


3. Once plugin appears in the search results click "Install Now" button.


4. When "Activate" button appears, click on it.


5. Register a [publisher account](https://joblookup.com/publisher/signup) on JobLookup, and and get your publisher ID from this [link](https://joblookup.com/publisher/jobbox#wordpress) once you've logged in to your account.


6. Now, go to the Widgets page of the Appearance section in WordPress and configure the JobLookup Jobbox widget.



== Frequently Asked Questions ==

= My WordPress Widget shows an error message. What does it mean? =


Depending on the error message, it can be related to one of the issues below:

**The selected publisher is invalid**: This error occurs when there's an invalid publisher ID parameter in the widget configuration. You need to make sure that you are using the correct Publisher ID, which you can find in your JobLookup publisher account under the ["Jobbox"](https://joblookup.com/publisher/jobbox#wordpress "Jobbox") menu.

**The selected channel is invalid**: This error occurs when there's invalid channel parameter in the widget configuration. You are only permitted to use channels which have been previously defined in your JobLookup publisher account.


= What can I change in the settings of the WordPress widget? =


If you are using a dark background, It's best to enable the light logo option. This will make the JobLookup logo appear more clearly.

Any Channel considered to be valid in JobLookup Jobbox plugin once you have defined it in your account.

By default, the widget looks for the 'keyword' variable in the URL to extract the user searched term, and will return the related jobs based on this value. It is the same behaviour for the area, and looks for the 'location' variable in the URL. You can overwrite these variable names by providing an alternative in the 'Keyword' and 'Area' variable names. As an example, if your URL structure follows the pattern below, you can overwrite the default variable names by setting the 'Keyword' variable name to 'mykey' and 'Area' variable name to 'myplace'.

**http://example.com/?mykey=KEYWORD_VALUE&myplace=LOCATION_VALUE**

= What are the prerequisites to install the JobLookup Jobbox plugin for WordPress? =


This plugin uses the WordPress widget for displaying the jobs list. Before using the plugin, make sure your current WordPress theme can handle widgets. If so, all you have to do is add the JobLookup Jobbox widget to your site.

== Screenshots ==
1. Configuration
2. JobLookup Jobbox Widget

== Changelog ==

= 1.0.3 =
* Pagination bug fixes

= 1.1.0 =
* Added the searchbox
* Added three types of pagination
* Added additional customisation options for the Jobbox

= 1.2.0 =
* Added country field to display jobs based on the user's location, either the UK or US
