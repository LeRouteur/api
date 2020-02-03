# API
Repository for the backend API (TIP in EPAI Fribourg).

# Objectives
This repository provides the files used to build the TIP website's API. The website is for a fictive enterprise named SecureConnect.

# Languages used / tools
* PHP with Slim3 micro-framework (REST architectural design)
* IntelliJ IDEA (Ultimate Edition)
* Apache2 Web Server
* Composer (dependency manager)

# Getting Started
Clone this repository and create a vHost in your preferred Web Server linking to this repo.
Add then 3 files in src/config, named :
* db.ini => DB connection credentials
* ldap.ini => LDAP connection credentials (used for AD)
* php_mail.ini => credentials for mail server
Please read the code to find which parameters you should put in the ini files.

You should then install Slim. Run this command in the root of the project :

```composer require slim/slim:^3.0```

# How Does It Work ?
When a user wants to register, the API will register it in the database and in a text file. This text file will contain the data for an LDAP insertion with a PowerShell script. I will provide this script in a future release.

# Notes
Please do not use this API in a production environment. I made it only for the TIP. While it's quite secure, it contains a few bugs and should not be used in production.
