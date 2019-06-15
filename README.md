# PHP SDK for Codequiry API 

Codequiry is a commercial grade plagiarism and similarity detection software for source code files. Submissions are checked with billions of sources on the web as well as checked locally against provided submissions. This is a NodeJS example application for the API to check code plagiarism and similarity.

The API allows us to run multiple different tests on source code files: 
1. Peer Check - Given a group of submissions as individual zip files, all lines of code are compared to each other and relative similarity scores are computed, as well as matched snippets. 
2. Database Check - Checks submissions against popular repositories and public sources of code.
3. Web Check - Does a full check of code with over 2 billion public sources on the web. 

Checks return us tons of data such as similarity scores, individual file scores, cluster graphs, similarity histograms, highlights results, matched snippets, percentage plagiarised and similar, and a ton more... 

Main Website: 
https://codequiry.com

Full API Docs:
https://codequiry.com/usage/api

## Installation
```
composer require "codequiry/codequiry-sdk-php:*"
```

#### Setting your API Key
```php
$codequiry = Codequiry{ApiKey: "YOUR_API_KEY"}
```
## Usage
#### Getting account information
```php
var accountInfo = cq.Account()
print(accountInfo)
```
#### Getting checks
```php
var checks = cq.Checks()
print(checks)
```
#### Creating checks (specify name and programming language)
Examples: java, c-cpp, python, csharp, txt
```php
var res = cq.CreateCheck("CheckNameHere", "java")
print(res)
```
#### Uploading to a check (specify check_id and file (must be a zip file)) 
```php
var res = cq.UploadFile("CHECK_ID", "./test.zip")
print(res)
```
#### Starting a check (specify check_id and if running database check or web check) 
```php
var res = cq.startCheck("CHECK_ID")
print(res)
```
#### Getting a check information/status
```php
var res = cq.getCheck("CHECK_ID", "./test.zip")
print(res)
```
#### Getting results overview
```php
var res = cq.getOverview("CHECK_ID", "./test.zip")
print(res)
```
#### Getting specific results of a submission
```php
var res = cq.getResults("CHECK_ID", "SUBMISSION_ID")
print(res)
```
## Realtime checking progress - SocketIO
This is an example of the listener, you can call this after getting a check status or after starting a check (both will reutrn a job ID, which you can listen to). Here we will listen to specific CHECK_ID.
```php
callback := func(data string) {
    print("Update: " + data)
}

cq.checkListen(1, callback)
```