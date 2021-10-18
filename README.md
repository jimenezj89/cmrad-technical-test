# Collective Minds Radiology technical test

## 🚀 How to
A *makefile* is provided, so:
* Start environment for first time:
```shell
$ make buid
```
* Execute tests:
```shell
$ make tests
```
* Clean up:
```shell
$ make clean
```
You can check all options:
```shell
$ make help
```

## 🤓 Assumptions
* There is one *customer repository* per customer.
* Customer repository is a Collective Minds Radiology "concept" and it should not be exposed to customers. 
* The customer repository identifier is provided as a request header param or it can be retrieved from authorization token(*JWT*).

## 📖 API documentation
API is documented using *OpenAPI specification 3*. It's an standard widely adopted
and supported by the most popular *API gateways/developer portals* like AWS and Kong. 
This documentation can be checked [here](http://127.0.0.1:8080).

This way of exposing the documentation should not be the way of doing it in a 
production environment.

## 👀 Take into account
* The action of adding a subject into a project results in a new "concept" I've called «enrollment».
* There is no authorization validation implemented.
* There is no repository identifier validation either(I assume the identifier is valid and the repository exists). This check could be done in a middleware.
* Integration tests covers almost 100%, and for not investing so much time, I've made the *CreateSubjectUseCase* unit test only 🙏.
* I'm not a fan of code annotations, code should be self-explanatory, but I've considered they are useful for
  this technical test 😇
 
## 💯 Improvements
* Validate the responses against the specification/documentation in action tests.
* Add some code quality tools like *phpstan*, *phpCodeSniffer*, ...
* ...and much more. 😅

