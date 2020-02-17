# Requirements: 

All what You need is git and proper docker setup with docker-compose installed. 

# How to run project: 

Please clone git repository of this small app. Once You do it go to project root directory.
From there please run `./build_project.sh` That command will do the job for You including:
- building all required containers
- configure environment (including apache etc.)
- install all dependencies
- create database and schema
- load fixtures

# Notes:

Please bear in mind that api is secured by ssl, so You are encouraged to use https instead of http
in API calls (it is adviced to use https because of security reasons and Personal data sent to our API).

This app is only a demo app. It does not contain complete functionality for real world app.
Skipping some part of the work is intentional. All prepared code should be in accordance to given task.
The services, repositories, listeners, exceptions, controllers etc. were prepared to show ability to code and
prepare right architecture. 

#room for improvements:
- use forms to process data and validate it
- add rest of methods to cover CRUD
- add OAuth2 or any other authentication for API
- move const into ENV (it might depends on env and also might change.. in that case the code itself should not be modified)
- add tests to improve code reliability
- cover more scenarios when calling API